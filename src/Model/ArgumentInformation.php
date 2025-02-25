<?php
namespace Clessic\Model;
use Clessic\Enum\ArgumentType;
use Clessic\Enum\ArgumentMode;

/**
 * 引数情報モデル
 */
class ArgumentInformation{
	/**
	 * @var array<string|int> $index 引数のインデックスの配列
	 */
	public $index;
    
	/**
	 * @var array<string|int> $extraIndex ロングオプション、ショートオプションを除外したインデックスの配列
	 */
	public $extraIndex;
	
	/**
	 * @var array<string> $long ロングオプションの配列
	 */
	public $long;
	
	/**
	 * @var array<string> $short ショートオプションの配列
	 */
	public $short;
	
	/**
	 * @var ArgumentType $type 引数の型
	 */
	public $type;
	
	/**
	 * @var ArgumentMode $mode 引数の取得モード
	 */
	public $mode;
	
	/**
	 * @var string $description 引数の説明文
	 */
	public $description;
	
	/**
	 * @var ?string $default 引数のデフォルト値
	 */
	public $default;
	
	/**
	 * @var string $param
	 */
	public $param;
	
	/**
	 * コンストラクタ
	 * @param array<string|ArgumentType|ArgumentMode> $info オプションの設定の配列
	 */
	public function __construct(string|ArgumentType|ArgumentMode ...$info){
		$this->index = [];
		$this->extraIndex = [];
		$this->long = [];
		$this->short = [];
		$this->type = ArgumentType::String;
		$this->mode = ArgumentMode::Single;
		$this->description = "";
		$this->default = null;
		$this->param = "";
		foreach($info as $item){
			if(is_string($item)){
				if(str_starts_with($item, "--")){
					$this->long[] = substr($item, 2);
				}elseif(str_starts_with($item, "-")){
					$this->short[] = substr($item, 1);
				}elseif($item == "%@*"){
					$this->extraIndex = ["*"];
					if(!$this->isMultiple()){
						$this->mode = ArgumentMode::Multiple;
						if($this->isBoolean()){
							$this->type = ArgumentType::String;
						}
					}
				}elseif($item == "%*"){
					$this->index = ["*"];
					if(!$this->isMultiple()){
						$this->mode = ArgumentMode::Multiple;
						if($this->isBoolean()){
							$this->type = ArgumentType::String;
						}
					}
				}elseif(str_starts_with($item, "%@")){
					$this->extraIndex[] = (int)substr($item, 2);
				}elseif(str_starts_with($item, "%")){
					$this->index[] = (int)substr($item, 1);
				}elseif(str_starts_with($item, "@")){
					$this->description = substr($item, 1);
				}elseif(ctype_alpha($item[0])){
					$this->default = $item;
				}
			}elseif($item instanceof ArgumentType){
				$this->type = $item;
				if($item->isBoolean()){
					$this->mode = ArgumentMode::Single;
				}
			}elseif($item instanceof ArgumentMode){
				$this->mode = $item;
				if($item->isMultiple()){
					if($this->isBoolean()){
						$this->type = ArgumentType::String;
					}
				}
			}
		}
	}
	
	/**
	 * 引数初期化時の値取得
	 * @return ?bool|array 初期化時の値
	 */
	public function getInitializeValue(): bool|array|null{
		if($this->isMultiple()){
			return [];
		}
		if($this->isBoolean()){
			return false;
		}
		return null;
	}
	
	/**
	 * 複数値判定関数
	 * @return bool 複数値である場合はtrue、それ以外はfalse
	 */
	public function isMultiple(): bool{
		return $this->mode->isMultiple();
	}
	
	
	/**
	 * 文字列引数判定関数
	 * @return bool 文字列引数である場合はtrue、それ以外はfalse
	 */
	public function isString(): bool{
		return $this->type->isString();
	}
	
	/**
	 * 整数引数判定関数
	 * @return bool 整数引数である場合はtrue、それ以外はfalse
	 */
	public function isInt(): bool{
		return $this->type->isInt();
	}
	
	/**
	 * 実数引数判定関数
	 * @return bool 実数引数である場合はtrue、それ以外はfalse
	 */
	public function isFloat(): bool{
		return $this->type->isFloat();
	}
	
	/**
	 * フラグ引数判定関数
	 * @return bool フラグ引数である場合はtrue、それ以外はfalse
	 */
	public function isBoolean(): bool{
		return $this->type->isBoolean();
	}
	
	/**
	 * ヘルプオプション判定関数
	 * @return bool ヘルプオプションである場合はtrue、それ以外はfalse
	 */
	public function isHelp(): bool{
		return $this->type->isHelp();
	}
}