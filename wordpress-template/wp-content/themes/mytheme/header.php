<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <?php //https://code.google.com/p/ie7-js/ ?>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
</head>
<body <?php body_class();?>>
<div id="page" class="hfeed site">

    <header id="masthead" class="site-header" role="banner">

        <!-- 可自定义的 LOGO 图像，在 外观->自定义 中配置 -->
        <?php if(has_header_image()) { ?>
        <img src="<?php header_image(); ?>" class="logo"
             height="<?php echo get_custom_header()->height; ?>"
             width="<?php echo get_custom_header()->width; ?>"
             alt="<?php bloginfo('title') ?>" />
        <?php } ?>

    </header><!-- #masthead -->

    <div id="main" class="site-main">