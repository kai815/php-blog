<?php

/**
 * データベースへのアクセスを行うクラス
 *
 * テーブルごとに子クラスを作成する
 * SQLの実行時に頻繁に出てくるような処理を抽象化しておく
 */

abstract class DbRepository
{
    protected $con;

    public function __construct($con)
    {
        $this->setConnection($con);
    }

    /**
     * DbManagerClassからPDOクラスのインスタンスを受け取り内部に保持する
     *
     * @param instance $con
     */
    public function setConnection($con)
    {
        $this->con = $con;
    }

    /**
     * プリペアードステートメントを実際に実行する
     *
     * @param string $sql
     * @param array $params
     * @return instance
     */
    public function execute($sql, $params = array())
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * レコードを1行取得
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetch($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * レコードをすべて取得
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
