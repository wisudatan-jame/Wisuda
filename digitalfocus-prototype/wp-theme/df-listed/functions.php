<?php
/**
 * DF Listed theme bootstrap.
 */

defined('ABSPATH') || exit;

define('DF_VERSION', wp_get_theme(get_template())->get('Version'));
define('DF_DIR', get_template_directory());
define('DF_URI', get_template_directory_uri());

require DF_DIR . '/inc/helpers.php';
require DF_DIR . '/inc/customizer.php';
require DF_DIR . '/inc/leads.php';
require DF_DIR . '/inc/seed.php';
require DF_DIR . '/inc/redirects.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_post_type_support('page', ['excerpt', 'custom-fields']);
    register_nav_menus([
        'primary_th' => 'เมนูหลัก (ไทย)',
        'primary_en' => 'Main menu (English)',
        'mobile_th'  => 'เมนูมือถือ (ไทย)',
        'mobile_en'  => 'Mobile menu (English)',
    ]);
});

add_action('wp_enqueue_scripts', function () {
    $palette = get_theme_mod('df_palette', 'a') === 'b' ? 'b' : 'a';
    wp_enqueue_style('df-fonts', 'https://fonts.googleapis.com/css2?family=Anuphan:wght@500;600;700&family=IBM+Plex+Sans+Thai:wght@400;500;600&display=swap', [], null);
    wp_enqueue_style('df-site', DF_URI . "/assets/css/site-{$palette}.css", ['df-fonts'], DF_VERSION);
    wp_enqueue_script('df-site', DF_URI . '/assets/js/site.js', [], DF_VERSION, true);
    wp_localize_script('df-site', 'DF', [
        'ajax'     => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('df_lead'),
        'lang'     => df_lang(),
        'channels' => df_channels(),
        'cookie'   => (bool) get_theme_mod('df_cookie_banner', true),
        'i18n'     => [
            'required' => df_t('กรุณากรอกข้อมูลที่มีเครื่องหมาย * ให้ครบและถูกต้อง', 'Please complete all fields marked * correctly'),
            'consent'  => df_t('กรุณายินยอมการเก็บข้อมูลก่อนส่งใบสมัคร', 'Please consent to data collection before submitting your application'),
            'sent'     => df_t('ได้รับใบสมัครแล้ว ทีมพัฒนาธุรกิจจะติดต่อกลับภายใน 3 วันทำการ', 'Application received. Our business development team will contact you within 3 business days.'),
            'irSent'   => df_t('ลงทะเบียนรับข่าวสารเรียบร้อยแล้ว', 'You are now subscribed to investor news.'),
            'error'    => df_t('ส่งข้อมูลไม่สำเร็จ กรุณาลองใหม่ หรือติดต่อ info@digitalfocus.co.th', 'Could not send. Please try again or email info@digitalfocus.co.th'),
            'menuOpen' => df_t('เปิดเมนู', 'Open menu'),
            'menuClose'=> df_t('ปิดเมนู', 'Close menu'),
        ],
    ]);
});

add_filter('body_class', function ($classes) {
    $classes[] = 'df-lang-' . df_lang();
    $key = df_page_key();
    if ($key) {
        $classes[] = 'df-page-' . $key;
    }
    return $classes;
});

add_filter('language_attributes', function ($output) {
    return preg_replace('/lang="[^"]*"/', 'lang="' . (df_lang() === 'en' ? 'en' : 'th') . '"', $output);
});

/* Seeded page content uses {{img}} and {{url:key}} placeholders so it survives a domain change. */
add_filter('the_content', 'df_expand_placeholders', 1);

/* Keep the seeded HTML exactly as written. */
add_filter('the_content', function ($content) {
    if (is_page() && get_post_meta(get_the_ID(), '_df_key', true)) {
        remove_filter('the_content', 'wpautop');
    }
    return $content;
}, 0);
