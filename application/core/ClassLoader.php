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

    /**
     * PHPをオートローダクラスに登録する
     *
     * loadClass()メソッドをオートロード時に呼び出されるように指定
     *
     * @access public
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * オートロード時に探すディレクトリを追加する
     *
     * modelとcoreのクラスファイルも読み込むようにする
     *
     * @access public
     * @param string $dir
     * 　　　　オートロードの対象となるディレクトリ(フルパスで指定)
     */
    public function registerDir($dir)
    {
        $this->dirs[] = $dir;
    }

    /**
     * クラスファイルの読み込みを行う
     *
     * $dirに指定されたディレクトリの中から「クラス名.php」を探して、
     * あったら読み込む。
     * @access public
     * @param string $class
     *        クラス名
     */
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