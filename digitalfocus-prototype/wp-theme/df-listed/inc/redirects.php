<?php
/**
 * Send visitors from the old site's page addresses to the new pages.
 * Runs only when WordPress would otherwise show "page not found".
 * Add more pairs to df_old_paths() if Search Console reports other old links.
 */

defined('ABSPATH') || exit;

function df_old_paths() {
    return [
        'home-2'             => ['home', 'th'],
        'about-2'            => ['about', 'th'],
        'news-2'             => ['news', 'th'],
        'contact-2'          => ['contact', 'th'],
        'solution'           => ['solutions', 'th'],
        'services'           => ['business', 'th'],
        'investor-relations' => ['contact', 'th'],
        'home'               => ['home', 'th'],
        'about'              => ['about', 'th'],
        'news'               => ['news', 'th'],
        'contact'            => ['contact', 'th'],
    ];
}

add_action('template_redirect', function () {
    if (!is_404()) {
        return;
    }
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    $map = df_old_paths();
    if (isset($map[$path])) {
        [$key, $lang] = $map[$path];
        wp_safe_redirect(df_url($key, $lang), 301);
        exit;
    }
});
