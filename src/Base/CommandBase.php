<?php
namespace Clessic\Base;
use Clessic\Enum\ReturnCode;
use Clessic\Enum\ArgumentType;
use Clessic\Enum\ArgumentMode;

/**
 * コマンドを定義するための抽象クラス
 *
 * @package Clessic
 */
abstract class CommandBase{
	/**
	 * @var string $package コマンドの分類/パッケージ
	 */
	public static $package;
	
	/**
	 * @var string $description コマンドの説明文。ヘルプコマンドで出力される
	 */
	public static $description;
	
	/**
	 * @var string $version コマンドバージョン。v[メジャーバージョン].[マイナーバージョン].[パッチバージョン]-[開発ステージ（開発/試用/安定）]
	 */
	public static $version;
	
	/**
	 * @var array<string, array<string|ArgumentType|ArgumentMode>> $options コマンドのオプション設定。
	 * - キー キーで指定した値がrun()引数の$argsのキーとなる連想配列で、オプションを取得できる。
	 * - 値
	 *   - ArgumentType 引数の型（例: Clessic\Enum\ArgumentType::String）
	 *   - ArgumentMode 引数のモード（例: Clessic\Enum\ArgumentMode::Single）
	 *   - string 短いオプション名 "-"から始まる文字列 （例: -h）
	 *   - string 長いオプション名 "--"から始まる文字列 （例: --help）
	 *   - string 任意のインデックス番号の引数 "%"とインデックス番号を結合した文字列 （例: %1）
	 *   - string すべての引数 "%*"
	 *   - string 短いオプション名と長いオプション名を除外した任意のインデックス番号の引数 "%@"とインデックス番号を結合した文字列 （例: %@1）
	 *   - string 短いオプション名と長いオプション名を除外した引数 "%@*"
	 *   - string オプションの説明文。ヘルプコマンドで出力される "@"から始まる文字列 （例: @ヘルプを表示するためのフラグ）
	 *   - string デフォルト値 serialize()でシリアライズされた値
	 */
	public static $options;
	
	/**
	 * 実行メソッド
	 * 具象クラスで実装されるべき
	 *
	 * @param array<mixed> $args
	 * @param mixed $body
	 * @param array<string, string> $headers
	 * @return int|ReturnCode
	 */
	abstract public static function run(array $args, mixed $body, array $headers): int|ReturnCode;
}
