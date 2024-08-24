
<?php

/** Child Theme Enqueue */
function loyd_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'loyd_scripts' );

/** Start of Cities - Temperature */


/** Post Type 
  * Cities */

  function create_cities_post_type() {
    $labels = array(
        'name' => 'Cities',
        'singular_name' => 'City',
        'menu_name' => 'Cities',
        'name_admin_bar' => 'City',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New City',
        'new_item' => 'New City',
        'edit_item' => 'Edit City',
        'view_item' => 'View City',
        'all_items' => 'All Cities',
        'search_items' => 'Search Cities',
        'not_found' => 'No cities found.',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-location',
    );

    register_post_type('city', $args);
}
add_action('init', 'create_cities_post_type');


/** Meta Box
 * Adding Longitude & Latitude
 */
function add_city_meta_boxes() {
    add_meta_box(
        'city_lat_long',
        'City Coordinates',
        'render_city_lat_long_meta_box',
        'city',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_city_meta_boxes');

/** Interface for Latitude & Longitude */

function render_city_lat_long_meta_box($post) {
    $latitude = get_post_meta($post->ID, 'city_latitude', true);
    $longitude = get_post_meta($post->ID, 'city_longitude', true);
    ?>
    <label for="city_latitude">Latitude:</label>
    <input type="text" id="city_latitude" name="city_latitude" value="<?php echo esc_attr($latitude); ?>" />
    <br />
    <label for="city_longitude">Longitude:</label>
    <input type="text" id="city_longitude" name="city_longitude" value="<?php echo esc_attr($longitude); ?>" />
    <?php
}

/** Saving City LatLong Function */

function save_city_meta_boxes($post_id) {
    if (isset($_POST['city_latitude'])) {
        update_post_meta($post_id, 'city_latitude', sanitize_text_field($_POST['city_latitude']));
    }
    if (isset($_POST['city_longitude'])) {
        update_post_meta($post_id, 'city_longitude', sanitize_text_field($_POST['city_longitude']));
    }
}
add_action('save_post', 'save_city_meta_boxes');

/** 
 * Taxonomy - Countries
 */

 function create_country_taxonomy() {
    $labels = array(
        'name' => 'Countries',
        'singular_name' => 'Country',
        'search_items' => 'Search Countries',
        'all_items' => 'All Countries',
        'edit_item' => 'Edit Country',
        'update_item' => 'Update Country',
        'add_new_item' => 'Add New Country',
        'new_item_name' => 'New Country Name',
        'menu_name' => 'Countries',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'country'),
    );

    register_taxonomy('country', array('city'), $args);
}
add_action('init', 'create_country_taxonomy');


/** AJAX Search for City */
function city_search_ajax_handler() {
    global $wpdb;

    $search = sanitize_text_field($_POST['search']);
    $cities = $wpdb->get_results($wpdb->prepare("
        SELECT p.ID, p.post_title, t.name AS country
        FROM {$wpdb->prefix}posts p
        LEFT JOIN {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
        LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
        LEFT JOIN {$wpdb->prefix}terms t ON (tt.term_id = t.term_id)
        WHERE p.post_type = 'city' AND p.post_status = 'publish' AND p.post_title LIKE %s
    ", '%' . $search . '%'));

    ob_start();
    foreach ($cities as $city) {
        $latitude = get_post_meta($city->ID, 'city_latitude', true);
        $longitude = get_post_meta($city->ID, 'city_longitude', true);

        // Call the Weatherstack API
        $api_key = WEATHERSTACKAPI;
		$response = wp_remote_get("https://api.weatherstack.com/current?access_key={$api_key}&query={$latitude},{$longitude}");

        if (is_array($response) && !is_wp_error($response)) {
			$body = wp_remote_retrieve_body($response);
            $weather_data = json_decode($body, true);
            $temperature = $weather_data['current']['temperature'];
            ?>
            <tr>
                <td><?php echo esc_html($city->country); ?></td>
                <td><?php echo esc_html($city->post_title); ?></td>
                <td><?php echo esc_html($temperature); ?>°C</td>
            </tr>
            <?php
        }
    }
    echo ob_get_clean();
    wp_die();
}
add_action('wp_ajax_city_search', 'city_search_ajax_handler');
add_action('wp_ajax_nopriv_city_search', 'city_search_ajax_handler');

function enqueue_city_search_scripts() {
    wp_enqueue_script( 'city-search-script',  get_stylesheet_directory_uri() . '/assets/js/city-search.js', array('jquery'), null, true );
    wp_localize_script('city-search-script', 'citySearch', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_city_search_scripts');


function custom_content_before_cities_table() {
    echo '<div>Search:</</div>';
}
add_action('before_cities_table', 'custom_content_before_cities_table');