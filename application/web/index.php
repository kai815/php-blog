<?php
/**
 * bootstrap.php,MiniBlog・・・を読み込むためのもの
 *
 * runメソッドも実行
 */

require '../bootstrap.php';
require '../MiniBlogApplication.php';

$app = new MiniBlogApplication(false);
$app->run();
