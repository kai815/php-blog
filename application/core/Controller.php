<?php

/**
 * 【共通】コントローラの抽象クラス
 *
 */

abstract class Controller
{
    protected $controller_name;
    protected $action_name;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    /**
     * コントローラ名を取得し、Requestクラスなどのインスタンスをプロパティにセット
     *
     */
    public function __construct($application)
    {
        $this->controller_name = strtolower(substr(get_class($this), 0, -10));

        $this->application = $application;
        $this->request     = $application->getRequest();
        $this->response    = $application->getResponse();
        $this->session     = $application->getSession();
        $this->db_manager  = $application->getDbManager();
    }

    /**
     * 実際にアクションを行う
     *
     * Applictionクラスから呼ばれる
     *
     * @param string $action
     * @param array $params
     * @return void
     */
    public function run($action, $params = array())
    {
        $this->action_name = $action;

        $action_method = $action . 'Action';
        if (!method_exists($this, $action_method)) {
            $this->forward404();
        }

        $content = $this->$action_method($params);

        return $content;
    }

    /**
     * ビュークラスのrenderメソッドを実行しビューファイルをレンダリングする
     *
     * @param array $variables
     * @param string $template
     * @param string $layout
     * @return レンダリングされたビュー
     */
    protected function render($variables = array(), $template = null, $layout = 'layout')
    {
        $defaults = array(
            'request'   => $this->request,
            'base_url'  => $this->request->getBaseUrl(),
            'session'   => $this->session,
        );

        $view = new View($this->application->getViewDir(), $defaults);

        if (is_null($template)) {
            $template = $this->action_name;
        }

        $path = $this->controller_name . '/' . $template;

        return $view->render($path, $variables, $layout);
    }

    /**
     * HttpNotFoundExceptionを通知し404エラー画面に遷移
     *
     */
    protected function forward404()
    {
        throw new HttpNotFoundException('Forwarded 404 page from ' . $this->controller_name . '/' .$this->action_name);
    }
    
    /**
     * URLを受け取りReponseオブジェクトにリダイレクトするように設定
     *
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        if (!preg_match('#https://#', $url)) {
            $protocol = $this->request->isSsl ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $base_url = $this->request->getBaseUrl();

            $url = $protocol . $host . $base_url . $url;
        }

        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeader('Location', $url);
    }
}
