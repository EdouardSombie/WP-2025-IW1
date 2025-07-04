<?php
/*
Plugin Name: Plugin ESGI
Plugin URI: https:esgi.fr
Description: Ajout d'un lien de duplication des articles et pages
Author: ESGI
Version: 1.0
*/

// Note : lors de la dernière séance, j'avais oublié de demandé à mon copilot de wrapper notre code dans un objet
// En voici le résultat. A noter l'accrochage aux hooks dans la fonction construct


// Sécurité : empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principale du plugin ESGI
 */
class ESGI_Plugin
{
    /**
     * Instance unique de la classe (Singleton)
     */
    private static $instance = null;

    /**
     * Version du plugin
     */
    const VERSION = '1.0';

    /**
     * Chemin du plugin
     */
    private $plugin_path;

    /**
     * URL du plugin
     */
    private $plugin_url;

    /**
     * Constructeur privé pour empêcher l'instanciation directe
     */
    private function __construct()
    {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);

        $this->init_hooks();
    }

    /**
     * Méthode pour obtenir l'instance unique (Singleton)
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialisation des hooks WordPress
     */
    private function init_hooks()
    {
        // Hooks d'activation/désactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Hooks d'initialisation
        add_action('init', array($this, 'init'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('init', array($this, 'register_shortcodes'));

        // Hooks pour la duplication de posts
        add_filter('post_row_actions', array($this, 'add_duplicate_link'), 10, 2);
        add_action('admin_action_esgi_duplicate_post', array($this, 'duplicate_post'));

        // Hooks pour les templates
        add_filter('template_include', array($this, 'template_include'));

        // Hooks pour la navigation
        add_filter('nav_menu_css_class', array($this, 'nav_menu_css_class'), 10, 2);
    }

    /**
     * Méthode d'activation du plugin
     */
    public function activate()
    {
        // Flush des règles de réécriture pour les nouveaux post types
        $this->register_post_types();
        $this->register_taxonomies();
        flush_rewrite_rules();
    }

    /**
     * Méthode de désactivation du plugin
     */
    public function deactivate()
    {
        // Flush des règles de réécriture
        flush_rewrite_rules();
    }

    /**
     * Initialisation du plugin
     */
    public function init()
    {
        // Chargement des traductions si nécessaire
        // Un exemple de surcode généré par l'IA...
        load_plugin_textdomain('esgi-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Enregistrement des custom post types
     */
    public function register_post_types()
    {
        $labels = array(
            'name'                  => __('Projets', 'esgi-plugin'),
            'singular_name'         => __('Projet', 'esgi-plugin'),
            'menu_name'             => __('Projets', 'esgi-plugin'),
            'name_admin_bar'        => __('Projet', 'esgi-plugin'),
            'archives'              => __('Archives des projets', 'esgi-plugin'),
            'attributes'            => __('Attributs du projet', 'esgi-plugin'),
            'parent_item_colon'     => __('Projet parent :', 'esgi-plugin'),
            'all_items'             => __('Tous les projets', 'esgi-plugin'),
            'add_new_item'          => __('Nouveau projet', 'esgi-plugin'),
            'add_new'               => __('Ajouter nouveau', 'esgi-plugin'),
            'new_item'              => __('Nouveau projet', 'esgi-plugin'),
            'edit_item'             => __('Modifier le projet', 'esgi-plugin'),
            'update_item'           => __('Mettre à jour le projet', 'esgi-plugin'),
            'view_item'             => __('Voir le projet', 'esgi-plugin'),
            'view_items'            => __('Voir les projets', 'esgi-plugin'),
            'search_items'          => __('Rechercher des projets', 'esgi-plugin'),
            'not_found'             => __('Aucun projet trouvé', 'esgi-plugin'),
            'not_found_in_trash'    => __('Aucun projet trouvé dans la corbeille', 'esgi-plugin'),
            'featured_image'        => __('Image mise en avant', 'esgi-plugin'),
            'set_featured_image'    => __('Définir l\'image mise en avant', 'esgi-plugin'),
            'remove_featured_image' => __('Supprimer l\'image mise en avant', 'esgi-plugin'),
            'use_featured_image'    => __('Utiliser comme image mise en avant', 'esgi-plugin'),
            'insert_into_item'      => __('Insérer dans le projet', 'esgi-plugin'),
            'uploaded_to_this_item' => __('Téléchargé vers ce projet', 'esgi-plugin'),
            'items_list'            => __('Liste des projets', 'esgi-plugin'),
            'items_list_navigation' => __('Navigation de la liste des projets', 'esgi-plugin'),
            'filter_items_list'     => __('Filtrer la liste des projets', 'esgi-plugin'),
        );

        $args = array(
            'label'                 => __('Projet', 'esgi-plugin'),
            'description'           => __('Custom post type pour les projets', 'esgi-plugin'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-portfolio',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array('slug' => 'projet'),
        );

        register_post_type('project', $args);
    }

    /**
     * Enregistrement des taxonomies
     */
    public function register_taxonomies()
    {
        $labels = array(
            'name'                       => __('Skills', 'esgi-plugin'),
            'singular_name'              => __('Skill', 'esgi-plugin'),
            'menu_name'                  => __('Skills', 'esgi-plugin'),
            'all_items'                  => __('Tous les skills', 'esgi-plugin'),
            'parent_item'                => __('Skill parent', 'esgi-plugin'),
            'parent_item_colon'          => __('Skill parent :', 'esgi-plugin'),
            'new_item_name'              => __('Nom du nouveau skill', 'esgi-plugin'),
            'add_new_item'               => __('Ajouter un nouveau skill', 'esgi-plugin'),
            'edit_item'                  => __('Modifier le skill', 'esgi-plugin'),
            'update_item'                => __('Mettre à jour le skill', 'esgi-plugin'),
            'view_item'                  => __('Voir le skill', 'esgi-plugin'),
            'separate_items_with_commas' => __('Séparer les skills par des virgules', 'esgi-plugin'),
            'add_or_remove_items'        => __('Ajouter ou supprimer des skills', 'esgi-plugin'),
            'choose_from_most_used'      => __('Choisir parmi les plus utilisés', 'esgi-plugin'),
            'popular_items'              => __('Skills populaires', 'esgi-plugin'),
            'search_items'               => __('Rechercher des skills', 'esgi-plugin'),
            'not_found'                  => __('Aucun skill trouvé', 'esgi-plugin'),
            'no_terms'                   => __('Aucun skill', 'esgi-plugin'),
            'items_list'                 => __('Liste des skills', 'esgi-plugin'),
            'items_list_navigation'      => __('Navigation de la liste des skills', 'esgi-plugin'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array('slug' => 'skill'),
        );

        register_taxonomy('skill', array('project'), $args);
    }

    /**
     * Enregistrement des shortcodes
     */
    public function register_shortcodes()
    {
        add_shortcode('skills-list', array($this, 'skills_list_shortcode'));
    }

    /**
     * Ajout du lien de duplication dans la liste des actions de post
     */
    public function add_duplicate_link($actions, $post)
    {
        if (!current_user_can('edit_posts')) {
            return $actions;
        }

        $url = wp_nonce_url(
            add_query_arg(
                array(
                    'action' => 'esgi_duplicate_post',
                    'post' => $post->ID,
                ),
                'admin.php'
            ),
            'esgi_duplicate_post_' . $post->ID
        );

        $actions['duplicate'] = '<a href="' . $url . '">' . __('Duplicata', 'esgi-plugin') . '</a>';
        return $actions;
    }

    /**
     * Fonction de duplication de post
     */
    public function duplicate_post()
    {
        if (!isset($_GET['post']) || !check_admin_referer('esgi_duplicate_post_' . $_GET['post'])) {
            wp_die(__('Action non autorisée.', 'esgi-plugin'));
        }

        $post_id = intval($_GET['post']);
        $original_post = get_post($post_id);

        if (!$original_post) {
            wp_die(__('Post non trouvé.', 'esgi-plugin'));
        }

        // Récupérer les propriétés du post original
        $args = array(
            'post_title'    => $original_post->post_title . ' - ' . __('DUPLICATE', 'esgi-plugin'),
            'post_content'  => $original_post->post_content,
            'post_excerpt'  => $original_post->post_excerpt,
            'post_type'     => $original_post->post_type,
            'post_status'   => 'draft',
            'post_author'   => get_current_user_id(),
        );

        $new_post_id = wp_insert_post($args);

        if ($new_post_id) {
            // Copier l'image mise en avant
            $thumbnail_id = get_post_thumbnail_id($post_id);
            if ($thumbnail_id) {
                set_post_thumbnail($new_post_id, $thumbnail_id);
            }

            // Copier les meta données
            $meta_data = get_post_meta($post_id);
            foreach ($meta_data as $key => $values) {
                foreach ($values as $value) {
                    add_post_meta($new_post_id, $key, $value);
                }
            }

            // Copier les taxonomies
            $taxonomies = get_object_taxonomies($original_post->post_type);
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                wp_set_post_terms($new_post_id, $terms, $taxonomy);
            }
        }

        wp_safe_redirect(
            add_query_arg(
                array('post_type' => $original_post->post_type),
                admin_url('edit.php')
            )
        );
        exit;
    }

    /**
     * Gestion des templates personnalisés
     */
    public function template_include($path)
    {
        if (!is_admin() && is_single() && get_query_var('post_type') == 'project') {
            // Vérifier si le thème actif a un template single-project.php
            $theme_template = locate_template('single-project.php');

            // Si le thème n'a pas de template, utiliser celui du plugin
            if (empty($theme_template)) {
                $plugin_template = $this->plugin_path . 'templates/single-project.php';
                if (file_exists($plugin_template)) {
                    $path = $plugin_template;
                }
            }
        }
        return $path;
    }

    /**
     * Gestion des classes CSS de navigation
     */
    public function nav_menu_css_class($classes, $item)
    {
        // Si on est sur une page liée aux projets
        if (is_singular('project') || is_post_type_archive('project') || is_tax('skill')) {

            // Supprimer la surbrillance des liens vers les articles
            if (
                $item->object == 'category' || $item->object == 'post' ||
                $item->url == get_permalink(get_option('page_for_posts')) ||
                ($item->type == 'post_type_archive' && $item->object == 'post')
            ) {

                $classes = array_diff($classes, array(
                    'current-menu-item',
                    'current_page_item',
                    'current-menu-ancestor',
                    'current_page_ancestor',
                    'current-menu-parent',
                    'current_page_parent'
                ));
            }

            // Ajouter la surbrillance au lien vers l'archive des projets quand on est sur un projet seul
            if (
                is_singular('project') &&
                $item->type == 'post_type_archive' &&
                $item->object == 'project'
            ) {

                if (!in_array('current-menu-parent', $classes)) {
                    $classes[] = 'current-menu-parent';
                }
            }
        }

        return $classes;
    }

    /**
     * Shortcode pour afficher la liste des skills
     */
    public function skills_list_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'title' => __('Skills', 'esgi-plugin'),
            'show_count' => false,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => true,
            'number' => 0
        ), $atts, 'skills-list');

        $skills = get_terms(array(
            'taxonomy' => 'skill',
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'hide_empty' => filter_var($atts['hide_empty'], FILTER_VALIDATE_BOOLEAN),
            'number' => intval($atts['number'])
        ));

        if (empty($skills) || is_wp_error($skills)) {
            return '<p>' . __('Aucun skill trouvé.', 'esgi-plugin') . '</p>';
        }

        $output = '<div class="skills-list-shortcode">';

        if (!empty($atts['title'])) {
            $output .= '<h3>' . esc_html($atts['title']) . '</h3>';
        }

        $output .= '<ul class="skills-list">';

        foreach ($skills as $skill) {
            $skill_link = get_term_link($skill);
            $count_text = filter_var($atts['show_count'], FILTER_VALIDATE_BOOLEAN) ? ' (' . $skill->count . ')' : '';

            $output .= '<li>';
            $output .= '<a href="' . esc_url($skill_link) . '">';
            $output .= esc_html($skill->name) . $count_text;
            $output .= '</a>';
            $output .= '</li>';
        }

        $output .= '</ul>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Getter pour le chemin du plugin
     */
    public function get_plugin_path()
    {
        return $this->plugin_path;
    }

    /**
     * Getter pour l'URL du plugin
     */
    public function get_plugin_url()
    {
        return $this->plugin_url;
    }
}

// Initialisation du plugin
function esgi_plugin_init()
{
    // Le code généré nous a créé un singleton, pourquoi pas...
    return ESGI_Plugin::get_instance();
}

// Démarrer le plugin
add_action('plugins_loaded', 'esgi_plugin_init');
