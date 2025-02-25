<?php
namespace Clessic\Enum;

/**
 * ANSIエスケープシーケンスを定義する列挙型
 */
enum AnsiEscCode: string{
	case ResetAll = "\033[0m";
	
	case Bold = "\033[1m";
	case Faint = "\033[2m";
	case ResetBoldFaint = "\033[22m";
	
	case Italic = "\033[3m";
	case ResetItalic = "\033[23m";
	
	case Underline = "\033[4m";
	case ResetUnderline = "\033[24m";
	
	case Strikethrough = "\033[9m";
	case ResetStrikethrough = "\033[29m";
	
	case TextBlack = "\033[30m";
	case TextRed = "\033[31m";
	case TextGreen = "\033[32m";
	case TextYellow = "\033[33m";
	case TextBlue = "\033[34m";
	case TextMagenta = "\033[35m";
	case TextCyan = "\033[36m";
	case TextWhite = "\033[37m";
	case ResetTextColor = "\033[39m";
	
	case BackgroundBlack = "\033[40m";
	case BackgroundRed = "\033[41m";
	case BackgroundGreen = "\033[42m";
	case BackgroundYellow = "\033[43m";
	case BackgroundBlue = "\033[44m";
	case BackgroundMagenta = "\033[45m";
	case BackgroundCyan = "\033[46m";
	case BackgroundWhite = "\033[47m";
	case ResetBackgroundColor = "\033[49m";
	
	/**
	 * 文字色変更判定関数
	 * @return bool 文字色変更である場合はtrue、それ以外はfalse
	 */
	public function isTextColor(): bool{
		return
			($this === self::TextBlack) ||
			($this === self::TextRed) ||
			($this === self::TextGreen) ||
			($this === self::TextYellow) ||
			($this === self::TextBlue) ||
			($this === self::TextMagenta) ||
			($this === self::TextCyan) ||
			($this === self::TextWhite);
	}
	
	/**
	 * 背景色変更判定関数
	 * @return bool 背景色変更である場合はtrue、それ以外はfalse
	 */
	public function isBackgroundColor(): bool{
		return
			($this === self::BackgroundBlack) ||
			($this === self::BackgroundRed) ||
			($this === self::BackgroundGreen) ||
			($this === self::BackgroundYellow) ||
			($this === self::BackgroundBlue) ||
			($this === self::BackgroundMagenta) ||
			($this === self::BackgroundCyan) ||
			($this === self::BackgroundWhite);
	}
	
	/**
	 * リセット判定関数
	 * @return bool リセットである場合はtrue、それ以外はfalse
	 */
	public function isReset(): bool{
		return
			($this === self::ResetAll) ||
			($this === self::ResetBoldFaint) ||
			($this === self::ResetItalic) ||
			($this === self::ResetUnderline) ||
			($this === self::ResetStrikethrough) ||
			($this === self::ResetTextColor) ||
			($this === self::ResetBackgroundColor);
	}
	
	/**
	 * リセット値取得関数
	 * @return ?AnsiEscCode 対応するリセット値を取得、無い場合はnull
	 */
	public function getReset(): ?self{
		if($this->isReset()){
			return null;
		}
		if(($this === self::Bold) || ($this === self::Faint)){
			return self::ResetBoldFaint;
		}
		if($this === self::Italic){
			return self::ResetItalic;
		}
		if($this === self::Underline){
			return self::ResetUnderline;
		}
		if($this === self::Strikethrough){
			return self::ResetStrikethrough;
		}
		if($this->isTextColor()){
			return self::ResetTextColor;
		}
		if($this->isBackgroundColor()){
			return self::ResetBackgroundColor;
		}
		return null;
	}
}
