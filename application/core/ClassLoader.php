<?php
/**
 * 【共通】オートロードクラス
 * 
 * オートロードに関する処理をまとめたクラス。
 * オートロードの対象となるクラスのルール
 * クラスは「クラス名.php」というファイル名で保存
 * クラスはcoreディレクトリ、modelディレクトリに配置
 * 
 * @access public
 * @category Common
 * @package ClassLoader
 */
class ClassLoader
{
    protected $dirs;

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function registerDir($dir)
    {
        $this->dirs[] = $dir;
    }

    public function loadClass($class)
    {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' . $class . '.php';
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}