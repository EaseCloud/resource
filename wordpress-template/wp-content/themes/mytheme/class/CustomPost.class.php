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

        // 需要调入闭包的变量
        add_action('init', function() use ($class) {

            register_post_type($class::$post_type, array(
                'label' => $class::$post_type_name,
                'description' => $class::$post_type_description,
                'labels' => array(
                    'name' => $class::$post_type_name,
                    'singular_name' => $class::$post_type_name,
                    'menu_name' => $class::$post_type_name,
                    'parent_item_colon'   => 'Parent '.$class::$post_type_name,
                    'all_items' => 'All '.$class::$post_type_name_plural,
                    'view_item' => 'View ',
                    'add_new_item' => 'Add '.$class::$post_type_name,
                    'add_new' => 'Add '.$class::$post_type_name,
                    'edit_item' => 'Edit '.$class::$post_type_name,
                    'update_item' => 'Update',
                    'search_items' => 'Search '.$class::$post_type_name,
                    'not_found' => 'There is no data here.',
                    'not_found_in_trash' => 'No items in trash.',
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


    /**
     * 获取当前 post 对象对应的所有 CustomTaxonomy 关联的对象
     * @param string $taxonomy 指定的 Taxonomy 别名或者类名
     * @return array 如果指定了 $taxonomy，返回一个 CustomTaxonomy 的纯数组
     * 如果没有指定，根据 $taxonomy 的别名分别指定数据返回
     */
    public function terms($taxonomy=null) {

        // CustomTaxonomy 子类分析
        $classes = array();
        foreach(get_declared_classes() as $cls) {
            // 找到所有 CustomTaxonomy 的子类
            if(is_subclass_of($cls, 'CustomTaxonomy')) {
                $tax = get_taxonomy($cls::$taxonomy);
                // 校验指定的 $post_type 是否与当前 taxonomy 关联
                if(!in_array(static::$post_type, $tax->object_type)) continue;
                // 如果指定了 $post_type，跳过所有不匹配的部分
                if($taxonomy && $taxonomy != $cls &&
                    $taxonomy != $cls::$taxonomy) continue;

                $classes []= $cls;
            }
        }

        // 如果指定了 $taxonomy，校验完整性
        assert(
            !$taxonomy || sizeof($taxonomy) === 1,
            '没有找到定义的关联 CustomTaxonomy 子类。'
        );

        // 逐个 post_type 进行查询
        $result = array();

        foreach($classes as $cls) {
            $result[$cls::$taxonomy] = array_map(function($term) use ($cls) {
                return new $cls($term);
            }, get_the_terms($this->post, $cls::$taxonomy)
            );
        }

        return $taxonomy ? $result[$classes[0]::$taxonomy] : $result;

    }

}


class CustomTaxonomy {

    // 配置选项（继承时必须填入这些属性）
    public static $taxonomy;  // tax 的别名
    public static $taxonomy_name;  // tax 的显示名称
    public static $taxonomy_name_plural;  // tax 的显示名称（复数）
    public static $post_types = array();  // tax 匹配的 post type

    // 实有成员
    public $term;

    /** 构造函数
     * @param $term
     */
    public function __construct($term) {

        // 构造 $term 对象
        if($term instanceof stdClass && $term->term_id) {
            // 直接使用 term 对象构造的情况
            $this->term = $term;
        } elseif (is_int($term)) {
            // 使用 term_ID 构造的情况
            $this->term = get_term($term, static::$taxonomy);
        } else {
            // 使用 slug 构造
            $this->term = get_term_by('slug', $term, static::$taxonomy);
        }

        assert(
            $this->term && $this->term->taxonomy == static::$taxonomy,
            '构造的 term 类型不符，应为 '.static::$taxonomy.' 类型的 term。'
        );

    }


    // 初始化脚本，完成 taxonomy 注册等工作，派生该类之后，如果需要使用必须手动先执行一次
    public static function init() {

        // 需要获取调用的子类
        $class = get_called_class();

        // 需要调入闭包的变量
        add_action('init', function() use ($class) {

            register_taxonomy($class::$taxonomy, $class::$post_types, array(
                'hierarchical' => true, // $class::$hierarchical,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => $class::$taxonomy),
                'labels' => array(
                    'name' => $class::$taxonomy_name,
                    'singular_name' => $class::$taxonomy_name,
                    'search_items' => 'Search '.$class::$taxonomy_name,
                    'all_items' => 'All '.$class::$taxonomy_name_plural,
                    'parent_item' => 'Parent '.$class::$taxonomy_name,
                    'parent_item_colon'   => 'Parent '.$class::$taxonomy_name,
                    'edit_item' => 'Edit '.$class::$taxonomy_name,
                    'update_item' => 'Update',
                    'add_new_item' => 'Add '.$class::$taxonomy_name,
                    'new_item_name' => 'Add '.$class::$taxonomy_name,
                    'menu_name' => $class::$taxonomy_name,
                ),
            ));

        }, 10, 0);

    }


    /**
     * 返回当前定义的 Taxonomy 的所有实例
     * @return CustomTaxonomy[]
     */
    public static function all() {
        $cls = get_called_class();
        return array_map(function($term) use ($cls) {
            return new $cls($term);
        }, get_terms($cls::$taxonomy, array(
            'hide_empty' => false,
        )));
    }


    /**
     * 获取当前分类法上面的所有匹配的 post
     * @param string $post_type 指定的 post 类名或者 post_type 名称
     * @return array 如果指定了 $post_type，返回一个对应的对象列表
     *      如果没有指定 $post_type，根据搜索到的 $post_type 返回一个关联所有分类的对象列表
     */
    public function posts($post_type=null) {

        $tax = get_taxonomy(static::$taxonomy);

        // CustomPost 子类分析
        $classes = array();
        foreach(get_declared_classes() as $cls) {
            // 找到所有 CustomPost 的子类
            if(is_subclass_of($cls, 'CustomPost')) {
                // 校验指定的 $post_type 是否与当前 taxonomy 关联
                if(!in_array($cls::$post_type, $tax->object_type)) continue;
                // 如果指定了 $post_type，跳过所有不匹配的部分
                if($post_type && $post_type != $cls &&
                    $post_type != $cls::$post_type) continue;

                $classes []= $cls;
            }
        }

        // 如果指定了 $post_type，校验完整性
        assert(
            !$post_type || sizeof($classes) === 1,
            '没有找到定义的关联 CustomPost 子类。'
        );

        // 逐个 post_type 进行查询
        $result = array();

        foreach($classes as $cls) {
            $result[$cls::$post_type] = array_map(function($post) use ($cls) {
                return new $cls($post);
            }, get_posts(array(
                'posts_per_page' => -1,
                'post_type' => $cls::$post_type,
                'tax_query' => array(
                    array(
                        'taxonomy' => static::$taxonomy,
                        'field' => 'id',
                        'terms' => $this->term->term_id,
                    )
                )
            )));
        }

        return $post_type ? $result[$classes[0]::$post_type] : $result;
    }


};


//// 调用范例，可写在 functions.php 中，或者另开文件
//class Customer extends CustomPost {
//    static $post_type = 'customer';
//    static $post_type_name = '客户';
//    static $menu_icon = 'dashicons-welcome-learn-more';
//};
//Customer::init();
