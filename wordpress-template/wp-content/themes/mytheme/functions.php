<?php

/**
 * 动态版本码以消灭静态文件缓存引起的问题
 */
define('VERSION', '1.0.0');
define('DYNAMIC_VERSION', WP_DEBUG ? strval(rand()) : VERSION);

/**
 * 导入自定义 PostType 类
 */
require __DIR__.'/class/CustomPost.class.php';

/**
 * 指定内容宽度
 */
if(!isset($content_width)) {
    $content_width = 600;
}

/**
 * 主题加载的动作
 */
add_action('after_setup_theme', function() {

    // 1. 支持特色图像
    add_theme_support( 'post-thumbnails' );
    // set_post_thumbnail_size( 672, 372, true );
    // add_image_size( 'image-full-width', 1038, 576, true );

    // 2. 注册菜单栏
    register_nav_menus( array(
        'primary'   => '主菜单栏',
        'secondary' => '侧边菜单栏',
        'footer' => '底部菜单栏',
        'mobile' => '移动端菜单',
    ) );

    // 3. 使用默认的组件 markup
    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ));

    // 4. 支持的文章格式，参考：http://codex.wordpress.org/Post_Formats
    add_theme_support( 'post-formats', array(
        'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery',
    ));

    // 5. 支持自动 feed
    add_theme_support('automatic-feed-links');

    // 6. 自动添加 <head> 里面的 title 属性
    add_theme_support('title-tag');

    // 7. 自定义顶部（顶部图像 - 可用作 LOGO，默认大小按照百度缩略图尺寸优化）
    // src: header_image(); height: get_custom_header()->height; width: get_custom_header()->width;
    add_theme_support('custom-header', array(
        'width'         => 121,
        'height'        => 75,
        'default-image' => get_template_directory_uri() . '/images/header.jpg',
    ));

    // 8. 支持自定义背景图图像
    add_theme_support('custom-background');

    // 9.. 只要是非手机端登录的用户就显示顶部管理菜单栏
    add_filter( 'show_admin_bar', function() {
        return is_user_logged_in() && !wp_is_mobile(); // 手机端不显示 admin_bar
    });

});

/**
 * 模板引入的 js 和 css 文件库
 */
add_action( 'wp_enqueue_scripts', function() {

    // 0. WordPress 内置脚本
    // 0.1. 评论回复脚本
    if (is_singular()) wp_enqueue_script( "comment-reply" );

    // 1. CSS 样式重置
    // http://cssreset.com
    wp_enqueue_style('reset', get_template_directory_uri().'/lib/cssreset-min.css', array());

    // 2. 轮播组件 owl-carousel
    // 2.1. Owl Carousel 2
    // http://www.owlcarousel.owlgraphic.com/
    wp_enqueue_style( 'owl-carousel-2', get_template_directory_uri().'/lib/owl-carousel-2/owl.carousel.css', array('reset'), '2.0.0-beta.2.4' );
    wp_enqueue_script( 'owl-carousel-2', get_template_directory_uri().'/lib/owl-carousel-2/owl.carousel.min.js', array( 'jquery' ), '2.0.0-beta.2.4', true );
    // 2.2. Owl Carousel 1
    // http://www.owlgraphic.com/owlcarousel/
    wp_enqueue_style( 'owl-carousel', get_template_directory_uri().'/lib/owl-carousel/owl.carousel.css', array('reset'), '1.3.2' );
    wp_enqueue_style( 'owl-carousel-theme', get_template_directory_uri().'/lib/owl-carousel/owl.theme.css', array('owl-carousel'), '1.3.2' );
    wp_enqueue_script( 'owl-carousel', get_template_directory_uri().'/lib/owl-carousel/owl.carousel.min.js', array( 'jquery' ), '1.3.2');

    // 3. jquery.form.js
    // http://plugins.jquery.com/form/
    // http://malsup.com/jquery/form/
    wp_enqueue_script( 'jquery-form', get_template_directory_uri().'/lib/jquery.form.js', array( 'jquery' ), '3.46.0');

    // 4. Dashicons 图标库
    // https://developer.wordpress.org/resource/dashicons/
    wp_enqueue_style( 'dashicons', get_home_url().'/wp-includes/css/dashicons.min.css', array('template-style'), '3.8' );

    // 5. Font Aowesome 图标库
    // http://fontawesome.io/
    wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/lib/font-awesome/css/font-awesome.min.css', array(), '4.3.0' );

    // 6. jpeg 压缩器
    // http://web.archive.org/web/20120830003356/http://www.bytestrom.eu/blog/2009/1120a_jpeg_encoder_for_javascript
    wp_enqueue_script( 'jpeg-encoder', get_template_directory_uri().'/lib/jpeg_encoder/jpeg_encoder_basic.js', array(), '0.0.0');

    // 7. 移除默认的 Google 字体
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');

    // 99. 模板自定义的 CSS/JS
    wp_enqueue_style('template-style', get_template_directory_uri().'/style.css', array('reset'), DYNAMIC_VERSION);
    wp_enqueue_script('template-script', get_template_directory_uri().'/js/functions.js', array('jquery'), DYNAMIC_VERSION);

});


/**
 * 注册小工具区
 */
add_action('widgets_init', function() {
    register_sidebar( array(
        'name'          => '侧栏挂件区',
        'id'            => 'widget-sidebar',
        'description'   => '从这里将你的小工具添加到页面侧栏',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
});


/**
 * 添加后台样式
 */
add_action('admin_enqueue_scripts', function() {

    // 1. 添加自定义样式和脚本
    wp_enqueue_style('admin-style', get_template_directory_uri().'/style-admin.css', false, DYNAMIC_VERSION);
    wp_enqueue_script('admin-script', get_template_directory_uri().'/js/functions-admin.js', array('jquery'), DYNAMIC_VERSION);

    // 2. 移除后台默认的 Google 字体
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');

});


/**
 * 为 body class 添加 page-[slug], single-[class] 等等 html class
 * @param $classes: 之前处理生成的 class 列表
 * @return array: 处理之后生成的 class 列表
 */
add_filter('body_class', function($classes) {
    // 页面的处理
    if(is_page()) {
        global $post;
        $classes []= 'page-'.$post->post_name;
    }
    // 文章的处理
    if(is_single()) {
        global $post;
        $classes []= 'single-'.$post->post_name;
    }
    // 加入是 pc 或者 mobile 的类
    $classes []= wp_is_mobile() ? 'ua-mobile' : 'ua-pc';
    // 返回结果
    return $classes;
}, 10, 1);


/**
 * 插入【置顶】、【隐藏】的评论操作标签
 */
function comment_row_action( $actions, $comment ) {
    // 置顶/取消置顶
    if(get_comment_meta($comment->comment_ID, 'is_sticky', true) === '1') {
        $new_actions['stick'] = '<a class="comment-action-custom"
            href="javascript:;" data-action="unstick" data-post-id="'.$comment->comment_post_ID.'"
            data-comment-id="'.$comment->comment_ID.'">取消置顶</a>';
    } else {
        $new_actions['unstick'] = '<a class="comment-action-custom"
            href="javascript:;" data-action="stick" data-post-id="'.$comment->comment_post_ID.'"
            data-comment-id="'.$comment->comment_ID.'">置顶</a>';
    }
    // 隐藏内容/显示内容
    if(get_comment_meta($comment->comment_ID, 'is_hide', true) === '1') {
        $new_actions['show'] = '<a class="comment-action-custom"
            href="javascript:;" data-action="show" data-post-id="'.$comment->comment_post_ID.'"
            data-comment-id="'.$comment->comment_ID.'">显示内容</a>';
    }
    else{
        $new_actions['hide'] = '<a class="comment-action-custom"
            href="javascript:;" data-action="hide" data-post-id="'.$comment->comment_post_ID.'"
            data-comment-id="'.$comment->comment_ID.'">隐藏内容</a>';
    }

    // 在前边插入新增的两个动作链接并返回
    return array_merge($new_actions, $actions);
}
add_filter('comment_row_actions', 'comment_row_action', 10, 2);


/**
 * ajax 处理新增的评论操作动作【置顶】、【隐藏】
 */
add_action('wp_ajax_comment_action_custom', function() {
    if(!empty($_REQUEST['action_comment']) && !empty($_REQUEST['comment_id'])) {
        // 执行修改的动作
        $action_comment = $_REQUEST['action_comment'];
        $comment_id = intval($_REQUEST['comment_id']);
        $comment = get_comment($comment_id);
        if($action_comment == 'stick') {
            update_comment_meta($comment_id, 'is_sticky', 1);
        } elseif($action_comment == 'unstick') {
            update_comment_meta($comment_id, 'is_sticky', 0);
        } elseif($action_comment == 'hide') {
            update_comment_meta($comment_id, 'is_hide', 1);
        } elseif($action_comment == 'show') {
            update_comment_meta($comment_id, 'is_hide', 0);
        }
        // 获取修改后的 markup
        ob_start();
        $wp_list_table = _get_list_table(
            strpos($_SERVER['HTTP_REFERER'], 'wp-admin/post.php') === false ?
                'WP_Comments_List_Table' : 'WP_Post_Comments_List_Table',
            array( 'screen' => 'edit-comments' )
        );
        $wp_list_table->single_row( $comment );
        $comment_list_item = ob_get_clean();
        // 打包输出
        $x = new WP_Ajax_Response();
        $x->add( array(
            'what' => 'edit_comment',
            'id' => $comment->comment_ID,
            'data' => $comment_list_item,
            'position' => -1
        ));
        $x->send();
        exit(0);
    }
});


/**
 * 编辑器管理
 */
add_action('admin_init', function() {

    // 添加编辑器样式表
    add_editor_style(get_template_directory_uri().'/css/editor-style.css');

    // 为编辑器添加调整字号的按钮
    add_filter('mce_buttons_3', function($buttons) {
        $buttons[] = 'hr'; $buttons[] = 'fontselect';
        $buttons[] = 'fontsizeselect';
        return $buttons;
    });
});


/**
 * 客户端点击评论操作时的动作
 */
add_action('admin_footer', function () {?>
    <script>
        jQuery(function($) {
            $('body').on('click', '.comment-action-custom', function() {
                var comment_id = parseInt($(this).data('comment-id'));
                var comment_post_id = parseInt($(this).data('comment-post-id'));
                $.post(ajaxurl, {
                    action: 'comment_action_custom',
                    action_comment: $(this).data('action'),
                    comment_id: comment_id,
                    comment_post_id: comment_post_id
                }, function(xml) {
                    var $old = $('#comment-' + comment_id);
                    var $new = $.trim(wpAjax.parseAjaxResponse(xml).responses[0].data);
                    $old.after($new).remove();
                });
            });
        });
    </script><?php
});


/**
 * 修改后台显示的评论作者状态，加上【置顶】
 */
function add_admin_comment_author_state($author, $comment_ID, $comment) {
    if(is_admin()) {
        if(get_comment_meta($comment->comment_ID, 'is_sticky', true) === '1') {
            $author = '[顶]'.$author;
        }
    }
    return $author;
}
add_filter('get_comment_author', 'add_admin_comment_author_state', 10, 3);


/**
 * 移动端跳转
 * 如果当前 wp_is_mobile 为真
 * 而且路径渲染的模板文件存在 [mobile] 版本
 * 则使用 [mobile] 的模板文件）
 */
add_filter('template_include', function($template) {

    $mobile_template_file = preg_replace('/\\.php$/', '[mobile].php', $template);

    if(wp_is_mobile() && file_exists($mobile_template_file)) {
        return $mobile_template_file;
    }

    return $template;

}, 10, 1);


/**
 * 添加所有 post 类型的置顶功能
 */
add_filter('post_row_actions', function($actions, $post) {

    $new_actions = array();

    // 置顶/取消置顶
    if(get_post_meta($post->ID, 'is_sticky', true) === '1') {
        $new_actions['stick'] = '<a class="post-action-custom"
            href="javascript:;" data-action="unstick"
            data-post-type="'.$post->post_type.'"
            data-post-id="'.$post->ID.'">取消置顶</a>';
    } else {
        $new_actions['unstick'] = '<a class="post-action-custom"
            href="javascript:;" data-action="stick"
            data-post-id="'.$post->ID.'">置顶</a>';
    }

    // 在前边插入新增的两个动作链接并返回
    return array_merge($new_actions, $actions);

}, 10, 2);


/**
 * 客户端点击文章操作时的动作
 */
add_action('admin_footer', function () { ?>
    <script>
        jQuery(function($) {
            $('body').on('click', '.post-action-custom', function() {
                var post_id = parseInt($(this).data('post-id'));
                $.post(ajaxurl, {
                    action: 'post_action_custom',
                    action_post: $(this).data('action'),
                    post_type: $(this).data('post-type'),
                    post_ID: $(this).data('post-id')
                }, function(data) {
//                    console.log(data);
                    location.reload();
                });
            });
        });
    </script><?php
});


/**
 * ajax 处理新增的文章操作动作【置顶】
 */
add_action('wp_ajax_post_action_custom', function () {
    if(!empty($_REQUEST['action_post']) && !empty($_REQUEST['post_ID'])) {
        // 执行修改的动作
        $action_post = $_REQUEST['action_post'];
        $post_id = intval($_REQUEST['post_ID']);
        $sticky_posts = get_option('sticky_posts');
        switch($action_post) {
            case 'stick':
                update_post_meta($post_id, 'is_sticky', 1);
                $sticky_posts []= $post_id;
                break;
            case 'unstick':
                update_post_meta($post_id, 'is_sticky', 0);
                $key = array_search($post_id, $sticky_posts);
                array_splice($sticky_posts, $key,1);
                break;
        }
        update_option('sticky_posts', $sticky_posts);
        exit(0);
    }
});


/**
 * @param $id
 * 更新文章的时候根据置顶设置设定 is_sticky 值
 */
function set_stick_after_post_update($id) {
    update_post_meta($id, 'is_sticky', in_array($id, get_option( 'sticky_posts' )) ? 1 : 0);
}
add_action('post_updated', 'set_stick_after_post_update');


/**
 * @param $option
 * 更新置顶状态的时候刷新所有的 is_sticky 属性
 */
function update_sticky_posts($option, $old_val, $val) {
    if($option == 'sticky_posts') {
        global $wpdb;
        // 让所有的文章都支持 is_sticky
        $wpdb->query("
            insert into {$wpdb->postmeta} (post_id, meta_key, meta_value)
            select p.ID, 'is_sticky', 0 from {$wpdb->posts} p
            where p.post_type in ('post', 'member', 'coupon', 'goods', 'community')
                and not exists(
                    select * from {$wpdb->postmeta} pm
                    where pm.post_id = p.ID and pm.meta_key = 'is_sticky'
                );
            ");

        // 让所有文章的 is_sticky 为 0
        $wpdb->query("
            update {$wpdb->postmeta} set meta_value = 0
            where meta_key = 'is_sticky';");

        // 让置顶的文章 is_sticky 为 1
        $wpdb->query("
            update {$wpdb->postmeta} set meta_value = 1
            where post_id in (".(implode(',', get_option('sticky_posts'))).")
                and meta_key = 'is_sticky';
            ");

    }
}
add_action('updated_option', 'update_sticky_posts', 10, 3);


/**
 * 屏蔽头像
 */
add_filter('get_avatar', function () {
    return '<img src="'.get_template_directory_uri().'/images/avatar.jpg" />';
});
