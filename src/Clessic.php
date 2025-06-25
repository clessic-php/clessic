<?php
namespace Clessic;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Clessic\Enum\OutputMode;
use Clessic\Enum\AnsiEscCode;

/**
 * Clessicクラス
 * Clessicプロジェクトを定義するクラス
 *
 * @package Clessic
 */
class Clessic{
	/**
	 * @var array<string> $commandPaths コマンド検索パス
	 */
	private static $commandPaths = [];
	
	/**
	 * @var array<string> $viewPaths ビュー検索パス
	 */
	private static $viewPaths = [];
	
	/**
	 * @var array<string> $requestBodyParserPaths リクエストボディパーサー検索パス
	 */
	private static $requestBodyParserPaths = [];
	
	/**
	 * @var array<string> $arguments 起動時のコマンド
	 */
	public static $arguments = [];
	
	/**
	 * @var array<string, string> $requestHeaders 起動時のリクエストヘッダ
	 */
	public static $requestHeaders = [];
	
	/**
	 * @var ?string $requestBody 起動時のリクエストボディ
	 */
	public static $requestBody = null;
	
	/**
	 * @var OutputMode $outputMode 出力モード
	 */
	public static $outputMode = OutputMode::Browser;
	
	/**
	 * コマンド検索パスを追加する関数
	 * 
	 * @param string $paths 可変長引数リストとして渡されるパス
	 * @return void
	 */
	public static function addCommandPaths(string ...$paths): void{
		self::$commandPaths = array_merge(self::$commandPaths, $paths);
	}
	
	/**
	 * コマンド検索パスを取得する関数
	 * 
	 * @return array<string>
	 */
	public static function getCommandPaths(): array{
		return self::$commandPaths;
	}
	
	/**
	 * コマンドを検索して実行可能なクラスを返す関数
	 * 
	 * @param string $command コマンド名
	 * @return ?string 実行可能なファイル名、見つからない場合はnull
	 */
	public static function findCommand(string $command): ?string{
		$encoded = strtolower(str_replace("/", DIRECTORY_SEPARATOR, $command . ".php"));
		foreach(self::$commandPaths as $path){
			$info = new SplFileInfo($path);
			$realCommandPath = $info->getRealPath();
			if(!is_string($realCommandPath)){
				$realCommandPath = $info->getPathname();
			}
			$pos = strlen($realCommandPath) + 1;
			$rdi = new RecursiveDirectoryIterator(
				$path,
				FilesystemIterator::SKIP_DOTS |
				FilesystemIterator::KEY_AS_PATHNAME
			);
			$ri = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::LEAVES_ONLY);
			foreach($ri as $i){
				$pathName = $i->getPathname();
				$pathI = strtolower(substr($pathName, $pos));
				if($encoded == $pathI){
					return $pathName;
				}
			}
		}
		return null;
	}
	
	/**
	 * ビュー検索パスを追加する関数
	 * 
	 * @param string $paths 可変長引数リストとして渡されるパス
	 * @return void
	 */
	public static function addViewPaths(string ...$paths): void{
		self::$viewPaths = array_merge(self::$viewPaths, $paths);
	}
	
	/**
	 * ビューを検索して実行可能なクラスを返す関数
	 * 
	 * @param string $view ビュー名
	 * @return ?string 実行可能なファイル名、見つからない場合はnull
	 */
	public static function findView(string $view): ?string{
		$encoded = strtolower(str_replace("/", DIRECTORY_SEPARATOR, $view . ".php"));
		foreach(self::$viewPaths as $path){
			$info = new SplFileInfo($path);
			$realViewPath = $info->getRealPath();
			if(!is_string($realViewPath)){
				$realViewPath = $info->getPathname();
			}
			$pos = strlen($realViewPath) + 1;
			$rdi = new RecursiveDirectoryIterator(
				$path,
				FilesystemIterator::SKIP_DOTS |
				FilesystemIterator::KEY_AS_PATHNAME
			);
			$ri = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::LEAVES_ONLY);
			foreach($ri as $i){
				$pathName = $i->getPathname();
				$pathI = strtolower(substr($pathName, $pos));
				if($encoded == $pathI){
					return $pathName;
				}
			}
		}
		return null;
	}
	
	/**
	 * リクエストボディパーサー検索パスを追加する関数
	 * 
	 * @param string $paths 可変長引数リストとして渡されるパス
	 * @return void
	 */
	public static function addRequestBodyParserPaths(string ...$paths): void{
		self::$requestBodyParserPaths = array_merge(self::$requestBodyParserPaths, $paths);
	}
	
	/**
	 * ボディパーサーを検索して実行可能なファイル名を返す関数
	 * 
	 * @param string $type 引数を含まない小文字のMIMEタイプ
	 * @return ?string 実行可能なファイル名、見つからない場合はnull
	 */
	public static function findBodyParser(string $type): ?string{
		$bodyParser = str_replace("/", "-", $type) . ".php";
		foreach(self::$requestBodyParserPaths as $path){
			$pathName = $path . DIRECTORY_SEPARATOR . $bodyParser;
			if(file_exists($pathName)){
				return $pathName;
			}
		}
		return null;
	}
	
	/**
	 * ANSIエスケープコードを使用してテキストの色やスタイルを設定します。
	 * 
	 * @param AnsiEscCode $esc エスケープコード
	 * @param ?string $text エスケープコードを適用するテキスト。デフォルトはnull
	 * @return string 適用後のテキスト
	 */
	public static function AnsiEscape(AnsiEscCode $esc, ?string $text = null): string{
		if(is_null($text)){
			return (self::$outputMode)->isAnsi() ? $esc->value : "";
		}
		if($esc->isReset()){
			return (self::$outputMode)->isAnsi() ? ($esc->value . $text) : $text;
		}
		if((self::$outputMode)->isAnsi()){
			return $esc->value . $text . ($esc->getReset())->value;
		}
		return $text;
	}
}

// 初期値設定
if(php_sapi_name() == "cli"){
	Clessic::$arguments = array_slice($GLOBALS["argv"], 1);
	Clessic::$outputMode = OutputMode::CommandLineAnsi;
}else if(array_key_exists("HTTP_X_OUTPUTMODE", $_SERVER) && strtolower($_SERVER["HTTP_X_OUTPUTMODE"]) == "emulation"){
	Clessic::$arguments = array_map("urldecode", preg_split("/\\++/", trim($_SERVER["QUERY_STRING"] ?? "", "+")));
	Clessic::$outputMode = OutputMode::Emulation;
}else{
	Clessic::$arguments = array_map("urldecode", preg_split("/\\++/", trim($_SERVER["QUERY_STRING"] ?? "", "+")));
	Clessic::$outputMode = OutputMode::Browser;
}
foreach($_SERVER as $requestHeader => $requestHeaderValue){
	if($requestHeader == "CONTENT_TYPE"){
		Clessic::$requestHeaders["Content-Type"] = $requestHeaderValue;
	}else if($requestHeader == "CONTENT_LENGTH"){
		Clessic::$requestHeaders["Content-Length"] = $requestHeaderValue;
	}else if($requestHeader == "CONTENT_MD5"){
		Clessic::$requestHeaders["Content-Md5"] = $requestHeaderValue;
	}else if(!str_starts_with($requestHeader, "HTTP_")){
		continue;
	}
	Clessic::$requestHeaders[substr(preg_replace_callback("/_?./", fn($matches) => (str_starts_with($matches[0], "_") ? ("-" . substr($matches[0], 1)) : strtolower($matches[0])), $requestHeader), 5)] = $requestHeaderValue;
}
if((array_key_exists("REQUEST_METHOD", $_SERVER)) && $_SERVER["REQUEST_METHOD"] != "GET"){
	Clessic::$requestBody = file_get_contents((Clessic::$outputMode)->isCommandLine() ? "php://stdin" : "php://input");
}
