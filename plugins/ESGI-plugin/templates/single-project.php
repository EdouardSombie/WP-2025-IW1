<?php

// Template générique d'un projet seul
get_header();


?>

<main>
    <h1><?php the_title() ?></h1>
    <?php the_content() ?>
</main>

<?php
get_footer();
