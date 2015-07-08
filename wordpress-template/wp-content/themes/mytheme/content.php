<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php the_post_thumbnail(); ?>

    <header class="entry-header">
        <?php if (is_single()) {?>
        <h1 class="entry-title"><?php the_title();?></h1>
        <?php } else {?>
        <h2 class="entry-title">
            <a href="<?php the_permalink()?>" rel="bookmark"><?php the_title()?></a>
        </h2>
        <?php }?>
    </header><!-- .entry-header -->

    <div class="entry-content">

        <?php
        the_content('继续阅读');

        // 分段页面的链接 <!--nextpage-->
        wp_link_pages();
        ?>
    </div><!-- .entry-content -->

    <?php // 作者简介（仅当作者写了简介时才显示）
    if (is_single() && get_the_author_meta('description')) {
        get_template_part( 'author-bio' );
    } ?>

    <footer class="entry-footer">

        <!-- 日期 -->
        <span class="posted-on">
            <span class="screen-reader-text">发布于：</span>
            <a href="<?php the_permalink();?>" rel="bookmark">
                <time class="entry-date published" datetime="<?php echo esc_attr(get_the_date('c')) ?>"
                    ><?php echo get_the_date() ?></time>
                <time class="entry-date updated" datetime="<?php echo esc_attr(get_the_modified_date('c')) ?>"
                    ><?php echo get_the_modified_date() ?></time>
            </a>
        </span>

        <!-- 作者 -->
        <span class="byline">
            <span class="author vcard">
                作者：
                <a class="url fn n"
                   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                    <?php echo get_the_author(); ?>
                </a>
            </span>
        </span>

        <!-- 分类目录 -->
        <span class="cat-links">
            <span class="screen-reader-text">分类目录：</span>
            <?php echo get_the_category_list();?>
        </span>

        <!-- 标签 -->
        <span class="tags-links">
            <span class="screen-reader-text">标签：</span>
            <?php echo get_the_tag_list();?>
        </span>

        <!-- 编辑链接 -->
        <?php edit_post_link('编辑', '<span class="edit-link">', '</span>'); ?>

    </footer><!-- .entry-footer -->

</article><!-- #post-## -->
