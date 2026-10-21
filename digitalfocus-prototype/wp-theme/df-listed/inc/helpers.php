<?php
/**
 * Language, URL and placeholder helpers.
 *
 * Thai pages sit at the site root (/about/), English pages under the "en" page (/en/about/).
 * Each seeded page stores _df_key (home, about, …) and _df_lang (th|en).
 */

defined('ABSPATH') || exit;

/** Page slugs by key. */
function df_slugs() {
    return [
        'home' => '', 'about' => 'about', 'business' => 'business', 'solutions' => 'solutions',
        'brands' => 'products', 'projects' => 'projects', 'esg' => 'sustainability',
        'news' => 'news', 'contact' => 'contact',
    ];
}

/** Current language: "en" on the English page tree, otherwise "th". */
function df_lang() {
    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }
    $lang = 'th';
    $id = get_queried_object_id();
    if ($id && is_page()) {
        $lang = get_post_meta($id, '_df_lang', true) === 'en' ? 'en' : 'th';
    }
    return $lang;
}

function df_t($th, $en) {
    return df_lang() === 'en' ? $en : $th;
}

/** Key (home, about, …) of the current page, or "news" on the posts page. */
function df_page_key() {
    if (is_home()) {
        return 'news';
    }
    if (is_front_page()) {
        return 'home';
    }
    if (is_page()) {
        return (string) get_post_meta(get_queried_object_id(), '_df_key', true);
    }
    return '';
}

/** Find the seeded page for a key and language. */
function df_page_id($key, $lang = null) {
    $lang = $lang ?: df_lang();
    $found = get_posts([
        'post_type'   => 'page',
        'post_status' => 'publish',
        'numberposts' => 1,
        'fields'      => 'ids',
        'meta_query'  => [
            ['key' => '_df_key', 'value' => $key],
            ['key' => '_df_lang', 'value' => $lang],
        ],
    ]);
    return $found ? (int) $found[0] : 0;
}

/** URL of a page by key, in the current language unless one is given. */
function df_url($key, $lang = null) {
    $lang = $lang ?: df_lang();
    if ($key === 'home' && $lang === 'th') {
        return home_url('/');
    }
    if ($key === 'news' && $lang === 'th' && get_option('page_for_posts')) {
        return get_permalink((int) get_option('page_for_posts'));
    }
    $id = df_page_id($key, $lang);
    if ($id) {
        return get_permalink($id);
    }
    $slug = df_slugs()[$key] ?? '';
    return home_url('/' . ($lang === 'en' ? 'en/' : '') . ($slug ? $slug . '/' : ''));
}

/** URL of the same page in the other language, for the TH / EN switch. */
function df_switch_url($to) {
    $key = df_page_key() ?: 'home';
    if (is_singular('post')) {
        $key = 'news';
    }
    return df_url($key, $to);
}

/** Replace {{img}} and {{url:key}} placeholders in seeded HTML. */
function df_expand_placeholders($html) {
    if (strpos($html, '{{') === false) {
        return $html;
    }
    $lang = 'th';
    if (in_the_loop() || is_singular()) {
        $lang = get_post_meta(get_the_ID(), '_df_lang', true) === 'en' ? 'en' : 'th';
    }
    $html = str_replace('{{img}}', esc_url(DF_URI . '/assets/img'), $html);
    return preg_replace_callback('/\{\{url:([a-z-]+)\}\}/', function ($m) use ($lang) {
        return esc_url(df_url($m[1], $lang));
    }, $html);
}

/** Online channels shown on the contact page, footer icons and in scripts. */
function df_channels() {
    $th = df_lang() !== 'en';
    $list = [
        ['web', $th ? 'เว็บไซต์' : 'Website', 'www.digitalfocus.co.th', home_url('/')],
        ['fb', 'Facebook', 'Digital Focus', get_theme_mod('df_fb', 'https://www.facebook.com/profile.php?id=61581609465585')],
        ['line', 'LINE Official', 'lin.ee/Ajw1OZw', get_theme_mod('df_line', 'https://lin.ee/Ajw1OZw')],
        ['shop', 'Shopee', 'digitalfocus_thailand', get_theme_mod('df_shopee', 'https://shopee.co.th/digitalfocus_thailand')],
        ['ig', 'Instagram', 'brainco.digitalfocus', get_theme_mod('df_ig', 'https://www.instagram.com/brainco.digitalfocus')],
        ['tt', 'TikTok', '@brainco.by.digita', get_theme_mod('df_tiktok', 'https://www.tiktok.com/@brainco.by.digita')],
        ['yt', 'YouTube', 'Digital Focus', get_theme_mod('df_youtube', 'https://www.youtube.com/channel/UCzA12znBYsygJ5KW2JYhG2g')],
    ];
    return array_values(array_filter($list, function ($c) {
        return !empty($c[3]);
    }));
}

/** Contact settings with prototype defaults. */
function df_contact($field) {
    $defaults = [
        'phone'   => '02 136 8989',
        'email'   => 'info@digitalfocus.co.th',
        'line'    => 'https://lin.ee/Ajw1OZw',
        'msgr'    => 'https://m.me/61581609465585',
    ];
    return get_theme_mod('df_' . $field, $defaults[$field] ?? '');
}

function df_tel_href($phone) {
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

/** Menu with a fallback to the seeded pages, so the header works before menus are set up. */
function df_menu($location_base, $mobile = false) {
    $location = $location_base . '_' . df_lang();
    if (has_nav_menu($location)) {
        wp_nav_menu([
            'theme_location' => $location,
            'container'      => false,
            'menu_class'     => '',
            'items_wrap'     => '<ul>%3$s</ul>',
            'depth'          => 1,
        ]);
        return;
    }
    $keys = ['home', 'about', 'business', 'solutions', 'brands', 'projects', 'esg'];
    if ($mobile) {
        $keys[] = 'news';
    }
    $keys[] = 'contact';
    $labels = df_nav_labels();
    $current = df_page_key();
    echo '<ul>';
    foreach ($keys as $k) {
        $cls = $k === $current ? ' class="current-menu-item"' : '';
        printf('<li%s><a href="%s">%s</a></li>', $cls, esc_url(df_url($k)), esc_html($labels[$k]));
    }
    echo '</ul>';
}

function df_nav_labels() {
    return df_lang() === 'en'
        ? ['home' => 'Home', 'about' => 'About Us', 'business' => 'Our Business', 'solutions' => 'Solutions', 'brands' => 'Products', 'projects' => 'Projects & Clients', 'esg' => 'Sustainability', 'news' => 'News', 'contact' => 'Contact Us']
        : ['home' => 'หน้าแรก', 'about' => 'เกี่ยวกับเรา', 'business' => 'ธุรกิจของเรา', 'solutions' => 'โซลูชัน', 'brands' => 'ผลิตภัณฑ์', 'projects' => 'ผลงานและลูกค้า', 'esg' => 'ความยั่งยืน', 'news' => 'ข่าวสาร', 'contact' => 'ติดต่อเรา'];
}

/** Inner-page hero from page meta (falls back to the page title). */
function df_page_hero($post_id, $crumb = null) {
    $title = get_post_meta($post_id, '_df_hero_title', true) ?: get_the_title($post_id);
    $lead = get_post_meta($post_id, '_df_hero_lead', true);
    $lead_style = (bool) get_post_meta($post_id, '_df_hero_lead_style', true);
    $crumb = $crumb ?: get_the_title($post_id);
    ?>
<section class="phero">
  <div class="phero-bg"><img src="<?php echo esc_url(DF_URI . '/assets/img/hq.jpg'); ?>" alt=""></div>
  <div class="wrap phero-in">
    <p class="crumb"><a href="<?php echo esc_url(df_url('home')); ?>"><?php echo esc_html(df_t('หน้าแรก', 'Home')); ?></a> / <span><?php echo esc_html($crumb); ?></span></p>
    <h1<?php echo $lead_style ? ' class="lead-t"' : ''; ?>><?php echo esc_html($title); ?></h1>
    <?php if ($lead && !$lead_style) : ?><p><?php echo esc_html($lead); ?></p><?php endif; ?>
  </div>
</section>
    <?php
}

function df_icon($name) {
    $icons = [
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'line'  => '<path d="M12 4C7 4 3 7.2 3 11.2c0 3.6 3.2 6.6 7.5 7.1l-.4 2.2 3.2-2.1C18 18 21 14.9 21 11.2 21 7.2 17 4 12 4z"/>',
        'msgr'  => '<path d="M12 3.5c-4.8 0-8.5 3.5-8.5 8 0 2.5 1.2 4.7 3 6.1V21l2.9-1.6c.8.2 1.7.3 2.6.3 4.8 0 8.5-3.5 8.5-8s-3.7-8.2-8.5-8.2z"/><path d="M7.5 13.5l3-3 2 2 4-3.5-3 3.5-2-2z"/>',
        'tel'   => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'chat'  => '<path d="M4 5h16v11H9l-5 4z"/><path d="M8 9.5h8M8 12.5h5"/>',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true">' . ($icons[$name] ?? '') . '</svg>';
}
