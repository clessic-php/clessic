<?php
namespace Clessic\Enum;

/**
 * 出力モードを定義する列挙型
 */
enum OutputMode: int{
	case Browser = 0;
	case CommandLine = 1;
	case CommandLineAnsi = 2;
	case Emulation = 3;
	
	/**
	 * コマンドライン判定関数
	 * @return bool コマンドライン出力である場合はtrue、それ以外はfalse
	 */
	public function isCommandLine(): bool{
		return ($this === self::CommandLine) || ($this === self::CommandLineAnsi);
	}
	
	/**
	 * ANSI判定関数
	 * @return bool ANSI出力である場合はtrue、それ以外はfalse
	 */
	public function isAnsi(): bool{
		return ($this === self::CommandLineAnsi) || ($this === self::Emulation);
	}
	
	/**
	 * ブラウザ判定関数
	 * @return bool ブラウザ出力である場合はtrue、それ以外はfalse
	 */
	public function isBrowser(): bool{
		return $this === self::Browser;
	}
}
