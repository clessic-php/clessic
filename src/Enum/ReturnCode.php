<?php
namespace Clessic\Enum;

/**
 * 終了コードを定義する列挙型
 */
enum ReturnCode: int{
	case Success = 0;
	case NotFound = 1;
	case Problem = 2;
	
	/**
	 * 成功判定関数
	 * @return bool 成功である場合はtrue、それ以外はfalse
	 */
	public function isSucceed(): bool{
		return $this === self::Success;
	}
	
	/**
	 * 未発見判定関数
	 * @return bool 未発見である場合はtrue、それ以外はfalse
	 */
	public function isNotFound(): bool{
		return $this === self::NotFound;
	}
	
	/**
	 * 異常を判定する関数
	 * @return bool 異常である場合はtrue、それ以外はfalse
	 */
	public function isError(): bool{
		return $this !== self::Success;
	}
}