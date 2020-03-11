<?php

/**
 * 【共通】リクエストクラス
 *
 * ユーザのリクエススト情報を制御するクラス
 *
 */

class Request
{
    /**
     * HTTPメソッドがPOSTかの判定を行う
     */
    public function isPost()
    {
        if ($_REQUEST['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    /**
     * $_GET変数から値を取得するメソッド
     */
    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }

    /**
     * $_POST変数から値を取得するメソッド
     */
    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return $default;
    }

    /**
     * サーバのホスト名を取得するメソッド
     */
    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * HTTPSでアクセスされたかを判定するメソッド
     */
    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    /**
     * URLのホスト部分以降の値を取得するメソッド
     */
    public function getRequstUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * ベースURLを取得するメソッド
     */
    public function getBageUrl()
    {
        $script_name = $_SERVER['SCRIPT_NAME'];

        $request_uri = $this->getRequstUri();

        if (0 === strpos($request_uri, $script_name)) {
            return $script_name;
        } elseif (0 === strpos($request_uri, dirname($script_name))) {
            return rtrim(dirname($script_name), '/');
        }

        return '';
    }

    /**
     * パスを取得するメソッド
     */
    public function getPathInfo()
    {
        $base_url = $this->getBageUrl();
        $request_uri = $this->getRequstUri();

        if (false !== ($pos = strpos($request_uri, '?'))) {
            $request_uri = substr($request_uri, 0, $pos);
        }

        $path_info = (string)substr($request_uri, strlen($base_url));

        return $path_info;
    }

}