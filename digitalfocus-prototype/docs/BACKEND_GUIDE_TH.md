# คู่มือทำหลังบ้านเว็บไซต์ใหม่ Digital Focus

สำหรับน้องฝึกงานที่รับงานนำดีไซน์ต้นแบบขึ้นเป็นเว็บไซต์จริงบน WordPress

อ่าน [`../README.md`](../README.md) ก่อน เพื่อเข้าใจโครงสร้างต้นแบบ แล้วค่อยอ่านคู่มือนี้

## 1. เป้าหมายของงาน

นำดีไซน์ต้นแบบขึ้นบน WordPress เดิมของ digitalfocus.co.th ให้ได้ผลดังนี้

- หน้าตาเหมือนต้นแบบทุกหน้า ทั้งบนคอมพิวเตอร์และมือถือ
- ทีมการตลาดเพิ่มข่าว ผลงาน แบรนด์ และแก้ข้อความเองได้ โดยไม่ต้องแก้โค้ด
- ฟอร์มสมัครพันธมิตรและฟอร์มรับข่าวสารนักลงทุนส่งข้อมูลถึงบริษัทจริง
- มีภาษาไทยและอังกฤษ
- รองรับ PDPA ปลอดภัย และโหลดเร็ว

ต้นแบบที่ใช้อ้างอิงมี 2 โทนสี ให้ใช้แบบที่ผู้บริหารอนุมัติ

| แบบ | ไฟล์ |
|---|---|
| A โทนน้ำเงินกรมท่า | `listed.html`, `listed-en.html` |
| B โทนสีเว็บเดิม | `listed-df.html`, `listed-df-en.html` |

ทั้งสองแบบมี HTML เหมือนกันทุกจุด ต่างกันเฉพาะ CSS ส่วนสี

## 2. ระบบเดิมที่มีอยู่

ข้อมูลนี้ตรวจจากหน้าเว็บ digitalfocus.co.th วันที่ 21 ตุลาคม 2026 ให้เข้าไปยืนยันในหลังบ้านอีกครั้ง

| ส่วน | ที่ใช้อยู่ |
|---|---|
| ระบบ | WordPress |
| ตัวสร้างหน้า | Elementor 3.32.4 และปลั๊กอิน PRO Elements |
| ธีม | `wpdt-starter` และ `wpdt-starter-child` |
| ประเภทเนื้อหาเพิ่ม | CPT UI มี post type ชื่อ `solutions` |
| ข่าว | Posts ของ WordPress (มีข่าวล่าสุดเดือนกรกฎาคม 2026) |
| ภาษา | มีหน้าไทยและอังกฤษแยกกัน เช่น `home` กับ `home-2` |
| ความปลอดภัย | Wordfence |
| สถิติ | Site Kit by Google |
| PDPA | ปลั๊กอิน PDPA Thailand |
| อื่น ๆ | SVG Support, Page scroll to id, Livemesh Addons |
| เซิร์ฟเวอร์ | LiteSpeed, **PHP 7.4.33** |

จุดที่ต้องระวัง

- **PHP 7.4 หมดระยะการอัปเดตความปลอดภัยแล้ว** ควรอัปเกรดเป็น PHP 8.2 ขึ้นไป โดยทดสอบบน staging ก่อนทุกครั้ง
- **PRO Elements ไม่ใช่ Elementor Pro ของบริษัท Elementor** เป็นชุดที่ชุมชนนำมาแจกต่อ ไม่มีการสนับสนุนจากผู้พัฒนา ให้แจ้งพี่เลี้ยงก่อนตัดสินใจใช้ต่อ
- **คำอธิบายเว็บไซต์ยังเป็นข้อความเก่า** ยังระบุแบรนด์ Jigami, Wulian, Max View และ iHorn ต้องแก้ตอนขึ้นเว็บใหม่ (ดูหัวข้อ 9)

## 3. ก่อนเริ่มงาน

### สิทธิ์ที่ต้องขอจากพี่เลี้ยง

- บัญชี WordPress บน **staging** ระดับ Administrator
- สิทธิ์เข้า hosting หรือ cPanel เฉพาะส่วน staging และ backup
- สิทธิ์เขียนใน repo นี้ เพื่อเก็บโค้ดธีม

### กฎสำคัญ

1. **ห้ามแก้บนเว็บจริงโดยตรง** ทำทุกอย่างบน local หรือ staging ก่อน
2. **สำรองข้อมูลก่อนทุกครั้ง** ทั้งไฟล์และฐานข้อมูล ก่อนอัปเดตปลั๊กอิน เปลี่ยนธีม หรืออัปเกรด PHP
3. **ติดตั้งปลั๊กอินใหม่ต้องขออนุมัติ** โดยเฉพาะปลั๊กอินที่ต้องซื้อ หรือปลั๊กอินที่เก็บข้อมูลส่วนบุคคล
4. **โค้ดธีมทุกบรรทัดต้องอยู่ใน repo** และส่งงานผ่าน Pull Request

### เตรียมเครื่อง

1. ติดตั้ง [Local](https://localwp.com/) เพื่อรัน WordPress บนเครื่อง
2. ขอไฟล์ backup จากพี่เลี้ยง แล้วนำเข้า Local ด้วยปลั๊กอิน All-in-One WP Migration หรือ Duplicator
3. ติดตั้ง VS Code และ Git

## 4. แนวทางที่ใช้

**สร้างธีมใหม่ชื่อ `df-listed`** โดยแปลงต้นแบบเป็นไฟล์ PHP ของ WordPress
ข้อความที่ทีมการตลาดต้องแก้บ่อย ให้ดึงจากหลังบ้าน ไม่เขียนตายตัวในโค้ด

ทำไมไม่สร้างใหม่ด้วย Elementor

- ต้นแบบใช้ CSS และโครงสร้างเฉพาะ ถ้าวาดใหม่ใน Elementor หน้าตาจะเพี้ยนและโหลดช้ากว่า
- ธีมที่เขียนเองดูแลง่ายกว่าในระยะยาว และไม่ขึ้นกับ PRO Elements

ระหว่างทำธีมใหม่ ให้เก็บ Elementor และธีมเดิมไว้ก่อน จนกว่าจะเปลี่ยนธีมบนเว็บจริงเรียบร้อย

### เก็บโค้ดไว้ที่ไหน

```
digitalfocus-prototype/
  wp-theme/
    df-listed/
      style.css              ชื่อธีมและเวอร์ชัน
      functions.php          โหลด CSS/JS เมนู post type และค่าตั้งค่า
      header.php             ส่วนหัวและเมนู
      footer.php             footer ปุ่มแชทลอย
      front-page.php         หน้าแรก
      page-about.php         เกี่ยวกับเรา
      page-business.php      ธุรกิจของเรา
      page-solutions.php     โซลูชัน
      page-products.php      ผลิตภัณฑ์
      page-projects.php      ผลงานและลูกค้า
      page-sustainability.php ความยั่งยืน
      page-contact.php       ติดต่อเรา
      home.php               รายการข่าว
      single.php             หน้าข่าวแต่ละข่าว
      template-parts/        ส่วนที่ใช้ซ้ำ เช่น การ์ดโครงการ แถบ CTA
      assets/css/site.css    CSS จาก <style> ในต้นแบบ
      assets/js/site.js      JavaScript ที่ยังต้องใช้
      assets/img/            รูปประกอบดีไซน์ที่ไม่ใช่เนื้อหา
```

## 5. เปลี่ยนจากต้นแบบไฟล์เดียวเป็นหน้า WordPress

### เรื่องที่ต่างจากต้นแบบมากที่สุด

ต้นแบบใส่ทุกหน้าไว้ในไฟล์เดียว และสลับหน้าด้วย `#hash` (`data-pages`, `PAGES`, `ALIAS`)
**บน WordPress ทุกหน้าต้องเป็น URL จริง** เช่น `/about/`, `/solutions/`

| ต้นแบบ | WordPress |
|---|---|
| `#home` | หน้าแรก (Settings → Reading → หน้าแรกเป็นหน้าคงที่) |
| `#about` | `/about/` ใช้ `page-about.php` |
| `#business` | `/business/` |
| `#solutions` | `/solutions/` |
| `#brands` | `/products/` |
| `#projects` | `/projects/` |
| `#esg` | `/sustainability/` |
| `#news` | `/news/` (หน้ารายการ Posts) |
| `#contact` | `/contact/` และส่วนย่อย `/contact/#partner`, `/contact/#ir` |
| `#biz-dist` และอื่น ๆ | `/business/#biz-dist` |

สิ่งที่ต้องทำ

1. คัด `<section>` ของแต่ละหน้าตาม `data-pages` ไปไว้ในไฟล์ PHP ของหน้านั้น
2. section ที่ใช้หลายหน้า เช่นแถบ CTA ท้ายหน้า ให้ทำเป็น `template-parts/cta.php` แล้วเรียกด้วย `get_template_part()`
3. แถบหัวหน้าเพจ (`.phero`) ให้ดึงชื่อหน้าจาก `the_title()` และคำโปรยจาก excerpt ของหน้า
4. **ลบ JavaScript ส่วนสลับหน้าออก** (`PAGES`, `ALIAS`, `go()` และ event ที่ดักลิงก์ `#`)
5. เปลี่ยนลิงก์ในเมนูจาก `#about` เป็นเมนูของ WordPress (หัวข้อ 6.2)

JavaScript ที่ยังใช้ต่อ: เมนูมือถือ (`#burger`, `#mnav`) แท็บอุตสาหกรรม (`.vtab`) ปุ่มแชทลอย (`#fchat`) การนับตัวเลข และการตรวจฟอร์มเบื้องต้น
แบนเนอร์คุกกี้ของต้นแบบให้ลบออก แล้วใช้ปลั๊กอิน PDPA แทน (หัวข้อ 8)

## 6. ตั้งค่าธีม

### 6.1 `style.css`

```css
/*
Theme Name: DF Listed
Description: ธีมเว็บไซต์ Digital Focus ตามดีไซน์ต้นแบบ listed
Version: 1.0.0
Text Domain: df-listed
*/
```

### 6.2 `functions.php` ส่วนพื้นฐาน

```php
<?php
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus([
        'primary' => 'เมนูหลัก',
        'footer'  => 'เมนู footer',
    ]);
});

add_action('wp_enqueue_scripts', function () {
    $v = wp_get_theme()->get('Version');
    wp_enqueue_style('df-fonts', 'https://fonts.googleapis.com/css2?family=Anuphan:wght@500;600;700&family=IBM+Plex+Sans+Thai:wght@400;500;600&display=swap', [], null);
    wp_enqueue_style('df-site', get_template_directory_uri() . '/assets/css/site.css', ['df-fonts'], $v);
    wp_enqueue_script('df-site', get_template_directory_uri() . '/assets/js/site.js', [], $v, true);
});
```

เวลาแก้ CSS หรือ JS ให้เพิ่มเลข `Version` ใน `style.css` เพื่อให้เบราว์เซอร์โหลดไฟล์ใหม่

ใน `header.php` ใช้เมนูของ WordPress แทนรายการลิงก์ในต้นแบบ

```php
<?php wp_nav_menu([
    'theme_location' => 'primary',
    'container'      => 'nav',
    'container_class'=> 'nav',
    'menu_class'     => '',
]); ?>
```

ทำเมนูมือถือ (`.mnav`) ด้วยเมนูเดียวกัน ชื่อหน้าปัจจุบันจะได้ class `current-menu-item` ให้เพิ่ม CSS ให้หน้าตาเหมือน `.nav a.on` ในต้นแบบ

### 6.3 ข้อมูลติดต่อและช่องทางออนไลน์

เบอร์โทร อีเมล LINE Facebook และช่องทางอื่นปรากฏหลายจุด ทั้งหน้าติดต่อ footer และปุ่มแชทลอย
**เก็บไว้ที่เดียวใน Customizer** (Appearance → Customize) แล้วทุกจุดดึงค่าเดียวกัน

```php
add_action('customize_register', function ($c) {
    $c->add_section('df_contact', ['title' => 'ข้อมูลติดต่อ Digital Focus']);
    $fields = [
        'df_phone' => 'เบอร์โทร',
        'df_email' => 'อีเมล',
        'df_line'  => 'ลิงก์ LINE Official',
        'df_fb'    => 'ลิงก์ Facebook',
        'df_msgr'  => 'ลิงก์ Messenger',
    ];
    foreach ($fields as $id => $label) {
        $c->add_setting($id, ['sanitize_callback' => 'sanitize_text_field']);
        $c->add_control($id, ['label' => $label, 'section' => 'df_contact']);
    }
});
```

เรียกใช้ในเทมเพลตด้วย `esc_html(get_theme_mod('df_phone'))` หรือ `esc_url(get_theme_mod('df_line'))`

## 7. เนื้อหาที่ทีมการตลาดแก้เองได้

ส่วนที่เป็นรายการซ้ำ ให้ทำเป็น post type ทีมการตลาดจะเพิ่มหรือลบรายการได้จากหลังบ้าน

| ส่วนบนเว็บ | ทำเป็น | ข้อมูลในแต่ละรายการ |
|---|---|---|
| ข่าวสารและกิจกรรม | Posts เดิม | หัวข้อ รูปหน้าปก วันที่ เนื้อหา |
| โครงการเด่น | post type `project` | รูป ชื่อโครงการ ระบบที่ติดตั้ง ปี คำอธิบาย ตัวเลข 3 ช่อง |
| โลโก้ลูกค้า (12 ช่อง) | post type `client` | ชื่อลูกค้า รูป ลำดับ |
| แบรนด์และผลิตภัณฑ์ | post type `brand` | ชื่อแบรนด์ หมวด คำอธิบาย รูป |
| อุตสาหกรรมในหน้าโซลูชัน | post type `solutions` ที่มีอยู่แล้ว | ชื่อไทย/อังกฤษ ภาพอาคาร รายการระบบ |
| รางวัลและมาตรฐาน | post type `award` | ปี ชื่อรางวัล คำอธิบาย ลิงก์ |

### วิธีสร้าง post type

ใช้ CPT UI ที่ติดตั้งอยู่แล้ว (CPT UI → Add/Edit Post Types) หรือเขียนใน `functions.php`

```php
add_action('init', function () {
    register_post_type('project', [
        'label'        => 'โครงการ',
        'public'       => true,
        'has_archive'  => false,
        'menu_icon'    => 'dashicons-portfolio',
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'show_in_rest' => true,
    ]);
});
```

`page-attributes` ทำให้กำหนดลำดับการแสดงได้จากช่อง Order

### ช่องข้อมูลเพิ่ม

ข้อมูลอย่างตัวเลข 3 ช่องของแต่ละโครงการ ใช้ปลั๊กอิน **Advanced Custom Fields (ACF) รุ่นฟรี** สร้าง field group แล้วผูกกับ post type
เรียกใช้ในเทมเพลตด้วย `get_field('spec_1_value')`

### ตัวอย่างดึงโครงการมาแสดง

```php
<?php
$projects = new WP_Query([
    'post_type'      => 'project',
    'posts_per_page' => 6,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);
while ($projects->have_posts()) : $projects->the_post(); ?>
  <div class="proj">
    <div class="im"><?php the_post_thumbnail('large', ['loading' => 'lazy']); ?></div>
    <div class="bd">
      <span class="pill"><?php echo esc_html(get_field('status')); ?></span>
      <h3><?php the_title(); ?></h3>
      <p class="sub"><?php echo esc_html(get_field('subtitle')); ?></p>
      <?php the_excerpt(); ?>
    </div>
  </div>
<?php endwhile; wp_reset_postdata(); ?>
```

ทุกข้อความที่มาจากหลังบ้านต้องผ่าน `esc_html()`, `esc_url()` หรือ `esc_attr()` ก่อนแสดง เพื่อป้องกันโค้ดแปลกปลอม

### ข้อความในหน้าคงที่

ข้อความยาวที่ไม่ได้เป็นรายการ เช่นวิสัยทัศน์ พันธกิจ และคำอธิบายแต่ละธุรกิจ ใช้ ACF ผูกกับหน้านั้น ๆ
ตั้งชื่อฟิลด์ให้สื่อความหมาย เช่น `vision_text`, `mission_items`

## 8. ฟอร์มและ PDPA

### ฟอร์มที่ต้องทำ

| ฟอร์ม | อยู่ที่ | ส่งถึง |
|---|---|---|
| สมัครเป็นพันธมิตร | `/contact/#partner` | ฝ่ายขาย (ยืนยันอีเมลกับพี่เลี้ยง) |
| รับข่าวสารนักลงทุน | `/contact/#ir` | info@digitalfocus.co.th |

ใช้ปลั๊กอินฟอร์มที่**เก็บข้อมูลที่ส่งไว้ในหลังบ้านได้** เช่น Fluent Forms (ฟรี) หรือ Contact Form 7 คู่กับ Flamingo
สร้างช่องให้ตรงกับฟอร์มในต้นแบบ (`#partnerForm`) แล้วนำ class ของต้นแบบไปใส่ เพื่อให้หน้าตาเหมือนเดิม

ฟอร์มสมัครพันธมิตรต้องมี

- ประเภทผู้สมัคร: ตัวแทนจำหน่าย / พาร์ทเนอร์ติดตั้งและบริการ
- ช่องบังคับกรอกตามต้นแบบ (มีเครื่องหมาย *)
- ตรวจเลขประจำตัวผู้เสียภาษีว่าเป็นตัวเลข 13 หลัก
- ช่องยินยอม PDPA ที่**ต้องติ๊กก่อนส่ง** และลิงก์ไปนโยบายความเป็นส่วนตัว
- ระบบกันสแปม เช่น Cloudflare Turnstile หรือ reCAPTCHA

### อีเมลที่ส่งออกจากเว็บ

ติดตั้ง **WP Mail SMTP** แล้วตั้งให้ส่งผ่านอีเมลของโดเมน digitalfocus.co.th
ถ้าไม่ตั้ง อีเมลจากฟอร์มมักตกไปอยู่ในกล่องสแปม

ทดสอบโดยส่งฟอร์มทุกแบบไปที่อีเมลทดสอบก่อน แล้วตรวจว่าได้รับครบทุกช่อง

### คุกกี้และ PDPA

- ใช้ปลั๊กอิน PDPA Thailand ที่มีอยู่ ตั้งหมวดคุกกี้ให้ครบ (จำเป็น / วิเคราะห์ / การตลาด)
- Google Analytics ผ่าน Site Kit ต้องทำงาน**หลังผู้ใช้ยินยอม**เท่านั้น
- ข้อมูลที่เก็บจากฟอร์มให้ตั้งเวลาลบตามนโยบายของบริษัท ถามพี่เลี้ยงว่าเก็บนานเท่าไร
- ข้อความ PDPA ทั้งหมดต้องได้รับอนุมัติก่อนขึ้นเว็บจริง

## 9. ภาษา ชื่อเว็บ และการค้นหา

### ภาษาไทยและอังกฤษ

1. ตรวจในหลังบ้านว่าตอนนี้ทำสองภาษาด้วยวิธีไหน (ปลั๊กอินหรือสร้างหน้าแยกเอง)
2. ถ้ายังไม่มีปลั๊กอิน แนะนำ **Polylang** เชื่อมหน้าไทยกับหน้าอังกฤษที่คู่กัน
3. ข้อความภาษาอังกฤษนำมาจาก `listed-en.html`
4. ปุ่ม TH / EN บนหัวเว็บใช้ฟังก์ชันของ Polylang สร้างลิงก์ไปหน้าคู่ของอีกภาษา
5. ข้อความในธีม (ปุ่ม เมนู ป้าย) ให้ลงทะเบียนด้วย `pll_register_string()` แล้วแปลใน Languages → Translations

### ชื่อเว็บและคำอธิบาย

- Settings → General แก้ Site Title และ Tagline ใหม่ เลิกใช้ข้อความเดิมที่ระบุ Jigami, Wulian, Max View, iHorn
- ใช้ปลั๊กอิน SEO (Yoast SEO หรือ Rank Math) ตั้งชื่อและคำอธิบายของทุกหน้า ทั้งสองภาษา
- ตรวจว่ามี sitemap แล้วส่งใน Google Search Console (เชื่อมผ่าน Site Kit ได้)

### ลิงก์เดิมต้องไม่เสีย

URL ของเว็บเดิม เช่น `/about-2/`, `/solution/`, `/contact-2/`, `/news-2/` อาจมีคนบันทึกไว้หรือติดอันดับใน Google
ใช้ปลั๊กอิน **Redirection** ตั้ง 301 redirect จาก URL เดิมทุกหน้าไปหน้าใหม่ที่ตรงกัน
ทำตารางรายการ URL เดิมกับ URL ใหม่ให้พี่เลี้ยงตรวจก่อนขึ้นจริง

## 10. ความเร็วและความปลอดภัย

### ความเร็ว

- เซิร์ฟเวอร์เป็น LiteSpeed ให้ใช้ปลั๊กอิน **LiteSpeed Cache**
- รูปที่อัปโหลดต้องย่อก่อน กว้างไม่เกิน 1600px และแปลงเป็น WebP ได้ในตั้งค่าของ LiteSpeed Cache
- ปิด CSS และ JS ของ Elementor ในหน้าที่ใช้ธีมใหม่ (หน้าเหล่านี้ไม่ได้สร้างด้วย Elementor แล้ว)
- ตรวจด้วย [PageSpeed Insights](https://pagespeed.web.dev/) ทั้งบนมือถือและคอมพิวเตอร์

### ความปลอดภัย

- อัปเกรด PHP เป็น 8.2 ขึ้นไป หลังทดสอบบน staging
- อัปเดต WordPress ธีม และปลั๊กอินทั้งหมด
- ลบปลั๊กอินที่ไม่ได้ใช้แล้ว เช่น Livemesh Addons ถ้าไม่มีหน้าไหนใช้
- เปิดการยืนยันตัวตน 2 ขั้นใน Wordfence สำหรับบัญชีผู้ดูแลทุกคน
- ให้ทีมการตลาดใช้บัญชีระดับ Editor ไม่ใช่ Administrator
- ตั้ง backup อัตโนมัติอย่างน้อยวันละครั้ง และเก็บสำเนาไว้นอกเซิร์ฟเวอร์

## 11. ลำดับการทำงานที่แนะนำ

| สัปดาห์ | งาน |
|---|---|
| 1 | ตั้ง local และ staging สำรองข้อมูล ตรวจระบบเดิมตามหัวข้อ 2 |
| 2 | สร้างธีม `df-listed` ทำ header, footer และหน้าแรก |
| 3 | ทำหน้าอื่นครบทุกหน้า และ post type สำหรับโครงการ ลูกค้า แบรนด์ รางวัล |
| 4 | ฟอร์ม อีเมล PDPA และ Customizer ข้อมูลติดต่อ |
| 5 | ภาษาอังกฤษ SEO redirect และความเร็ว |
| 6 | ทดสอบตามรายการหัวข้อ 12 แก้ไข และเตรียมขึ้นเว็บจริง |

ส่งงานผ่าน Pull Request ทุกสัปดาห์ ให้พี่เลี้ยงตรวจทีละส่วน

## 12. รายการตรวจก่อนขึ้นเว็บจริง

- [ ] ทุกหน้าหน้าตาตรงกับต้นแบบ ที่ความกว้าง 375px, 768px และ 1440px
- [ ] เมนู ปุ่ม และลิงก์ทุกจุดไปถูกหน้า ไม่มีลิงก์เสีย
- [ ] สลับ TH / EN แล้วไปหน้าคู่ที่ถูกต้อง
- [ ] ปุ่มแชทลอยเปิด LINE, Messenger, โทร และอีเมลได้จริงบนมือถือ
- [ ] ฟอร์มทุกแบบส่งถึงอีเมลปลายทาง และข้อมูลถูกเก็บในหลังบ้าน
- [ ] ฟอร์มไม่ยอมส่งถ้าไม่ติ๊กยินยอม PDPA
- [ ] แบนเนอร์คุกกี้ทำงาน และ Analytics ไม่ทำงานก่อนผู้ใช้ยินยอม
- [ ] Redirect จาก URL เดิมทุกหน้าทำงาน
- [ ] ชื่อเว็บและคำอธิบายในผลค้นหาเป็นข้อความใหม่ทั้งสองภาษา
- [ ] PageSpeed บนมือถือได้คะแนน 80 ขึ้นไป
- [ ] ทีมการตลาดลองเพิ่มข่าว 1 ข่าว และโครงการ 1 รายการได้เองโดยไม่ต้องถาม
- [ ] มี backup ล่าสุดของเว็บเดิมก่อนเปลี่ยนธีม

## 13. วันขึ้นเว็บจริง

1. นัดเวลาที่คนเข้าเว็บน้อย และแจ้งทีมที่เกี่ยวข้องล่วงหน้า
2. สำรองเว็บจริงทั้งไฟล์และฐานข้อมูล
3. ย้ายธีม `df-listed` เนื้อหา และการตั้งค่าจาก staging ขึ้นเว็บจริง
4. เปิดใช้ธีมใหม่ แล้วล้าง cache ของ LiteSpeed
5. ตรวจตามรายการหัวข้อ 12 อีกรอบบนเว็บจริง
6. ถ้ามีปัญหาร้ายแรง เปลี่ยนกลับเป็นธีมเดิมทันที แล้วค่อยแก้บน staging
7. เฝ้าดูอีเมลจากฟอร์ม และ Google Search Console ต่อเนื่อง 1–2 สัปดาห์

## 14. ต้องได้รับอนุมัติก่อนทำ

- ซื้อหรือติดตั้งปลั๊กอินใหม่
- อัปเกรด PHP หรือเปลี่ยนการตั้งค่า hosting และ DNS
- เปลี่ยนธีมบนเว็บจริง
- ข้อความเกี่ยวกับนักลงทุนสัมพันธ์ การเข้าจดทะเบียน ตัวเลขบริษัท และข้อความ PDPA
- ลบหน้า ข่าว หรือรูปเดิมบนเว็บจริง

ถ้าไม่แน่ใจ ให้ถามพี่เลี้ยงก่อนทุกครั้ง
