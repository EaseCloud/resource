<?php
// 加密的 post 不显示评论块
if (post_password_required()) return;
?>

<div id="comments" class="comments-area">

    <?php if(have_comments()) { ?>

        <!-- 当前评论列表 -->
        <h2 class="comments-title">
            共有<?php echo get_comments_number();?>个评论
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 56,
            ) );
            ?>
        </ol><!-- .comment-list -->

        <?php if(get_comment_pages_count() > 1 && get_option( 'page_comments' )) {?>
            <nav class="navigation comment-navigation" role="navigation">
                <h2>评论导航：</h2>
                <div class="nav-links">

                    <?php if($prev_link = get_previous_comments_link('上一页：')) {?>
                        <div class="nav-previous"><?php echo $prev_link; ?></div>
                    <?php }?>

                    <?php if($next_link = get_next_comments_link('下一页：')) {?>
                        <div class="nav-next"><?php echo $next_link; ?></div>
                    <?php }?>

                    <?php paginate_comments_links(); // 评论翻页 ?>

                </div><!-- .nav-links -->
            </nav><!-- .comment-navigation -->
        <?php }?>


    <?php } // have_comments() ?>

    <?php if (!comments_open() && get_comments_number() &&
        post_type_supports(get_post_type(), 'comments')) {?>
        <!-- 如果现在存在评论，但是评论功能被关闭，显示这句提示 -->
        <p class="no-comments">评论功能已经被管理员关闭。</p>
    <?php } ?>

    <?php comment_form(); ?>

</div><!-- .comments-area -->
