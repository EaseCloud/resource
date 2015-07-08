<header class="page-header">
    <h1 class="page-title">这里什么都没有找到</h1>
</header>

<div class="page-content">
    <?php if(is_home() && current_user_can('publish_posts')) { ?>

        <p>写一篇文章吧：<a href="<?php echo esc_url(admin_url('post-new.php')); ?>">写文章</a></p>

    <?php } elseif (is_search()) { ?>

        <p>这次搜索找不到任何东西，再试一次？</p>
        <?php get_search_form(); ?>

    <?php } else { ?>

        <p>这里还没有任何东西，或者搜索可以帮到您？</p>
        <?php get_search_form(); ?>

    <?php } ?>
</div><!-- .page-content -->
