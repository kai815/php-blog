<?php $this->setLayoutVar('title', $article['user_name']); ?>

<?php echo $this->render('article/article', array('article' => $article)); ?>
