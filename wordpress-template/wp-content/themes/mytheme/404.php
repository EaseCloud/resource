<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title">您打开的页面不存在</h1>
            </header><!-- .page-header -->

            <div class="page-content">
                <p><a href="<?php echo home_url();?>">返回首页</a>，或者搜索一下？</p>
                <?php get_search_form(); ?>
            </div><!-- .page-content -->
        </section><!-- .error-404 -->

    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
