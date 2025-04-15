<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= get_bloginfo('name') ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <header>
        <?php
        if (has_nav_menu('primary_menu')) {
            wp_nav_menu([
                'container' => 'nav',
                'container_class' => 'main-nav',
                'theme_location' => 'primary_menu'
            ]);
        }
        ?>
    </header>