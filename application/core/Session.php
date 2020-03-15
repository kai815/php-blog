<?php

/**
 * セッション情報を管理するクラス
 */
class Session
{
    protected static $sessionStarted = false;
    protected static $sessionIdRegenarated = false;

    /**
     * セッション自動スタート
     */
    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    /**
     * セッションをセットする
     *
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * セッションを取得する
     *
     */
    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    /**
     * セッションを破棄する
     *
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * セッションを初期化する
     *
     * */
    public function clear()
    {
        $_SESSION = array();
    }

    /**
     * セッションIDを新しく発行する
     *
     * @param boolean $destroy
     */
    public function regenarete($destroy = true)
    {
        if (!self::$sessionStarted) {
            session_regenerate_id($destroy);

            self::$sessionIdRegenarated = true;
        }
    }

    /**
     * ログイン状態の制御
     *
     * @param [type] $bool
     *
     */
    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool)$bool);

        $this->regenarete();
    }

    /**
     * ログイン状態の制御
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}