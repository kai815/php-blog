<?php
/**
 * 【共通】レスポンスを表すクラス
 *
 * HTTPヘッダとHTMLなどのコンテンツを返す
 *
 * @category Common
 * @package Response
 */
class Response
{
    protected $content;
    protected $status_code = 200;
    protected $status_text = 'OK';
    protected $http_headers = array();

    /**
     * 実際にレスポンスを返す
     */
    public function send()
    {
        header('HTTP/1.1' .$this->status_code . ' ' . $this->status_text);

        foreach ($this->http_headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->content;
    }

    /**
     * HTMLなどのクライアントに返す内容を格納する
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * ステータスコードとテキストを格納する
     *
     * @param integer $status_code
     * @param string $status_text
     */
    public function setStatusCode($status_code, $status_text = '')
    {
        $this->status_code = $status_code;
        $this->status_txt = $status_text;
    }

    /**
     * HTTPヘッダを格納する
     *
     * @param string $name
     * @param string $value
     */
    public function setHttpHeader($name, $value)
    {
        $this->http_headers[$name] = $value;
    }
}
