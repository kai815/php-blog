<?php $this->setLayoutVar('title', 'ホーム') ?>

<h2>ホーム</h2>
<form action="<?php echo $base_url; ?>/article/post" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />

    <?php if (isset($errors) && count($errors) > 0) : ?>
        <li><?php echo $this->render('errors', array('errors' => $errors)); ?></li>
    <?php endif; ?>
    
    <textarea name="body" rows="2" cols="60">
    <?php echo $this->escape($body); ?>
    </textarea>
    <p>
        <input type="submit" value="発言" />
    </p>
</form>

<div id="articles">
    <?php foreach ($articles as $article) : ?>
        <?php echo $this->render('article/article', array('article' => $article)); ?>
    <?php endforeach; ?>
</div>