<?php
/**
 * Thai home page: the hero and every section live in the page content.
 */
get_header();
while (have_posts()) :
    the_post();
    the_content();
endwhile;
get_footer();
