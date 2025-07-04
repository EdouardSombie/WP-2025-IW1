<?php get_header(); ?>

<main>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h1>Tous les projets</h1>
                <?php
                $postType = 'project';
                include('template-parts/post-list.php');
                ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>