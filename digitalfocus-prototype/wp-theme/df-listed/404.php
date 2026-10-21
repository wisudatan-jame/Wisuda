<?php
get_header();
?>
<section class="phero">
  <div class="phero-bg"><img src="<?php echo esc_url(DF_URI . '/assets/img/hq.jpg'); ?>" alt=""></div>
  <div class="wrap phero-in">
    <h1><?php echo esc_html(df_t('ไม่พบหน้าที่ต้องการ', 'Page not found')); ?></h1>
    <p><?php echo esc_html(df_t('หน้านี้อาจถูกย้ายหรือลบไปแล้ว', 'This page may have been moved or removed.')); ?></p>
  </div>
</section>
<section class="sec">
  <div class="wrap"><a class="btn btn-p" href="<?php echo esc_url(df_url('home')); ?>"><?php echo esc_html(df_t('กลับหน้าแรก', 'Back to Home')); ?> <?php echo df_icon('arrow'); ?></a></div>
</section>
<?php
get_footer();
