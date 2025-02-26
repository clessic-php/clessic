<?php
namespace Clessic\Command;
use Clessic\View;
use Clessic\Base\CommandBase;
use Clessic\Enum\ArgumentType;
use Clessic\Enum\ArgumentMode;
use Clessic\Enum\ReturnCode;

/**
 * 仮想ビューコマンド
 * コマンド名でビューを検索、表示を行う仮想コマンド
 *
 * @package Clessic
 */
class VirtualView extends CommandBase{
	/** @inheritdoc CommandBase */
	public static $package = "Clessic";
	
	/** @inheritdoc CommandBase */
	public static $description = "仮想ビューコマンド";
	
	/** @inheritdoc CommandBase */
	public static $version = "v1.0.0-試用";
	
	/** @inheritdoc CommandBase */
	public static $options = [
		"help" => ["-?", "--help", ArgumentType::Help, "@Display this help and exit."],
		"path" => ["%0", ArgumentType::String, "@Command"],
	];
	
	/** @inheritdoc CommandBase */
	public static function run(array $args, mixed $body, array $headers): int|ReturnCode{
		$view = new View($args["path"]);
		echo $view([]);
		return 0;
	}
}