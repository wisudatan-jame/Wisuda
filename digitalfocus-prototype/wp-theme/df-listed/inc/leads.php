<?php
/**
 * Partner applications and investor-news sign-ups.
 *
 * The forms in the page content post here through admin-ajax (see assets/js/site.js).
 * Every submission is stored as a private "df_lead" entry (Leads in the admin menu)
 * and emailed to the address set in Customize → Digital Focus → ฟอร์มและคุกกี้.
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('df_lead', [
        'labels'          => ['name' => 'ผู้ติดต่อจากเว็บ', 'singular_name' => 'ผู้ติดต่อ', 'menu_name' => 'ผู้ติดต่อจากเว็บ'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-groups',
        'capability_type' => 'post',
        'capabilities'    => ['create_posts' => 'do_not_allow'],
        'map_meta_cap'    => true,
        'supports'        => ['title', 'custom-fields'],
    ]);
});

/* Fresh nonce on demand: page caches (LiteSpeed Cache) would otherwise serve an expired one. */
add_action('wp_ajax_nopriv_df_nonce', 'df_send_nonce');
add_action('wp_ajax_df_nonce', 'df_send_nonce');
function df_send_nonce() {
    nocache_headers();
    wp_send_json_success(['nonce' => wp_create_nonce('df_lead')]);
}

add_action('wp_ajax_nopriv_df_lead', 'df_handle_lead');
add_action('wp_ajax_df_lead', 'df_handle_lead');

function df_handle_lead() {
    if (!check_ajax_referer('df_lead', 'nonce', false)) {
        wp_send_json_error(['message' => 'expired'], 403);
    }
    // Bots fill the hidden field; people never see it.
    if (!empty($_POST['website'])) {
        wp_send_json_success();
    }
    // One submission per visitor every 30 seconds.
    $ip_key = 'df_lead_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    if (get_transient($ip_key)) {
        wp_send_json_error(['message' => 'slow_down'], 429);
    }

    $type = sanitize_key($_POST['type'] ?? '');
    $f = function ($k) {
        return sanitize_text_field(wp_unslash($_POST[$k] ?? ''));
    };

    if ($type === 'ir') {
        $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
        if (!is_email($email)) {
            wp_send_json_error(['message' => 'invalid'], 400);
        }
        $fields = ['อีเมล' => $email];
        $title = 'รับข่าวนักลงทุน: ' . $email;
    } elseif ($type === 'partner') {
        $fields = [
            'ประเภท'            => $f('ptype') === 'installer' ? 'พาร์ทเนอร์ติดตั้งและบริการ' : 'ตัวแทนจำหน่าย',
            'บริษัท / ร้านค้า'    => $f('company'),
            'เลขผู้เสียภาษี'       => $f('tax'),
            'ผู้ติดต่อ'           => $f('name'),
            'ตำแหน่ง'           => $f('pos'),
            'โทรศัพท์'          => $f('tel'),
            'อีเมล'             => sanitize_email(wp_unslash($_POST['email'] ?? '')),
            'LINE ID'          => $f('line'),
            'พื้นที่ให้บริการ'      => $f('region'),
            'ประสบการณ์'        => $f('years'),
            'จำนวนช่าง'         => $f('team'),
            'สินค้าที่สนใจ'        => $f('prod'),
            'รายละเอียด'        => sanitize_textarea_field(wp_unslash($_POST['msg'] ?? '')),
        ];
        $missing = !$fields['บริษัท / ร้านค้า'] || !$fields['ผู้ติดต่อ'] || !$fields['โทรศัพท์'] || !is_email($fields['อีเมล']) || !$fields['พื้นที่ให้บริการ'];
        $bad_tax = $fields['เลขผู้เสียภาษี'] !== '' && !preg_match('/^\d{13}$/', $fields['เลขผู้เสียภาษี']);
        if ($missing || $bad_tax || empty($_POST['consent'])) {
            wp_send_json_error(['message' => 'invalid'], 400);
        }
        $title = 'สมัครพันธมิตร: ' . $fields['บริษัท / ร้านค้า'];
    } else {
        wp_send_json_error(['message' => 'type'], 400);
    }

    $fields['ยินยอม PDPA'] = current_time('mysql') . ' (' . sanitize_text_field(wp_unslash($_POST['lang'] ?? 'th')) . ')';
    $id = wp_insert_post(['post_type' => 'df_lead', 'post_status' => 'private', 'post_title' => $title], true);
    if (is_wp_error($id)) {
        wp_send_json_error(['message' => 'store'], 500);
    }
    foreach ($fields as $label => $value) {
        update_post_meta($id, $label, $value);
    }
    update_post_meta($id, '_df_type', $type);
    set_transient($ip_key, 1, 30);

    $to = get_theme_mod('df_lead_to', 'info@digitalfocus.co.th');
    $body = '';
    foreach ($fields as $label => $value) {
        $body .= $label . ': ' . $value . "\n";
    }
    $body .= "\nดูทั้งหมดในหลังบ้าน: " . admin_url('edit.php?post_type=df_lead');
    $headers = [];
    if ($type === 'partner' && is_email($fields['อีเมล'])) {
        $headers[] = 'Reply-To: ' . $fields['อีเมล'];
    }
    wp_mail($to, '[เว็บไซต์] ' . $title, $body, $headers);

    wp_send_json_success();
}

/* Show the submitted fields on the entry screen. */
add_action('add_meta_boxes_df_lead', function () {
    add_meta_box('df_lead_fields', 'ข้อมูลที่ส่งมา', function ($post) {
        echo '<table class="widefat striped"><tbody>';
        foreach (get_post_meta($post->ID) as $k => $v) {
            if ($k[0] === '_') {
                continue;
            }
            printf('<tr><th style="width:180px">%s</th><td>%s</td></tr>', esc_html($k), nl2br(esc_html($v[0])));
        }
        echo '</tbody></table>';
    }, 'df_lead', 'normal', 'high');
    remove_meta_box('postcustom', 'df_lead', 'normal');
});
