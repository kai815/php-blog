<?php
/**
 * 投稿のコントローラのクラス
 */

class ArticleController extends Controller
{
    protected $auth_actions = array('index', 'post');

    /**
     * 投稿一覧を表示するアクション
     *
     * @return void
     */
    public function indexAction()
    {
        $user = $this->session->get('user');
        $articles = $this->db_manager->get('article')->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render(array(
            'articles' => $articles,
            'body'     => '',
            '_token'   => $this->generateCsrfToken('article/post'),
        ));
    }

    /**
     * 投稿を保存するアクション
     *
     */
    public function postAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }
        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('article/post', $token)) {
            return $this->redirect('/');
        }

        $body = $this->request->getPost('body');
        $errors = array();
        if (!strlen($body)) {
            $errors[] = 'ひとことを入力してください';
        } elseif (mb_strlen($body) > 200) {
            $error[] = 'ひとことは200文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $user = $this->session->get('user');
            $this->db_manager->get('Article')->insert($user['id'], $body);

            return $this->redirect('/');
        }

        $user = $this->session->get('user');
        $articles = $this->db_manager->get('Article')->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render(array(
            'errors'    => $errors,
            'body'      => $body,
            'articles'  => $articles,
            '_token'    => $this->generateCsrfToken('article/post'),
        ), 'index');
    }

    /**
     * ユーザーの投稿一覧アクション
     *
     * @param array $params
     * @return void
     */
    public function userAction($params)
    {
        $user = $this->db_manager->get('User')->fetchByUserName($params['user_name']);

        if (!$user) {
            $this->forward404();
        }

        $articles = $this->db_manager->get('Article')->fetchAllByUserId($user['id']);

        $following = null;
        if ($this->session->isAuthenticated()) {
            $my = $this->session->get('user');
            if ($my['id'] !== $user['id']) {
                $following = $this->db_manager->get('Following')->isFollowing($my['id'], $user['id']);
            }
        }

        return $this->render(array(
            'user'      => $user,
            'articles'  => $articles,
            'following' => $following,
            '_token'    => $this->generateCsrfToken('account/follow'),
        ));
    }

    public function showAction($params)
    {
        $article = $this->db_manager->get('Article')->fetchByIdAndUserName($params['id'], $params['user_name']);

        if (!$article) {
            $this->forward404();
        }

        return $this->render(array('article' => $article));
    }
}
