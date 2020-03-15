<?php

/**
 * 【共通】アプリケーション全体を司るクラス
 *
 * Requestクラス、Routerクラス、Sessionクラスなどのオブジェクトの管理
 * ルーティングの定義、コントローラーの事項、レスポンスの送信を行う
 */

abstract class Application
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    /**
     * デバッグモードのセット
     *
     * 値に応じてエラー表示を変更する
     *
     * @param boolean $debug
     *        デバッグモードフラグ
     */
    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    /**
     * クラスの初期化を行う
     *
     */
    protected function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DbManager();
        $this->router = new Router($this->registerRoutes());
    }

    protected function configure()
    {
    }

    abstract public function getRootDir();

    abstract protected function registerRoutes();

    /**
     * デバックモードの値を返却する
     *
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->debug;
    }

    /**
     * リクエストクラスのインスタンスを返却
     *
     * @return instance
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * レスポンスクラスのインスタンスを返却
     *
     * @return instance
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * セッションクラスのインスタンスを返却
     *
     * @return instance
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * DBマネージャクラスのインスタンスを返却
     *
     * @return instance
     */
    public function getDbManger()
    {
        return $this->db_manager;
    }

    /**
     * コントローラーのディレクトリを返却
     *
     * @return string
     */
    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    /**
     * ビューのディレクトリを返却
     *
     * @return string
     */
    public function getViewDir()
    {
        return $this->getrootDir() . '/views';
    }

    /**
     * モデルのディレクトリを返却
     *
     * @return string
     */
    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    /**
     * Webのディレクトリを返却
     *
     * @return string
     */
    public function getWevDir()
    {
        return $this->getRootDir() . '/web';
    }

    /**
     * アクションの実行を行う
     *
     * ルーティングパラメータを受け取り、コントローラー名とアクション名を特定してrunAction()を実施
     */
    public function run()
    {
        $params = $this->router->resolve($this->request->getPathinfo());
        if ($params === false) {
            // todo-A
        }

        $controller = $param['controller'];
        $action = $params['action'];

        $this->runAction($controller, $action, $params);

        $this->response->send();
    }

    /**
     * 実際にアクションを実行する
     *
     * コントローラのクラス名にはControllerをつける
     *
     * @param string $controller_name
     * @param string $action
     * @param array $params
     */
    public function runAction($controller_name, $action, $params = array())
    {
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            // todo-B
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    /**
     * コントローラクラスの読み込み
     *
     * コントローラクラスが読み込まれていない場合に行う
     *
     * @param string $controller_class
     * @return void
     */
    protected function findController($controller_class)
    {
        if (!class_exists($controller_class)) {
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
            if (!is_readable($controller_file)) {
                return false;
            } else {
                require_once $controller_file;

                if (!class_exists(($controller_class))) {
                    return false;
                }
            }
        }

        return new $controller_class($this);
    }
}
