<?php
/**
 * One-click starter content: Appearance → ตั้งค่าเนื้อหาเริ่มต้น.
 *
 * Creates the Thai and English pages from data/pages.json, the news posts from
 * data/news-th.json, the four menus, the front page and posts page, and pretty permalinks.
 * Safe to run again: anything that already exists is left alone.
 */

defined('ABSPATH') || exit;

add_action('admin_menu', function () {
    add_theme_page('ตั้งค่าเนื้อหาเริ่มต้น', 'ตั้งค่าเนื้อหาเริ่มต้น', 'manage_options', 'df-seed', 'df_seed_screen');
});

add_action('admin_notices', function () {
    if (!current_user_can('manage_options') || get_option('df_seeded') || (isset($_GET['page']) && $_GET['page'] === 'df-seed')) {
        return;
    }
    printf('<div class="notice notice-info"><p>ธีม DF Listed: <a href="%s">สร้างหน้าเว็บและเมนูเริ่มต้น</a> ก่อนใช้งาน</p></div>', esc_url(admin_url('themes.php?page=df-seed')));
});

function df_seed_screen() {
    $log = null;
    if (isset($_POST['df_seed']) && check_admin_referer('df_seed')) {
        $log = df_seed(!empty($_POST['df_retire']));
    }
    echo '<div class="wrap"><h1>ตั้งค่าเนื้อหาเริ่มต้น</h1>';
    echo '<p>สร้างหน้าภาษาไทยและอังกฤษ ข่าว เมนู หน้าแรก และรูปแบบลิงก์ ตามดีไซน์ต้นแบบ หน้าที่มีอยู่แล้วจะไม่ถูกเขียนทับ</p>';
    if ($log) {
        echo '<div class="notice notice-success"><p>' . implode('<br>', array_map('esc_html', $log)) . '</p></div>';
    }
    echo '<form method="post">';
    wp_nonce_field('df_seed');
    echo '<p><label><input type="checkbox" name="df_retire" value="1"> ย้ายหน้าเดิมที่ใช้ชื่อลิงก์เดียวกัน (เช่น about, contact, news, home) ไปเป็นฉบับร่าง และเปลี่ยนชื่อลิงก์เป็น -old เพื่อให้หน้าใหม่ใช้ลิงก์เดิมได้</label></p>';
    submit_button('สร้างเนื้อหาเริ่มต้น', 'primary', 'df_seed');
    echo '</form></div>';
}

function df_seed($retire_old = false) {
    $log = [];
    $GLOBALS['df_retire_old'] = $retire_old;
    $data = json_decode(file_get_contents(DF_DIR . '/data/pages.json'), true);
    kses_remove_filters();

    $ids = ['th' => [], 'en' => []];
    foreach (['th', 'en'] as $lang) {
        // English home is the parent of every English page, so URLs read /en/about/.
        $parent = 0;
        foreach ($data[$lang] as $page) {
            if ($page['key'] !== 'home') {
                continue;
            }
            $ids[$lang]['home'] = df_seed_page($page, $lang, 0, $lang === 'en' ? 'en' : 'home', $log);
            $parent = $lang === 'en' ? $ids[$lang]['home'] : 0;
        }
        foreach ($data[$lang] as $page) {
            if ($page['key'] === 'home') {
                continue;
            }
            $ids[$lang][$page['key']] = df_seed_page($page, $lang, $parent, $page['slug'], $log);
        }
    }

    // Old pages replaced by the new design go to draft, so their addresses redirect (inc/redirects.php).
    if ($retire_old) {
        foreach (array_keys(df_old_paths()) as $old_slug) {
            $old = get_page_by_path($old_slug, OBJECT, 'page');
            if ($old && !get_post_meta($old->ID, '_df_key', true) && $old->post_status === 'publish') {
                wp_update_post(['ID' => $old->ID, 'post_name' => $old_slug . '-old', 'post_status' => 'draft']);
                $log[] = "ย้ายหน้าเดิม \"{$old->post_title}\" เป็นฉบับร่าง ({$old_slug}-old)";
            }
        }
    }

    update_option('show_on_front', 'page');
    update_option('page_on_front', $ids['th']['home']);
    update_option('page_for_posts', $ids['th']['news']);
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%postname%/');
    }

    df_seed_news($log);
    df_seed_menus($ids, $log);

    kses_init_filters();
    flush_rewrite_rules();
    update_option('df_seeded', DF_VERSION);
    $log[] = 'เสร็จแล้ว เปิดดูหน้าเว็บได้เลย';
    return $log;
}

function df_seed_page($page, $lang, $parent, $slug, &$log) {
    $existing = df_page_id($page['key'], $lang);
    if ($existing) {
        $log[] = "มีอยู่แล้ว: {$page['title']} ({$lang})";
        return $existing;
    }
    if (!empty($GLOBALS['df_retire_old']) && !$parent) {
        $old = get_page_by_path($slug, OBJECT, 'page');
        if ($old && !get_post_meta($old->ID, '_df_key', true)) {
            wp_update_post(['ID' => $old->ID, 'post_name' => $slug . '-old', 'post_status' => 'draft']);
            $log[] = "ย้ายหน้าเดิม \"{$old->post_title}\" เป็นฉบับร่าง ({$slug}-old)";
        }
    }
    $id = wp_insert_post(wp_slash([
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => $page['title'],
        'post_name'    => $slug,
        'post_parent'  => $parent,
        'post_content' => $page['content'],
        'post_excerpt' => $page['hero_lead'],
    ]));
    update_post_meta($id, '_df_key', $page['key']);
    update_post_meta($id, '_df_lang', $lang);
    update_post_meta($id, '_df_hero_title', $page['hero_title']);
    update_post_meta($id, '_df_hero_lead', $page['hero_lead']);
    update_post_meta($id, '_df_hero_lead_style', $page['hero_lead_style'] ? 1 : 0);
    $log[] = "สร้างหน้า: {$page['title']} ({$lang})";
    return $id;
}

function df_seed_news(&$log) {
    if (get_posts(['post_type' => 'post', 'numberposts' => 1, 'meta_key' => '_df_seeded', 'fields' => 'ids'])) {
        $log[] = 'มีข่าวตัวอย่างอยู่แล้ว';
        return;
    }
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $news = json_decode(file_get_contents(DF_DIR . '/data/news-th.json'), true);
    foreach ($news as $item) {
        $id = wp_insert_post(wp_slash([
            'post_type'    => 'post',
            'post_status'  => 'publish',
            'post_title'   => $item['title'],
            'post_excerpt' => $item['excerpt'],
            'post_content' => $item['content'],
            'post_date'    => $item['date'],
        ]));
        update_post_meta($id, '_df_seeded', 1);
        $att = df_seed_image($item['img'], $id);
        if ($att) {
            set_post_thumbnail($id, $att);
        }
        if (!empty($item['sticky'])) {
            stick_post($id);
        }
    }
    $log[] = 'สร้างข่าว ' . count($news) . ' รายการ';
}

function df_seed_image($file, $parent) {
    $path = DF_DIR . '/assets/img/' . basename($file);
    if (!file_exists($path)) {
        return 0;
    }
    $up = wp_upload_bits(basename($file), null, file_get_contents($path));
    if (!empty($up['error'])) {
        return 0;
    }
    $att = wp_insert_attachment(['post_mime_type' => wp_check_filetype($up['file'])['type'], 'post_title' => pathinfo($file, PATHINFO_FILENAME), 'post_status' => 'inherit'], $up['file'], $parent);
    wp_update_attachment_metadata($att, wp_generate_attachment_metadata($att, $up['file']));
    return $att;
}

function df_seed_menus($ids, &$log) {
    $labels = [
        'th' => ['home' => 'หน้าแรก', 'about' => 'เกี่ยวกับเรา', 'business' => 'ธุรกิจของเรา', 'solutions' => 'โซลูชัน', 'brands' => 'ผลิตภัณฑ์', 'projects' => 'ผลงานและลูกค้า', 'esg' => 'ความยั่งยืน', 'news' => 'ข่าวสาร', 'contact' => 'ติดต่อเรา'],
        'en' => ['home' => 'Home', 'about' => 'About Us', 'business' => 'Our Business', 'solutions' => 'Solutions', 'brands' => 'Products', 'projects' => 'Projects & Clients', 'esg' => 'Sustainability', 'news' => 'News', 'contact' => 'Contact Us'],
    ];
    $locations = get_theme_mod('nav_menu_locations', []);
    foreach (['th' => ['primary_th' => 'เมนูหลัก (ไทย)', 'mobile_th' => 'เมนูมือถือ (ไทย)'], 'en' => ['primary_en' => 'Main menu (English)', 'mobile_en' => 'Mobile menu (English)']] as $lang => $menus) {
        foreach ($menus as $location => $name) {
            if (!empty($locations[$location]) && wp_get_nav_menu_object($locations[$location])) {
                continue;
            }
            $menu_id = wp_get_nav_menu_object($name) ? wp_get_nav_menu_object($name)->term_id : wp_create_nav_menu($name);
            $mobile = strpos($location, 'mobile') === 0;
            foreach ($labels[$lang] as $key => $label) {
                if ($key === 'news' && !$mobile) {
                    continue;
                }
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title'     => $label,
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => $ids[$lang][$key],
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                ]);
            }
            $locations[$location] = $menu_id;
            $log[] = "สร้างเมนู: {$name}";
        }
    }
    set_theme_mod('nav_menu_locations', $locations);
}
