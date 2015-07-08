<?php if (has_nav_menu('secondary') || is_active_sidebar( 'widget-sidebar' )) { ?>

<div id="secondary" class="secondary">

    <?php if ( has_nav_menu( 'secondary' ) ) : ?>
        <!-- 侧栏导航菜单（侧边菜单栏） -->
        <nav id="site-navigation" class="main-navigation" role="navigation">
            <?php
            wp_nav_menu( array(
                'menu_class'     => 'nav-menu',
                'theme_location' => 'secondary',
            ) );
            ?>
        </nav><!-- .main-navigation -->
    <?php endif; ?>

    <?php if ( is_active_sidebar( 'widget-sidebar' ) ) : ?>
        <!-- 侧栏小工具挂件区 -->
        <div id="widget-area" class="widget-area" role="complementary">
            <?php dynamic_sidebar( 'widget-sidebar' ); ?>
        </div><!-- .widget-area -->
    <?php endif; ?>

</div><!-- .secondary -->

<?php }
