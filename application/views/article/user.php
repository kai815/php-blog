<?php $this->setLayoutVar('title', $user['user_name']); ?>

<h2><?php echo $this->escape($user['user_name']); ?></h2>

<div id="articles">
    <?php foreach ($articles as $article) : ?>
        <?php echo $this->render('article/article', array('article' => $article)); ?>
    <?php endforeach; ?>
</div>
