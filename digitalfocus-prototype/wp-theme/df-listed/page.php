<?php
/**
 * Pages: seeded pages print their stored section HTML under the navy page hero.
 * The English home page (key "home") has its own hero inside the content.
 */
get_header();
while (have_posts()) :
    the_post();
    $key = get_post_meta(get_the_ID(), '_df_key', true);
    if ($key !== 'home') {
        df_page_hero(get_the_ID());
    }
    if ($key) {
        the_content();
    } else {
        ?>
<section class="sec"><div class="wrap entry"><?php the_content(); ?></div></section>
        <?php
    }
endwhile;
get_footer();
