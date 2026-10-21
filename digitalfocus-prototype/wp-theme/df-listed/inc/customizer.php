<?php
/**
 * Appearance → Customize → "Digital Focus": colour option, contact details, channels, forms.
 */

defined('ABSPATH') || exit;

add_action('customize_register', function (WP_Customize_Manager $c) {
    $c->add_panel('df', ['title' => 'Digital Focus', 'priority' => 30]);

    $c->add_section('df_look', ['title' => 'โทนสี', 'panel' => 'df']);
    $c->add_setting('df_palette', ['default' => 'b', 'sanitize_callback' => function ($v) {
        return $v === 'a' ? 'a' : 'b';
    }]);
    $c->add_control('df_palette', [
        'label'   => 'โทนสีของเว็บไซต์',
        'section' => 'df_look',
        'type'    => 'radio',
        'choices' => ['b' => 'แบบ B · น้ำเงินครามและแดง (ผู้บริหารเลือก)', 'a' => 'แบบ A · น้ำเงินกรมท่า'],
    ]);

    $c->add_section('df_contact', ['title' => 'ข้อมูลติดต่อและปุ่มแชท', 'panel' => 'df']);
    $text = [
        'df_phone' => ['เบอร์โทร', '02 136 8989'],
        'df_email' => ['อีเมลบริษัท', 'info@digitalfocus.co.th'],
        'df_line'  => ['ลิงก์ LINE Official', 'https://lin.ee/Ajw1OZw'],
        'df_msgr'  => ['ลิงก์ Facebook Messenger', 'https://m.me/61581609465585'],
    ];
    foreach ($text as $id => [$label, $default]) {
        $c->add_setting($id, ['default' => $default, 'sanitize_callback' => strpos($id, 'email') ? 'sanitize_email' : (in_array($id, ['df_line', 'df_msgr'], true) ? 'esc_url_raw' : 'sanitize_text_field')]);
        $c->add_control($id, ['label' => $label, 'section' => 'df_contact']);
    }

    $c->add_section('df_social', ['title' => 'ช่องทางออนไลน์', 'panel' => 'df']);
    $links = [
        'df_fb'      => ['Facebook', 'https://www.facebook.com/profile.php?id=61581609465585'],
        'df_shopee'  => ['Shopee', 'https://shopee.co.th/digitalfocus_thailand'],
        'df_ig'      => ['Instagram', 'https://www.instagram.com/brainco.digitalfocus'],
        'df_tiktok'  => ['TikTok', 'https://www.tiktok.com/@brainco.by.digita'],
        'df_youtube' => ['YouTube', 'https://www.youtube.com/channel/UCzA12znBYsygJ5KW2JYhG2g'],
    ];
    foreach ($links as $id => [$label, $default]) {
        $c->add_setting($id, ['default' => $default, 'sanitize_callback' => 'esc_url_raw']);
        $c->add_control($id, ['label' => $label . ' (เว้นว่างเพื่อซ่อน)', 'section' => 'df_social', 'type' => 'url']);
    }

    $c->add_section('df_forms', ['title' => 'ฟอร์มและคุกกี้', 'panel' => 'df']);
    $c->add_setting('df_lead_to', ['default' => 'info@digitalfocus.co.th', 'sanitize_callback' => 'sanitize_email']);
    $c->add_control('df_lead_to', ['label' => 'อีเมลรับใบสมัครพันธมิตรและผู้สมัครรับข่าวนักลงทุน', 'section' => 'df_forms', 'type' => 'email']);
    $c->add_setting('df_cookie_banner', ['default' => true, 'sanitize_callback' => 'rest_sanitize_boolean']);
    $c->add_control('df_cookie_banner', ['label' => 'แสดงแบนเนอร์คุกกี้ของธีม (ปิดถ้าใช้ปลั๊กอิน PDPA แทน)', 'section' => 'df_forms', 'type' => 'checkbox']);
});
