<?php get_header(); ?>

<main>

    <?php include('template-parts/identity-card.php'); ?>

    <?php
    if (!is_front_page()) {
        include('template-parts/post-list.php');
    }
    ?>

</main>

<?php get_footer(); ?>