<?php
namespace Clessic;
use Clessic\Enum\ArgumentType;
use Clessic\Enum\ArgumentMode;
use Clessic\Enum\ReturnCode;
use Clessic\Enum\AnsiEscCode;
use Clessic\Model\ArgumentInformation;

/**
 * コマンドライン引数を解析するクラス
 *
 * @package Clessic
 */
class Command{
	/**
	 * @var array<string> $args コマンドライン引数の配列
	 */
	protected $args;
	
	/**
	 * @var bool $web Webモードフラグ
	 */
	protected $web;
	
	/**
	 * @var bool $help ヘルプフラグ
	 */
	protected $help;
	
	/**
	 * @var ?string $body リクエストボディ
	 */
	protected $body;
	
	/**
	 * @var ?array<string, string> $headers ヘルプフラグ
	 */
	protected $headers;
	
	
	/**
	 * @var string デフォルトのコマンド
	 */
	protected static $defaultCommand = "index";
	
	/**
	 * コンストラクタ
	 *
	 * @param array<string> $args コマンドライン引数の配列
	 */
	public function __construct(array $args){
		$this->args = $args;
		$this->web = false;
		$this->help = false;
		$this->body = null;
		$this->headers = null;
		if((count($this->args) == 0) || (reset($this->args) == "")){
			$this->args = [static::$defaultCommand];
		}
		$command = reset($this->args);
		if(str_starts_with($command, "-")){
			array_unshift($this->args, $command = static::$defaultCommand);
		}
		if(str_contains($command, ":")){
			$this->web = true;
		}
	}
	
	/**
	 * コマンドライン引数を解析して取得する
	 *
	 * @param array<string, ArgumentInformation> $opts 許可されるオプションの設定
	 * @return ?array<string, string|int|float|bool|array<string>|array<int>|array<float>|null> 解析結果の連想配列
	 */
	protected function getArguments(array $opts): ?array{
		$res = [];
		$n = count($this->args);
		$longOpts = [];
		$shortOpts = [];
		$indexOpts = [];
		$extraIndexOpts = [];
		$x = 0;
		foreach($opts as $k => $opt){
			$res[$k] = $opt->getInitializeValue();
			foreach($opt->long as $value){
				$longOpts[$value] = $k;
			}
			foreach($opt->short as $value){
				$shortOpts[$value] = $k;
			}
			foreach($opt->index as $value){
				if(!array_key_exists($value, $indexOpts)){
					$indexOpts[$value] = [];
				}
				$indexOpts[$value][] = $k;
			}
			foreach($opt->extraIndex as $value){
				if(!array_key_exists($value, $extraIndexOpts)){
					$extraIndexOpts[$value] = [];
				}
				$extraIndexOpts[$value][] = $k;
			}
		}
		for($p = 0; $p < $n; $p++){
			foreach(["*", $p] as $idx){
				if(($idx == "*") && ($p == 0)){
					continue;
				}
				if(array_key_exists($idx, $indexOpts)){
					foreach($indexOpts[$idx] as $k){
						$opt = $opts[$k];
						$value = $this->getArgumentAt($p, $opt->type);
						if($opt->isMultiple()){
							array_push($res[$k], $value);
						}else{
							$res[$k] = $value;
						}
					}
				}
			}
			$arg = $this->getArgumentAt($p, ArgumentType::String);
			if(str_starts_with($arg, "--")){
				$pos = strpos($arg, "=");
				if(is_int($pos)){
					$optKey = substr($arg, 2, $pos - 2);
					if(!array_key_exists($optKey, $longOpts)){
						continue;
					}
					$k = $longOpts[$optKey];
					$opt = $opts[$k];
					$value = substr($arg, $pos + 1);
					if($opt->isString()){
					}elseif($opt->isInt()){
						$value = (int)$value;
					}elseif($opt->isFloat()){
						$value = (float)$value;
					}else{
						if($opt->isHelp()){
							$this->help = true;
						}
						$value = null;
					}
					if($opt->isMultiple()){
						array_push($res[$k], $value);
					}else{
						$res[$k] = $value;
					}
				}else{
					$optKey = substr($arg, 2);
					if(!array_key_exists($optKey, $longOpts)){
						continue;
					}
					$k = $longOpts[$optKey];
					$opt = $opts[$k];
					if($opt->isBoolean()){
						$res[$k] = true;
					}elseif($opt->isHelp()){
						$this->help = true;
					}
				}
			}elseif(str_starts_with($arg, "-")){
				$optKey = substr($arg, 1);
				if(!array_key_exists($optKey, $shortOpts)){
					continue;
				}
				$k = $shortOpts[$optKey];
				$opt = $opts[$k];
				if($opt->isBoolean()){
					$res[$k] = true;
				}elseif($opt->isHelp()){
					$this->help = true;
				}else{
					$p++;
					if($p < $n){
						foreach(["*", $p] as $idx){
							if(($idx == "*") && ($p == 0)){
								continue;
							}
							if(array_key_exists($idx, $indexOpts)){
								foreach($indexOpts[$idx] as $k2){
									$opt2 = $opts[$k2];
									$value = $this->getArgumentAt($p, $opt2->type);
									if($opt2->isMultiple()){
										array_push($res[$k2], $value);
									}else{
										$res[$k2] = $value;
									}
								}
							}
						}
					}
					$value = $this->getArgumentAt($p, $opt->type);
					if($opt->isMultiple()){
						array_push($res[$k], $value);
					}else{
						$res[$k] = $value;
					}
				}
			}else{
				foreach(["*", $x] as $idx){
					if(($idx == "*") && ($x == 0)){
						continue;
					}
					if(array_key_exists($idx, $extraIndexOpts)){
						foreach($extraIndexOpts[$idx] as $k){
							$opt = $opts[$k];
							$value = $this->getArgumentAt($p, $opt->type);
							if($opt->isMultiple()){
								array_push($res[$k], $value);
							}else{
								$res[$k] = $value;
							}
						}
					}
				}
				$x++;
			}
		}
		if($this->help){
			return null;
		}
		foreach($res as $k => $v){
			$opt = $opts[$k];
			if($opt->isHelp()){
				unset($res[$k]);
				continue;
			}
			if(is_null($v) && (!is_null($opt->default))){
				$res[$k] = unserialize($opt->default);
				continue;
			}
			if(is_array($v) && empty($v) && (!is_null($opt->default))){
				$res[$k] = unserialize($opt->default);
				continue;
			}
		}
		return $res;
	}
	
	/**
	 * 指定したインデックスの引数を取得する
	 *
	 * @param int $index 取得するインデックス
	 * @param ArgumentType $type 期待する引数の型
	 * @return mixed 取得した引数の値
	 */
	protected function getArgumentAt(int $index, ArgumentType $type): mixed{
		$args = $this->args;
		if(count($args) <= $index){
			return null;
		}
		$value = $args[$index];
		if($type->isString()){
			return $value;
		}
		if($type->isInt()){
			return (int)$value;
		}
		if($type->isFloat()){
			return (float)$value;
		}
		if($type->isBoolean()){
			return true;
		}
		if($type->isHelp()){
			$this->help = true;
		}
		return null;
	}
	
	/**
	 * ヘルプが指定されたかどうかを判定する
	 *
	 * @return bool ヘルプフラグの状態
	 */
	public function hasHelp(): bool{
		return $this->help;
	}
	
	/**
	 * ヘルプメッセージを生成する
	 *
	 * @param string $pre ヘルプの前に追加するプレフィックス
	 * @param array<string, ArgumentInformation> $options 設定されているオプション
	 * @return string ヘルプメッセージ
	 */
	protected static function getHelp(string $pre, array $options): string{
		if((Clessic::$outputMode)->isBrowser()){
			header("Content-Type: text/plain");
		}
		$table = [$pre];
		$paramSize = 0;
		$typeSize = 7;
		$modeSize = 8;
		foreach($options as &$opt){
			$tokens = [];
			if(!empty($opt->index)){
				$tokens[] = "%" . implode(", %", $opt->index);
			}
			if(!empty($opt->extraIndex)){
				$tokens[] = "%@" . implode(", %@", $opt->extraIndex);
			}
			if(!empty($opt->short)){
				$tokens[] = "-" . implode(", -", $opt->short);
			}
			if(!empty($opt->long)){
				$tokens[] = "--" . implode(", --", $opt->long);
			}
			$opt->param = implode(", ", $tokens);
			$len = strlen($opt->param);
			if($len > $paramSize){
				$paramSize = $len;
			}
		}
		$descriptionDelimiter = "\n" . str_repeat(" ", $paramSize + $typeSize + $modeSize + 3);
		foreach($options as &$opt){
			$line = Clessic::AnsiEscape(AnsiEscCode::ResetAll);
			$line .= Clessic::AnsiEscape(AnsiEscCode::Bold, str_pad($opt->param, $paramSize, " ", STR_PAD_RIGHT)) . " ";
			if($opt->isString()){
				$line .= Clessic::AnsiEscape(AnsiEscCode::TextYellow, str_pad("string", $typeSize, " ", STR_PAD_RIGHT)) . " ";
			}elseif($opt->isInt()){
				$line .= Clessic::AnsiEscape(AnsiEscCode::TextYellow, str_pad("int", $typeSize, " ", STR_PAD_RIGHT)) . " ";
			}elseif($opt->isFloat()){
				$line .= Clessic::AnsiEscape(AnsiEscCode::TextYellow, str_pad("float", $typeSize, " ", STR_PAD_RIGHT)) . " ";
			}elseif($opt->isBoolean()){
				$line .= Clessic::AnsiEscape(AnsiEscCode::TextYellow, str_pad("boolean", $typeSize, " ", STR_PAD_RIGHT)) . " ";
			}else{
				$line .= str_pad("", $typeSize, " ", STR_PAD_RIGHT) . " ";
			}
			if($opt->isMultiple()){
				$line .= Clessic::AnsiEscape(AnsiEscCode::TextGreen, str_pad("multiple", $modeSize, " ", STR_PAD_RIGHT)) . " ";
			}else{
				$line .= Clessic::AnsiEscape(AnsiEscCode::TextGreen, str_pad("single", $modeSize, " ", STR_PAD_RIGHT)) . " ";
			}
			$line .= Clessic::AnsiEscape(AnsiEscCode::TextCyan, str_replace("\n", $descriptionDelimiter, $opt->description));
			$table[] = $line;
		}
		return implode("\n", $table);
	}
	
	/**
	 * コマンドを実行する
	 *
	 * @param string $proxy コマンドファイルの検索を行わずに代替で実行するクラス
	 * @return int|ReturnCode 実行結果のエラーコード
	 */
	public function execute(?string $proxy = null): int|ReturnCode{
		if($this->web){
			$uri = reset($this->args);
			if(!(Clessic::$outputMode)->isBrowser()){
				echo Clessic::AnsiEscape(AnsiEscCode::Underline, $uri);
			}else{
				header("Location: {$uri}");
			}
			return ReturnCode::Success;
		}
		if(!is_null($proxy)){
			$opts = [];
			foreach($proxy::$options as $k => $columns){
				$opts[$k] = new ArgumentInformation(...$columns);
			}
			$args = $this->getArguments($opts);
			if($this->help){
				echo static::getHelp($proxy::$description, $opts);
				return ReturnCode::Success;
			}
			["body" => $body, "headers" => $headers] = $this->getExecuteParameters();
			return $proxy::run($args, $body, $headers);
		}
		$found = Clessic::findCommand(reset($this->args));
		if(is_string($found)){
			$ctor = require($found);
			$opts = [];
			foreach($ctor::$options as $k => $columns){
				$opts[$k] = new ArgumentInformation(...$columns);
			}
			$args = $this->getArguments($opts);
			if($this->help){
				echo static::getHelp($ctor::$description, $opts);
				return ReturnCode::Success;
			}
			["body" => $body, "headers" => $headers] = $this->getExecuteParameters();
			return $ctor::run($args, $body, $headers);
		}
		return ReturnCode::NotFound;
	}
	
	/**
	 * コマンドを実行パラメータを取得する
	 *
	 * @return array{body: mixed, headers: array<string, string>}
	 */
	function getExecuteParameters(): array{
		$body = $this->body ?? Clessic::$requestBody;
		$headers = $this->headers ?? Clessic::$requestHeaders;
		if((!is_null($body)) && array_key_exists("Content-Type", $headers)){
			$parts = explode(";", trim($headers["Content-Type"], " \n\r\t\v\x00;"));
			$type = strtolower(trim(array_shift($parts)));
			$found = Clessic::findBodyParser($type);
			if(is_string($found)){
				$params = [];
				foreach($parts as $param){
					$pos = strpos($param, "=");
					if(is_int($pos)){
						$key = strtolower(trim(substr($param, 0, $pos)));
						$value = trim(substr($param, $pos + 1));
						$params[$key] = $value;
					}else{
						$key = strtolower(trim($param));
						$params[$key] = true;
					}
				}
				$parser = require($found);
				$body = $parser($body, $params);
			}
		}
		return ["body" => $body, "headers" => $headers];
	}
}