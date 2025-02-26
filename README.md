# Clessic

Clessicは、コマンドライン引数やクエリパラメータをパースし、PHPで書かれたコマンドを実行するための軽量フレームワークです。

## 特徴

- コマンドライン引数、クエリパラメータをパースして、コマンドの実行
- ANSIエスケープシーケンスに対応したカラー出力
- 依存ライブラリなしで動作

## インストール

```sh
composer require clessic-php/clessic
```

## 使用方法

### パースとコマンドの実行

次の例では、コマンドライン引数とクエリパラメータを共通のコードでパースし、コマンドを実行します。

```php
<?php

$classLoader = require 'vendor/autoload.php';

use Clessic\Clessic;
use Clessic\Command;
use Clessic\Command\VirtualView;
use Clessic\Enum\ReturnCode;

// パスの設定
Clessic::addCommandPaths('Commands');
Clessic::addViewPaths('Views');
Clessic::addRequestBodyParserPaths('BodyParsers');
$classLoader->addPsr4('Modeles\\', 'Modeles');

// コマンドラインまたはクエリパラメータを入力として受け取る
$cmd = new Command(Clessic::$arguments);
$returnCode = $cmd->execute();
if(($returnCode instanceof ReturnCode) && $returnCode->isNotFound())
{
    $returnCode = $cmd->execute(VirtualView::class);
}
```

### カラー出力の実行

ANSIエスケープに対応したコンソールではカラー出力が行えます。

```php
<?php

require 'vendor/autoload.php';

use Clessic\Clessic;
use Clessic\Enum\AnsiEscCode;

echo Clessic::AnsiEscape(AnsiEscCode::ResetAll) . Clessic::AnsiEscape(AnsiEscCode::TextRed, "Hello, World!");
```

## ライセンス

このプロジェクトは MIT ライセンスの下でライセンスされています。詳細は [LICENSE](LICENSE) ファイルを参照してください。
