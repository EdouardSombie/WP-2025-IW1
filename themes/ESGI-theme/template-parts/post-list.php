<?php

$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
$args = [
    'post_type' => 'post', // valeur par défaut
    'posts_per_page' => 2, // Serait mieux d'utiliser la valeur définie en bo
    'paged' => $paged // numéro de la page demandée
];
//$posts = get_posts($args); // équivalent d'un fetchAll

// Pour pouvoir générer une pagination, le mieux est de se reposer sur une WP_Query
$query = new WP_Query($args);


// echo '<pre>';
// var_dump($posts);
?>
<div>
    <ul class="post-list">
        <?php if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post(); // Instancier un objet WP_Post qui fait office de post courant
                $post = get_post();
        ?>
                <li>
                    <a href="<?= the_permalink() ?>"><?= the_title() ?> <time><?= wp_date('j F Y', strtotime($post->post_date)) ?></time></a>
                </li>
        <?php }
        } ?>
    </ul>
    <div class="post-list-pagination">
        <?php
        $big = 999999999; // need an unlikely integer

        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages
        ));
        ?>
    </div>
</div>