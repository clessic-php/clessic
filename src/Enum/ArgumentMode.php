<?php
namespace Clessic\Enum;

/**
 * 引数の取得モード（単一値 or 複数値）を定義する列挙型
 *
 * @package Clessic
 */
enum ArgumentMode: int{
	case Single = 0;
	case Multiple = 1;
	
	/**
	 * 複数値判定関数
	 *
	 * @return bool 複数値である場合はtrue、それ以外はfalse
	 */
	public function isMultiple(): bool{
		return $this === self::Multiple;
	}
}
