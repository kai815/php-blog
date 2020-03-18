<div class="article">
    <div class="article_content">
        <a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($article['user_name']); ?>">
            <?php echo $this->escape($article['user_name']); ?>
        </a>
        <?php echo $this->escape($article['body']); ?>
    </div>
    <div>
        <a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($article['user_name']);?>/article/<?php echo $this->escape($article['id']); ?>">
            <?php echo $this->escape($article['created_at']); ?>
        </a>
    </div>
</div>
