<?php

// Template d'un article seul
get_header();

// Dans un template d'article seul, WP crée une variable $post : utilisons-la !

?>

<main>
    <h1><?php the_title() ?></h1> <!-- Fait référence au post courant -->
    <div class="post-meta">
        <div class="post-author">
            <?= get_the_author_meta('nickname', $post->post_author)  ?>
        </div>
        <time>
            <?= wp_date('j F Y', strtotime($post->post_date)) ?>
        </time>
    </div>
    <div class="post-thumbnail">
        <?php the_post_thumbnail() ?>
    </div>
    <div>
        <?php the_content() ?>
    </div>
</main>

<?php
get_footer();
