<?php
/**
 * A single news post.
 */
get_header();
while (have_posts()) :
    the_post();
    ?>
<section class="phero">
  <div class="phero-bg"><img src="<?php echo esc_url(DF_URI . '/assets/img/hq.jpg'); ?>" alt=""></div>
  <div class="wrap phero-in">
    <p class="crumb"><a href="<?php echo esc_url(df_url('home')); ?>">หน้าแรก</a> / <a href="<?php echo esc_url(df_url('news')); ?>">ข่าวสาร</a></p>
    <h1><?php the_title(); ?></h1>
  </div>
</section>
<section class="sec">
  <div class="wrap entry">
    <p class="post-meta"><?php echo esc_html(get_the_date()); ?></p>
    <?php if (has_post_thumbnail()) : ?><div class="frame" style="margin-bottom:28px"><?php the_post_thumbnail('large'); ?></div><?php endif; ?>
    <?php the_content(); ?>
    <p style="margin-top:32px"><a class="btn btn-o" href="<?php echo esc_url(df_url('news')); ?>">← ข่าวทั้งหมด</a></p>
  </div>
</section>
    <?php
endwhile;
get_footer();
