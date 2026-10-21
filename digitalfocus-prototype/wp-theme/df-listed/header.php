<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#top"><?php echo esc_html(df_t('ข้ามไปยังเนื้อหา', 'Skip to content')); ?></a>
<header class="hdr" id="hdr">
  <div class="wrap hdr-row">
    <a class="brand" href="<?php echo esc_url(df_url('home')); ?>" aria-label="<?php echo esc_attr(df_t('Digital Focus หน้าแรก', 'Digital Focus Home')); ?>"><img src="<?php echo esc_url(DF_URI . '/assets/img/logo.png'); ?>" alt="Digital Focus" width="773" height="347"></a>
    <nav class="nav" aria-label="<?php echo esc_attr(df_t('เมนูหลัก', 'Main menu')); ?>"><?php df_menu('primary'); ?></nav>
    <div class="hdr-side">
      <a class="btn btn-p hdr-cta" href="<?php echo esc_url(df_url('contact') . '#ir'); ?>"><?php echo esc_html(df_t('นักลงทุนสัมพันธ์', 'Investor Relations')); ?></a>
      <button class="burger" id="burger" type="button" aria-expanded="false" aria-controls="mnav" aria-label="<?php echo esc_attr(df_t('เปิดเมนู', 'Open menu')); ?>"><i></i></button>
      <div class="lang">
        <?php $is_en = df_lang() === 'en'; ?>
        <a<?php echo $is_en ? '' : ' class="on" aria-current="true"'; ?> href="<?php echo esc_url(df_switch_url('th')); ?>" hreflang="th" lang="th">TH</a><a<?php echo $is_en ? ' class="on" aria-current="true"' : ''; ?> href="<?php echo esc_url(df_switch_url('en')); ?>" hreflang="en" lang="en">EN</a>
      </div>
    </div>
  </div>
  <nav class="mnav" id="mnav" aria-label="<?php echo esc_attr(df_t('เมนู', 'Menu')); ?>" hidden><div class="wrap"><?php df_menu('mobile', true); ?></div></nav>
</header>

<main id="top">
