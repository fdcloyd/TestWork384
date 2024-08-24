<?php
/*
Template Name: City Template
*/

get_header();

// Custom action hook before the table
do_action('before_cities_table');

// AJAX Search Form
?>
<form id="city-search-form">
    <input type="text" id="city-search" placeholder="Type Here to Search for a City..." />
</form>

<table id="cities-table">
    <thead>
        <tr>
            <th>Country</th>
            <th>City</th>
            <th>Temperature</th>
        </tr>
    </thead>
    <tbody>
        <?php
        global $wpdb;
        $cities = $wpdb->get_results("
            SELECT p.ID, p.post_title, t.name AS country
            FROM {$wpdb->prefix}posts p
            LEFT JOIN {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
            LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
            LEFT JOIN {$wpdb->prefix}terms t ON (tt.term_id = t.term_id)
            WHERE p.post_type = 'city' AND p.post_status = 'publish'
        ");

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
        ?>
    </tbody>
</table>

<?php
// Custom action hook after the table
do_action('after_cities_table');

get_footer();
