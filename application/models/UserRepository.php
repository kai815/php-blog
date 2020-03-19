<?php
/**
 * ユーザーモデルのクラス
 */

class UserRepository extends DbRepository
{
    /**
     * レコードの新規作成
     *
     * @param string $user_name
     * @param string $password
     */
    public function insert($user_name, $password)
    {
        $password = $this->hashPassword($password);
        $now = new DateTime();

        $sql = "
            INSERT INTO user(user_name, password, created_at)
            VALUES(:user_name, :password, :created_at)
            ";
        $stmt = $this->execute($sql, array(
            ':user_name'    => $user_name,
            ':password'     => $password,
            ':created_at'   => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * パスワードのハッシュ化
     *
     * @param string $password
     * @return void
     */
    public function hashPassword($password)
    {
        return sha1($password . 'SecretKey');
    }

    /**
     * ユーザーIDを元にユーザデータを取得
     *
     * @param string $user_name
     * @return array
     */
    public function fetchByUserName($user_name)
    {
        $sql = "SELECT * FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, array(':user_name' => $user_name));
    }

    /**
     * ユーザーIDが一意かチェック
     *
     * @param string $user_name
     * @return boolean
     */
    public function isUniqueUserName($user_name)
    {
        $sql = "SELECT COUNT(id) as count FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, array(':user_name' => $user_name));
        if ($row['count'] === '0') {
            return true;
        }

        return false;
    }

    public function fetchAllFollowingByUserId($user_id)
    {
        $sql = "
            SELECT u.*
            FROM user u
                LEFT JOIN following f ON f.following_id = u.id
            WHERE f.user_id = :user_id
        ";

        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }

}