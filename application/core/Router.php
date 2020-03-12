<?php

/**
 * 【共通】ルータークラス
 *
 * ルーティングの定義をまとめたクラス。
 * パスからコントローラーとアクションを特定する
 */

class Router
{
    protected $routes;

    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }
    /**
     * 動的パラメータを正規表現でキャプチャできる形式に変換する
     *
     * @access public
     * @param array $definitions
     *        ルーティング定義配列
     * @return array ルーティング定義配列
     */
    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url));
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/'.implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    /**
     * ルーティングとパスのマッチングを行う
     *
     * @access public
     * @param string $path_info
     *        アクセスされたパスの情報
     * @return array $params マッチした場合にルーティングパラメータを変換（マッチしない場合はfalse）
     */
    public function resolve($path_info)
    {
        if ('/' !== substr($path_info, 0, 1)) {
            $path_info = '/' . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
                $params = array_merge($params, $matches);

                return $params;
            }
        }

        return false;
    }
}
