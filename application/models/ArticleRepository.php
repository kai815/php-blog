<?php

/**
 * 記事モデルのクラス
 */
class ArticleRepository extends DbRepository
{
    /**
     * 投稿をDBに挿入する
     *
     * @param integer $user_id
     * @param string $body
     */
    public function insert($user_id, $body)
    {
        $now = new DateTime();
        $sql = "
            INSERT INTO article(user_id, body, created_at)
                VALUES(:user_id, :body, :created_at)
        ";

        $stmt = $this->execute($sql, array(
            ':user_id'     => $user_id,
            ':body'        => $body,
            ':created_at'  => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * ログインユーザーに関連する投稿を取得
     *
     * @param integer $user_id
     * @return array
     */
    public function fetchAllPersonalArchivesByUserId($user_id)
    {
        $sql = "
            SELECT a.*, u.user_name
            FROM article a
                LEFT JOIN user u ON a.user_id = u.id
            WHERE u.id = :user_id
            ORDER BY a.created_at DESC
        ";

        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }
}
