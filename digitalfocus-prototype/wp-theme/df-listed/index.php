<?php
/**
 * Fallback for archives and search.
 */
get_header();
?>
<section class="phero">
  <div class="phero-bg"><img src="<?php echo esc_url(DF_URI . '/assets/img/hq.jpg'); ?>" alt=""></div>
  <div class="wrap phero-in"><h1><?php echo is_search() ? esc_html(sprintf('ผลการค้นหา: %s', get_search_query())) : esc_html(wp_strip_all_tags(get_the_archive_title())); ?></h1></div>
</section>
<section class="sec">
  <div class="wrap">
    <?php if (have_posts()) : ?>
    <div class="gal">
      <?php while (have_posts()) : the_post(); ?>
      <figure><a href="<?php the_permalink(); ?>"><div class="im"><?php the_post_thumbnail('medium_large', ['loading' => 'lazy']); ?></div><figcaption><span><?php echo esc_html(get_the_date()); ?></span><?php the_title(); ?></figcaption></a></figure>
      <?php endwhile; ?>
    </div>
    <div class="pager"><?php echo paginate_links(); ?></div>
    <?php else : ?>
    <p>ไม่พบเนื้อหา</p>
    <?php endif; ?>
  </div>
</section>
<?php
get_footer();
