<?php
$en = df_lang() === 'en';
$phone = df_contact('phone');
$email = df_contact('email');
?>
</main>

<footer class="ftr">
  <div class="wrap">
    <div class="ftr-grid">
      <div>
        <img class="logo-w" src="<?php echo esc_url(DF_URI . '/assets/img/logo-white.png'); ?>" alt="Digital Focus" width="773" height="347">
        <p><?php echo esc_html(df_t('บริษัท ดิจิตอลโฟกัส จำกัด — ผู้ให้บริการ Smart Security Solutions & AiIOT ครบวงจร ด้วยประสบการณ์กว่า 24 ปี และเครือข่ายตัวแทนทั่วประเทศ', 'Digital Focus Co., Ltd. — provider of end-to-end Smart Security Solutions & AiIOT, with over 24 years of experience and a nationwide dealer network')); ?></p>
        <div class="social" id="social"></div>
      </div>
      <div><h4><?php echo esc_html(df_t('บริษัท', 'Company')); ?></h4><ul>
        <li><a href="<?php echo esc_url(df_url('about')); ?>"><?php echo esc_html(df_t('เกี่ยวกับเรา', 'About Us')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('esg')); ?>"><?php echo esc_html(df_t('ความยั่งยืน', 'Sustainability')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('news')); ?>"><?php echo esc_html(df_t('ข่าวสาร', 'News')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('contact') . '#ir'); ?>"><?php echo esc_html(df_t('นักลงทุนสัมพันธ์', 'Investor Relations')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('contact') . '#partner'); ?>"><?php echo esc_html(df_t('ร่วมเป็นพันธมิตร', 'Become a Partner')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('contact')); ?>"><?php echo esc_html(df_t('ติดต่อเรา', 'Contact Us')); ?></a></li>
      </ul></div>
      <div><h4><?php echo esc_html(df_t('ธุรกิจ', 'Business')); ?></h4><ul>
        <li><a href="<?php echo esc_url(df_url('solutions')); ?>"><?php echo esc_html(df_t('โซลูชัน', 'Solutions')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('brands')); ?>"><?php echo esc_html(df_t('ผลิตภัณฑ์', 'Products')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('business') . '#biz-si'); ?>"><?php echo esc_html(df_t('ออกแบบและวางระบบ', 'Design and System Integration')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('business') . '#biz-service'); ?>"><?php echo esc_html(df_t('บริการรายเดือนและหลังการขาย', 'Monthly Service Plans and After-Sales Service')); ?></a></li>
        <li><a href="<?php echo esc_url(df_url('projects')); ?>"><?php echo esc_html(df_t('ผลงานและลูกค้า', 'Projects & Clients')); ?></a></li>
        <?php if (get_theme_mod('df_shopee', 'https://shopee.co.th/digitalfocus_thailand')) : ?>
        <li><a href="<?php echo esc_url(get_theme_mod('df_shopee', 'https://shopee.co.th/digitalfocus_thailand')); ?>" target="_blank" rel="noopener"><?php echo esc_html(df_t('ร้านค้าออนไลน์', 'Online Store')); ?></a></li>
        <?php endif; ?>
      </ul></div>
      <div><h4><?php echo esc_html(df_t('ติดต่อ', 'Contact')); ?></h4><ul>
        <li><?php echo esc_html(df_t('2782/4-6 ถนนลาดพร้าว แขวงคลองจั่น เขตบางกะปิ กรุงเทพฯ 10240', '2782/4-6 Lat Phrao Road, Khlong Chan, Bang Kapi, Bangkok 10240')); ?></li>
        <li><a href="<?php echo esc_attr(df_tel_href($phone)); ?>"><?php echo esc_html($phone); ?></a></li>
        <li><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></li>
      </ul></div>
    </div>
    <div class="ftr-bot"><span><?php echo esc_html(sprintf(df_t('© %s บริษัท ดิจิตอลโฟกัส จำกัด สงวนลิขสิทธิ์', '© %s Digital Focus Co., Ltd. All rights reserved.'), wp_date('Y'))); ?></span><span><?php echo esc_html(df_t('ทะเบียนเลขที่ 0105545026907 · เตรียมเข้าจดทะเบียนในตลาดหลักทรัพย์ฯ', 'Registration No. 0105545026907 · Preparing to list on the Stock Exchange of Thailand (SET)')); ?></span></div>
  </div>
</footer>

<div class="fchat" id="fchat">
  <div class="fchat-list" id="fchatList" hidden>
    <?php if (df_contact('line')) : ?><a href="<?php echo esc_url(df_contact('line')); ?>" target="_blank" rel="noopener"><span class="ci line"><?php echo df_icon('line'); ?></span><?php echo esc_html(df_t('แชทผ่าน LINE', 'Chat on LINE')); ?></a><?php endif; ?>
    <?php if (df_contact('msgr')) : ?><a href="<?php echo esc_url(df_contact('msgr')); ?>" target="_blank" rel="noopener"><span class="ci fb"><?php echo df_icon('msgr'); ?></span>Facebook Messenger</a><?php endif; ?>
    <a href="<?php echo esc_attr(df_tel_href($phone)); ?>"><span class="ci tel"><?php echo df_icon('tel'); ?></span><?php echo esc_html(df_t('โทร ', 'Call ') . $phone); ?></a>
    <a href="mailto:<?php echo esc_attr($email); ?>"><span class="ci mail"><?php echo df_icon('mail'); ?></span><?php echo esc_html($email); ?></a>
  </div>
  <button class="fchat-btn" id="fchatBtn" type="button" aria-expanded="false" aria-controls="fchatList"><?php echo df_icon('chat'); ?><span><?php echo esc_html(df_t('แชทกับเรา', 'Chat with us')); ?></span></button>
</div>

<?php if (get_theme_mod('df_cookie_banner', true)) : ?>
<div class="cookie" id="cookie" role="region" aria-label="<?php echo esc_attr(df_t('การตั้งค่าคุกกี้', 'Cookie settings')); ?>" hidden><p><?php echo esc_html(df_t('เราใช้คุกกี้ที่จำเป็นต่อการทำงานของเว็บไซต์ และใช้คุกกี้เพื่อการวิเคราะห์เมื่อคุณยินยอม ตาม พ.ร.บ.คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562', 'We use cookies essential to the website\'s operation, and analytics cookies with your consent, in accordance with the Personal Data Protection Act B.E. 2562 (2019) (PDPA).')); ?></p><div><button class="btn btn-p" type="button" data-c="all"><?php echo esc_html(df_t('ยอมรับทั้งหมด', 'Accept All')); ?></button><button class="btn btn-o" type="button" data-c="necessary"><?php echo esc_html(df_t('เฉพาะที่จำเป็น', 'Essential Only')); ?></button></div></div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
