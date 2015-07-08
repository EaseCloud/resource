<?php get_header(); ?>

<section id="primary" class="content-area">

    <main id="main" class="site-main" role="main">

        <?php if (have_posts()) { ?>

            <header class="page-header">
                <h1 class="page-title">查找：<?php echo get_search_query(); ?></h1>
            </header><!-- .page-header -->

            <?php while (have_posts()) {

                the_post();

                get_template_part('content', 'search');

            }

            // 分页组件
            the_posts_pagination(array(
                'prev_text'          => '上一页',
                'next_text'          => '下一页',
                'before_page_number' => '分页：',
            ));

        } else {

            get_template_part( 'content', 'none' );

        } ?>

    </main><!-- .site-main -->

</section><!-- .content-area -->

<?php get_footer(); ?>
