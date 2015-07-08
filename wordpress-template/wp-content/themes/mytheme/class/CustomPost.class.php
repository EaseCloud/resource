<?php

/**
 * 自定义类，派生使用，派生时需要写入 static 参数进行配置。
 * 然后需要调用子类的 init 方法。
 */
class CustomPost {

    // 配置选项（继承时必须填入这些属性）
    public static $post_type;
    public static $post_type_name;
    public static $post_type_description = '';
    public static $post_type_supports =
        array('title', 'thumbnail', 'excerpt', 'editor', 'comments');
    public static $menu_icon = 'dashicons-admin-post';

    // 实有成员
    public $post;

    /** 构造函数
     * @param $post
     */
    public function __construct($post) {

        // 构造 $post 对象
        if($post instanceof WP_Post) {
            // 直接使用 post 对象构造的情况
            $this->post = $post;
        } else {
            // 使用 post_ID 构造的情况
            $this->post = get_post($post);
        }

        // 校验 $post 的类型
        assert(
            $this->post && $this->post->post_type == static::$post_type,
            '构造的 post 类型不符，应为 '.static::$post_type.' 类型的 post。'
        );

    }

    // 执行动态属性的读写为 post_meta 的读写
    public function __get($key) {
        return get_post_meta($this->post->ID, $key, true);
    }

    // 执行动态属性的读写为 post_meta 的读写
    public function __set($key, $val) {
        update_post_meta($this->post->ID, $key, $val);
    }

    // 获取当前商品的特色图像，如果没有设置返回默认图。
    public function getThumbnailUrl() {
        $img_id = get_post_thumbnail_id($this->post->ID);
        if(!$img_id) return get_template_directory_uri().'/images/thumbnail.jpg';
        $img = wp_get_attachment_image_src($img_id, 'full');
        return $img[0];
    }

    // 初始化脚本，完成 post_type 注册等工作，派生该类之后，如果需要使用必须手动先执行一次
    public static function init() {

        // 需要获取调用的子类
        $class = get_called_class();

        // 注册会员商户【Member】相关的 PostType
        // 需要调入闭包的变量
        add_action('init', function() use ($class) {

            // 1. 会员商户
            register_post_type($class::$post_type, array(
                'label' => $class::$post_type_name,
                'description' => $class::$post_type_description,
                'labels' => array(
                    'name' => $class::$post_type_name,
                    'singular_name' => $class::$post_type_name,
                    'menu_name' => $class::$post_type_name,
                    'parent_item_colon'   => '父'.$class::$post_type_name,
                    'all_items' => '所有'.$class::$post_type_name,
                    'view_item' => '查看',
                    'add_new_item' => '新增'.$class::$post_type_name,
                    'add_new' => '添加'.$class::$post_type_name,
                    'edit_item' => '编辑'.$class::$post_type_name,
                    'update_item' => '更新',
                    'search_items' => '查找'.$class::$post_type_name,
                    'not_found' => '没有任何数据',
                    'not_found_in_trash' => '回收站没有东西哦',
                ),
                'supports' => $class::$post_type_supports,
//                'taxonomies' => array('region', 'industry'),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_in_admin_bar' => true,
                'menu_position' => 5,
                'can_export' => true,
                'has_archive' => true,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'menu_icon' => $class::$menu_icon,
                'rewrite' => array(
                    'slug' => $class::$post_type,
                    'with_front' => true,
                ),
            ));

        }, 10, 0);

    }

}


//// 调用范例，可写在 functions.php 中，或者另开文件
//class Customer extends CustomPost {
//    static $post_type = 'customer';
//    static $post_type_name = '客户';
//    static $menu_icon = 'dashicons-welcome-learn-more';
//};
//Customer::init();