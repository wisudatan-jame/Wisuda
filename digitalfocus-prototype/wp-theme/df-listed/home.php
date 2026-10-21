<?php
/**
 * News (posts page). The sticky post leads as a large card; the rest form the grid.
 * Anything written in the News page itself (the call-to-action band) follows the list.
 */
get_header();
$news_page = (int) get_option('page_for_posts');
df_page_hero($news_page);
$sticky = get_option('sticky_posts');
$paged = max(1, get_query_var('paged'));
?>
<section class="sec" id="news">
  <div class="wrap">
    <div class="head"><span class="kick">ห้องข่าว</span><h2>ข่าวสารและกิจกรรม</h2></div>
    <?php
    if ($paged === 1 && $sticky) :
        $lead = new WP_Query(['post__in' => $sticky, 'posts_per_page' => 1, 'ignore_sticky_posts' => true]);
        while ($lead->have_posts()) :
            $lead->the_post();
            ?>
    <div class="proj">
      <div class="im"><?php the_post_thumbnail('large', ['loading' => 'lazy']); ?></div>
      <div class="bd">
        <span class="pill">ข่าวเด่น · <?php echo esc_html(get_the_date('Y')); ?></span>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <p class="sub"><?php echo esc_html(get_the_date()); ?></p>
        <p><?php echo esc_html(get_the_excerpt()); ?></p>
        <a class="more" href="<?php the_permalink(); ?>">อ่านต่อ <?php echo df_icon('arrow'); ?></a>
      </div>
    </div>
            <?php
        endwhile;
        wp_reset_postdata();
    endif;

    $list = new WP_Query(['post__not_in' => $paged === 1 ? $sticky : [], 'posts_per_page' => 8, 'paged' => $paged, 'ignore_sticky_posts' => true]);
    if ($list->have_posts()) :
        ?>
    <div class="gal" style="margin-top:24px">
        <?php
        while ($list->have_posts()) :
            $list->the_post();
            ?>
      <figure><a href="<?php the_permalink(); ?>"><div class="im"><?php the_post_thumbnail('medium_large', ['loading' => 'lazy']); ?></div><figcaption><span><?php echo esc_html(get_the_date()); ?></span><?php the_title(); ?></figcaption></a></figure>
            <?php
        endwhile;
        ?>
    </div>
    <div class="pager">
        <?php
        echo paginate_links(['total' => $list->max_num_pages, 'current' => $paged, 'prev_text' => '‹', 'next_text' => '›']);
        ?>
    </div>
        <?php
        wp_reset_postdata();
    endif;
    ?>
  </div>
</section>
<?php
if ($news_page) {
    $page = get_post($news_page);
    echo apply_filters('the_content', $page->post_content);
}
get_footer();
