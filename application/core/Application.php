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
    public function getDbManager()
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
     * @throws HttpNotFoundException ルーティングにパスがなかった時に404を表示
     */
    public function run()
    {
        try {
            $params = $this->router->resolve($this->request->getPathinfo());

            if ($params === false) {
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }

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
     * @throws HttpNotFoundException コントローラがなかった時に404を表示
     */
    public function runAction($controller_name, $action, $params = array())
    {
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            throw new HttpNotFoundException($controller_class . ' controller is not found.');
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

    /**
     * 404エラーのページを描画する処理
     */
    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
<!DOCTYPE html PUBLIC "-//W#C//DTD XHTML 1.0 Transitionas//EN"http://w3.org/TR/xhtml1/DTD/xhtml1-trasitiona.dtd">
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>404</title>
<head>
</head>
<body>
    {$message}
</body>
</html>
EOF
        );
    }
}
