<?php
/**
 * Aperture Theme Functions
 * 
 * Editorial photography portfolio theme
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function aperture_setup() {
    // Theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('align-wide');
    add_theme_support('custom-logo');
    
    // Image sizes
    add_image_size('aperture-grid', 600, 600, true);
    add_image_size('aperture-large', 1600, 1200, false);
    add_image_size('aperture-full', 2400, 1800, false);
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'aperture'),
    ));
}
add_action('after_setup_theme', 'aperture_setup');

/**
 * Enqueue Google Fonts
 */
function aperture_enqueue_fonts() {
    wp_enqueue_style(
        'aperture-fonts',
        'https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'aperture_enqueue_fonts');

/**
 * Enqueue Theme Assets
 */
function aperture_enqueue_assets() {
    // Main stylesheet
    wp_enqueue_style(
        'aperture-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
    
    // Portfolio styles
    wp_enqueue_style(
        'aperture-portfolio',
        get_template_directory_uri() . '/assets/css/portfolio.css',
        array(),
        '1.0.0'
    );
    
    // Main JavaScript
    wp_enqueue_script(
        'aperture-scripts',
        get_template_directory_uri() . '/assets/js/aperture.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'aperture_enqueue_assets');

/**
 * Register Portfolio Custom Post Type
 */
function aperture_register_portfolio() {
    $labels = array(
        'name'               => 'Projects',
        'singular_name'      => 'Project',
        'menu_name'          => 'Portfolio',
        'add_new'            => 'Add New Project',
        'add_new_item'       => 'Add New Project',
        'edit_item'          => 'Edit Project',
        'new_item'           => 'New Project',
        'view_item'          => 'View Project',
        'search_items'       => 'Search Projects',
        'not_found'          => 'No projects found',
        'not_found_in_trash' => 'No projects found in trash',
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'work'),
        'menu_icon'          => 'dashicons-camera',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
    );
    
    register_post_type('portfolio', $args);
}
add_action('init', 'aperture_register_portfolio');

/**
 * Register Portfolio Categories
 */
function aperture_register_taxonomies() {
    $labels = array(
        'name'          => 'Categories',
        'singular_name' => 'Category',
        'search_items'  => 'Search Categories',
        'all_items'     => 'All Categories',
        'edit_item'     => 'Edit Category',
        'update_item'   => 'Update Category',
        'add_new_item'  => 'Add New Category',
        'new_item_name' => 'New Category Name',
        'menu_name'     => 'Categories',
    );
    
    register_taxonomy('portfolio_category', 'portfolio', array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'category'),
    ));
}
add_action('init', 'aperture_register_taxonomies');

/**
 * Add Portfolio Meta Boxes
 */
function aperture_add_meta_boxes() {
    add_meta_box(
        'aperture_project_details',
        'Project Details',
        'aperture_project_details_callback',
        'portfolio',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'aperture_add_meta_boxes');

function aperture_project_details_callback($post) {
    wp_nonce_field('aperture_save_project_details', 'aperture_project_nonce');
    
    $client = get_post_meta($post->ID, '_aperture_client', true);
    $year = get_post_meta($post->ID, '_aperture_year', true);
    $location = get_post_meta($post->ID, '_aperture_location', true);
    
    echo '<p><label>Client:</label><br>';
    echo '<input type="text" name="aperture_client" value="' . esc_attr($client) . '" style="width:100%"></p>';
    
    echo '<p><label>Year:</label><br>';
    echo '<input type="text" name="aperture_year" value="' . esc_attr($year) . '" style="width:100%"></p>';
    
    echo '<p><label>Location:</label><br>';
    echo '<input type="text" name="aperture_location" value="' . esc_attr($location) . '" style="width:100%"></p>';
}

function aperture_save_project_details($post_id) {
    if (!isset($_POST['aperture_project_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['aperture_project_nonce'], 'aperture_save_project_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['aperture_client'])) {
        update_post_meta($post_id, '_aperture_client', sanitize_text_field($_POST['aperture_client']));
    }
    
    if (isset($_POST['aperture_year'])) {
        update_post_meta($post_id, '_aperture_year', sanitize_text_field($_POST['aperture_year']));
    }
    
    if (isset($_POST['aperture_location'])) {
        update_post_meta($post_id, '_aperture_location', sanitize_text_field($_POST['aperture_location']));
    }
}
add_action('save_post', 'aperture_save_project_details');

/**
 * Custom excerpt length
 */
function aperture_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'aperture_excerpt_length');

/**
 * Register Block Patterns
 */
function aperture_register_patterns() {

    register_block_pattern_category('aperture', array(
        'label' => __('Aperture Patterns', 'aperture'),
    ));

    /* ===============================
       EDITORIAL GRID – inspirerad av dina bilder
    =============================== */

    register_block_pattern(
        'aperture/editorial-grid',
        array(
            'title'       => 'Editorial Image Grid',
            'categories'  => array('aperture'),
            'description' => 'Magazine style mosaic grid',
            'content'     => '
            <!-- wp:group {"layout":{"type":"constrained","contentSize":"1400px"}} -->
            <div class="wp-block-group">
                <!-- wp:gallery {"columns":3,"linkTo":"none","sizeSlug":"aperture-grid"} -->
                <figure class="wp-block-gallery columns-3">
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                </figure>
                <!-- /wp:gallery -->
            </div>
            <!-- /wp:group -->'
        )
    );

    /* ===============================
       X-RAY MOSAIC GRID (bild 3 vibe)
    =============================== */

    register_block_pattern(
        'aperture/xray-mosaic',
        array(
            'title'       => 'X-Ray Mosaic Grid',
            'categories'  => array('aperture'),
            'description' => 'Dense photographic archive grid',
            'content'     => '
            <!-- wp:group {"layout":{"type":"constrained","contentSize":"1600px"}} -->
            <div class="wp-block-group">
                <!-- wp:gallery {"columns":6,"linkTo":"none","sizeSlug":"aperture-grid"} -->
                <figure class="wp-block-gallery columns-6">
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                    <!-- wp:image {"sizeSlug":"aperture-grid"} /-->
                </figure>
                <!-- /wp:gallery -->
            </div>
            <!-- /wp:group -->'
        )
    );

    /* ===============================
       EDITORIAL SPLIT IMAGE (sista bilden vibe)
    =============================== */

    register_block_pattern(
        'aperture/editorial-split',
        array(
            'title'       => 'Editorial Split Layout',
            'categories'  => array('aperture'),
            'description' => 'Offset editorial photography layout',
            'content'     => '
            <!-- wp:columns -->
            <div class="wp-block-columns">
                <!-- wp:column -->
                <div class="wp-block-column">
                    <!-- wp:image {"sizeSlug":"aperture-large"} /-->
                </div>
                <!-- /wp:column -->

                <!-- wp:column -->
                <div class="wp-block-column">
                    <!-- wp:image {"sizeSlug":"aperture-large"} /-->
                </div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->'
        )
    );
}
add_action('init', 'aperture_register_patterns');
