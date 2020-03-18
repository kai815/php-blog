<?php
/**
 * 【共通】ミニブログアプリケーションクラス
 */

class MiniBlogApplication extends Application
{
    protected $login_action = array('account', 'signin');

    /**
     * ルートディレクトリのパスを返す
     *
     * @return string
     */
    public function getRootDir()
    {
        return dirname(__FILE__);
    }

    /**
     * ルーティング定義配列を返す
     *
     * アクションを実装していくときに適宜追加
     *
     * @return array
     */
    protected function registerRoutes()
    {
        return array(
            '/'
                => array('controller' => 'article', 'action' => 'index'),
            '/article/post'
                => array('controller' => 'article', 'action' => 'post'),
            '/account'
                => array('controller' => 'account', 'action' => 'index'),
            '/account/:action'
                => array('controller' => 'account'),
        );
    }

    /**
     * アプリケションの設定を記載
     *
     * 本来ならgitにあげないべきかもだが、仮想環境のものなのでコミットしちゃう
     */
    protected function configure()
    {
        $this->db_manager->connect('master', array(
            'dsn'      => 'mysql:dbname=mini_blog',
            'user'     => 'root',
            'password' => 'root',
        ));
    }
}
