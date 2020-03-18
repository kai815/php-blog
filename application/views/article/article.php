<div class="article">
    <div class="article_content">
        <?php echo $this->escape($article['user_name']); ?>
        <?php echo $this->escape($article['body']); ?>
    </div>
    <div>
        <?php echo $this->escape($article['created_at']); ?>
    </div>
</div>
