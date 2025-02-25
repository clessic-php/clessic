<?php
namespace Clessic\Enum;

/**
 * 引数の型を定義する列挙型
 */
enum ArgumentType: int{
	case String = 0;
	case Int = 1;
	case Float = 2;
	case Boolean = 3;
	case Help = 4;
	
	/**
	 * 文字列引数判定関数
	 * @return bool 文字列引数である場合はtrue、それ以外はfalse
	 */
	public function isString(): bool{
		return $this === self::String;
	}
	
	/**
	 * 整数引数判定関数
	 * @return bool 整数引数である場合はtrue、それ以外はfalse
	 */
	public function isInt(): bool{
		return $this === self::Int;
	}
	
	/**
	 * 実数引数判定関数
	 * @return bool 実数引数である場合はtrue、それ以外はfalse
	 */
	public function isFloat(): bool{
		return $this === self::Float;
	}
	
	/**
	 * フラグ引数判定関数
	 * @return bool フラグ引数である場合はtrue、それ以外はfalse
	 */
	public function isBoolean(): bool{
		return $this === self::Boolean;
	}
	
	/**
	 * ヘルプオプション判定関数
	 * @return bool ヘルプオプションである場合はtrue、それ以外はfalse
	 */
	public function isHelp(): bool{
		return $this === self::Help;
	}
}
