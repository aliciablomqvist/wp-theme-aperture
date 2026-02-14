<?php

if (!defined('ABSPATH')) {
    exit;
}

function aperture_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('align-wide');
    add_theme_support('custom-logo');
    
    add_image_size('aperture-grid', 600, 600, true);
    add_image_size('aperture-large', 1600, 1200, false);
    add_image_size('aperture-full', 2400, 1800, false);
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'aperture'),
    ));
}
add_action('after_setup_theme', 'aperture_setup');

function aperture_enqueue_fonts() {
    wp_enqueue_style(
        'aperture-fonts',
        'https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'aperture_enqueue_fonts');

function aperture_enqueue_assets() {
    wp_enqueue_style(
        'aperture-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
    
    wp_enqueue_style(
        'aperture-portfolio',
        get_template_directory_uri() . '/assets/css/portfolio.css',
        array(),
        '1.0.0'
    );
    
    wp_enqueue_script(
        'aperture-scripts',
        get_template_directory_uri() . '/assets/js/aperture.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'aperture_enqueue_assets');

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

function aperture_excerpt_length($length) {
    return 30;
}

add_shortcode('flipbook', 'aperture_flipbook_shortcode');

add_filter('excerpt_length', 'aperture_excerpt_length');

function aperture_register_patterns() {

    register_block_pattern_category('aperture', array(
        'label' => __('Aperture Patterns', 'aperture'),
    ));

    
    
    register_block_pattern(
        'aperture/ringbinder-page',
        array(
            'title' => 'Ring Binder Page',
            'description' => 'Full page with ring binder effect and image number',
            'categories' => array('aperture'),
            'content' => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"backgroundColor":"white","layout":{"type":"constrained","contentSize":"900px"},"className":"aperture-binder-page"} -->
<div class="wp-block-group aperture-binder-page has-white-background-color has-background" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="" alt="Main photo"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"3rem","bottom":"3rem","left":"4rem","right":"4rem"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-base-background-color has-background" style="padding-top:3rem;padding-right:4rem;padding-bottom:3rem;padding-left:4rem"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"20%"} -->
<div class="wp-block-column" style="flex-basis:20%"><!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8rem","letterSpacing":"0.1em"}},"fontFamily":"mono"} -->
<p class="has-mono-font-family" style="font-size:0.8rem;letter-spacing:0.1em">( 001 )</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"80%"} -->
<div class="wp-block-column" style="flex-basis:80%"><!-- wp:heading {"textAlign":"right","style":{"typography":{"fontSize":"1.2rem","letterSpacing":"0.05em","textTransform":"uppercase"}},"fontFamily":"inter"} -->
<h2 class="wp-block-heading has-text-align-right has-inter-font-family" style="font-size:1.2rem;letter-spacing:0.05em;text-transform:uppercase">PROJECT TITLE<br>SUBTITLE HERE</h2>
<!-- /wp:heading --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
        )
    );

    register_block_pattern(
        'aperture/table-contents',
        array(
            'title' => 'Table of Contents',
            'description' => 'Editorial table of contents',
            'categories' => array('aperture'),
            'content' => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"8rem","bottom":"8rem","left":"6rem","right":"6rem"}}},"backgroundColor":"base","layout":{"type":"constrained","contentSize":"700px"}} -->
<div class="wp-block-group has-base-background-color has-background" style="padding-top:8rem;padding-right:6rem;padding-bottom:8rem;padding-left:6rem"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.7rem","letterSpacing":"0.2em"}},"fontFamily":"mono"} -->
<p class="has-text-align-center has-mono-font-family" style="font-size:0.7rem;letter-spacing:0.2em">00.0 / CONTENTS</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"5rem"} -->
<div style="height:5rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"2rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-bottom:2rem"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"2rem"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"10%"} -->
<div class="wp-block-column" style="flex-basis:10%"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem"}},"fontFamily":"mono"} -->
<p class="has-mono-font-family" style="font-size:1rem">01</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.1rem","letterSpacing":"0.03em","fontWeight":"600"}},"fontFamily":"inter"} -->
<h3 class="wp-block-heading has-inter-font-family" style="font-size:1.1rem;font-weight:600;letter-spacing:0.03em">CHAPTER ONE</h3>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:paragraph {"style":{"typography":{"fontSize":"0.85rem"}},"fontFamily":"mono"} -->
<p class="has-mono-font-family" style="font-size:0.85rem">01.1 / Section<br>01.2 / Section</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"10%"} -->
<div class="wp-block-column" style="flex-basis:10%"><!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":"0.85rem"}},"fontFamily":"mono"} -->
<p class="has-text-align-right has-mono-font-family" style="font-size:0.85rem">01<br>04</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
        )
    );

    register_block_pattern(
        'aperture/photo-spread',
        array(
            'title' => 'Photo Spread',
            'description' => 'Two-page photo spread',
            'categories' => array('aperture'),
            'content' => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"white","layout":{"type":"constrained","contentSize":"100%"},"className":"aperture-spread"} -->
<div class="wp-block-group alignfull aperture-spread has-white-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="" alt="Left page"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="" alt="Right page"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
        )
    );

    register_block_pattern(
        'aperture/grid-collection',
        array(
            'title' => 'Photo Grid 4x4',
            'description' => 'Grid of 16 photos',
            'categories' => array('aperture'),
            'content' => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem","left":"3rem","right":"3rem"}}},"backgroundColor":"white","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group has-white-background-color has-background" style="padding-top:4rem;padding-right:3rem;padding-bottom:4rem;padding-left:3rem"><!-- wp:gallery {"columns":4,"linkTo":"none","sizeSlug":"medium","className":"aperture-photo-grid"} -->
<figure class="wp-block-gallery has-nested-images columns-4 is-cropped aperture-photo-grid">
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="" alt=""/></figure>
<!-- /wp:image -->
</figure>
<!-- /wp:gallery --></div>
<!-- /wp:group -->',
        )
    );
register_block_pattern(
    'aperture/flipbook-gallery',
    array(
        'title' => 'Flipbook Gallery',
        'description' => 'Multiple pages with flip animation',
        'categories' => array('aperture'),
        'content' => '<!-- wp:group {"className":"aperture-flipbook-container"} -->
<div class="wp-block-group aperture-flipbook-container">
    <!-- Navigation -->
    <div class="flipbook-nav">
        <button class="flip-prev">← PREV</button>
        <span class="page-indicator">1 / 10</span>
        <button class="flip-next">NEXT →</button>
    </div>
    
    <!-- Pages -->
    <div class="flipbook-pages">
        <!-- Page 1 -->
        <div class="flipbook-page active" data-page="1">
            <!-- wp:image {"sizeSlug":"large"} -->
            <figure class="wp-block-image size-large"><img src="" alt="Page 1"/></figure>
            <!-- /wp:image -->
            <div class="page-caption">( 001 ) TITLE HERE</div>
        </div>
        
        <!-- Page 2 -->
        <div class="flipbook-page" data-page="2">
            <!-- wp:image {"sizeSlug":"large"} -->
            <figure class="wp-block-image size-large"><img src="" alt="Page 2"/></figure>
            <!-- /wp:image -->
            <div class="page-caption">( 002 ) TITLE HERE</div>
        </div>
        
        <!-- Page 3 -->
        <div class="flipbook-page" data-page="3">
            <!-- wp:image {"sizeSlug":"large"} -->
            <figure class="wp-block-image size-large"><img src="" alt="Page 3"/></figure>
            <!-- /wp:image -->
            <div class="page-caption">( 003 ) TITLE HERE</div>
        </div>
        
        <!-- Page 4 -->
        <div class="flipbook-page" data-page="4">
            <!-- wp:image {"sizeSlug":"large"} -->
            <figure class="wp-block-image size-large"><img src="" alt="Page 4"/></figure>
            <!-- /wp:image -->
            <div class="page-caption">( 004 ) TITLE HERE</div>
        </div>
        
        <!-- Page 5 -->
        <div class="flipbook-page" data-page="5">
            <!-- wp:image {"sizeSlug":"large"} -->
            <figure class="wp-block-image size-large"><img src="" alt="Page 5"/></figure>
            <!-- /wp:image -->
            <div class="page-caption">( 005 ) TITLE HERE</div>
        </div>
        
        <!-- Add more pages as needed -->
    </div>
</div>
<!-- /wp:group -->',
    )
);

}
add_action('init', 'aperture_register_patterns');