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
    protected $auth_actions = array();

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

        if ($this->needsAuthentication($action) && !$this->session->isAuthenticated()) {
            throw new UnauthorizedActionException();
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
            $protocol = $this->request->isSsl() ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $base_url = $this->request->getBaseUrl();

            $url = $protocol . $host . $base_url . $url;
        }

        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeader('Location', $url);
    }

    /**
     * トークンを生成し、セッションに格納する
     *
     * 複数画面に対応するために最大10個保持
     *
     * @param string $form_name
     * @return string $token 生成されたトークン
     */
    protected function generateCsrfToken($form_name)
    {
        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());
        if (count($tokens) >= 10) {
            array_shift($tokens);
        }

        $token = sha1($form_name . session_id() . microtime());
        $tokens[] = $token;

        $this->session->set($key, $tokens);

        return $token;
    }

    /**
     * セッション上に格納されたトークンからPOSTされたトークンを探す
     *
     * 一度使用したら削除
     *
     * @param string $form_name
     * @param string $token
     * @return boolean トークンが存在した場合にtrueを返す
     */
    protected function checkCsrfToken($form_name, $token)
    {
        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());

        if (false !== ($pos = array_search($token, $tokens, true))) {
            unset($tokens[$pos]);
            $this->session->set($key, $tokens);

            return true;
        }

        return false;
    }

    /**
     * 認証が必要なアクションかどうかの判定
     *
     * @param string $action
     * @return boolean
     */
    protected function needsAuthentication($action)
    {
        if ($this->auth_actions === true
        || (is_array($this->auth_actions) && in_array($action, $this->auth_actions))) {
            return true;
        }

        return false;
    }
}
