<?php
/**
 *
 * 【開発用】bootstrap.php,MiniBlog・・・を読み込むためのもの
 *
 * runメソッドも実行
 */

require '../bootstrap.php';
require '../MiniBlogApplication.php';

$app = new MiniBlogApplication(true);
$app->run();
