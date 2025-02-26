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


### コマンドの追加

`Clessic::addCommandPaths()`で設定したディレクトリ下に `sample.php` という名前で、次のファイルを保存します。

```php
<?php

namespace Clessic\Sample;

use Clessic\Base\CommandBase;
use Clessic\Enum\ReturnCode;
use Clessic\Enum\ArgumentType;
use Clessic\Enum\ArgumentMode;

if(!class_exists(SampleCommand::class)):
/**
 * サンプルコマンドクラス
 *
 * @package Clessic
 */
class SampleCommand extends CommandBase {
    /** @inheritdoc CommandBase */
    public static $package = 'sample';
    
    /** @inheritdoc CommandBase */
    public static $description = 'サンプルコマンドの説明文';
    
    /** @inheritdoc CommandBase */
    public static $version = 'v1.0.0-stable';
    
    /** @inheritdoc CommandBase */
    public static $options = [
        'name' => [
            ArgumentType::String,
            ArgumentMode::Single,
            '-n',
            '--name',
            '@名前を指定するオプション',
        ],
        'help' => [
            ArgumentType::Help,
            '-h',
            '--help',
            '@ヘルプを表示するためのフラグ',
        ]
    ];

    /** @inheritdoc CommandBase */
    public static function run(array $args, mixed $body, array $headers): int|ReturnCode {
        $name = $args['name'] ?? 'default';
        echo "Hello, " . $name . "!" . PHP_EOL;
        return ReturnCode::Success;
    }
}
endif;
return SampleCommand::class;
```

次のコマンドで実行すると、"Hello, World!" と出力されます。
```sh
php （パースとコマンドの実行を保存したファイル名） sample -n World
```


### ビューの追加

`Clessic::addViewPaths()`で設定したディレクトリ下に `index.php` という名前で、次のファイルを保存します。

```php
<?php return function(...$_){ ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sample View</h1>
        <p>This is a sample view created for the Clessic framework.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/gh/clessic-php/clessic@1.0/Clessic.min.js"></script>
    <script>
        var command = new Clessic();
        command.run(["sample", "-n", "World"]).then(function(result){
            console.log(result); // Hello, World!
        })
    </script>
</body>
</html>
<?php
};
```


### カスタムリクエストボディパーサの追加

`Clessic::addRequestBodyParserPaths()`で設定したディレクトリ下に `application-json.php` という名前で、次のファイルを保存します。

```php
<?php
return function($body, $params)
{
    return json_decode($body, true);
};
```

リクエストヘッダー`Content-Type: application/json`でJSONリクエストボディを送信したコマンドは、`run()`の第2引数をJSONをパースした値が渡されます。



## ライセンス

このプロジェクトは MIT ライセンスの下でライセンスされています。詳細は [LICENSE](LICENSE) ファイルを参照してください。
