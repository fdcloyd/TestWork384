
<?php
class City_Temperature_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'city_temperature_widget',
            'City Temperature Widget',
            array('description' => 'Displays a city name and current temperature')
        );
    }

    public function widget($args, $instance) {
        $city_id = isset($instance['city_id']) ? $instance['city_id'] : 0;

        if ($city_id) {
            $city_name = get_the_title($city_id);
            $latitude = get_post_meta($city_id, 'city_latitude', true);
            $longitude = get_post_meta($city_id, 'city_longitude', true);

            // Call the Weatherstack API
            $api_key = WEATHERSTACKAPI;
            $response = wp_remote_get("https://api.weatherstack.com/current?access_key={$api_key}&query={$latitude},{$longitude}");

            if (is_array($response) && !is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $weather_data = json_decode($body, true);
                $temperature = $weather_data['current']['temperature'];
                echo $args['before_widget'];
                echo $args['before_title'] . $city_name . $args['after_title'];
                echo "<p>Temperature: " . esc_html($temperature) . "°C</p>";
                echo $args['after_widget'];
            }
        }
    }

    public function form($instance) {
        $city_id = isset($instance['city_id']) ? $instance['city_id'] : 0;
        $cities = get_posts(array('post_type' => 'city', 'posts_per_page' => -1));

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('city_id'); ?>">Select City:</label>
            <select id="<?php echo $this->get_field_id('city_id'); ?>" name="<?php echo $this->get_field_name('city_id'); ?>">
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo $city->ID; ?>" <?php selected($city_id, $city->ID); ?>><?php echo $city->post_title; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['city_id'] = (!empty($new_instance['city_id'])) ? intval($new_instance['city_id']) : 0;
        return $instance;
    }
}

function register_city_temperature_widget() {
    register_widget('City_Temperature_Widget');
}
add_action('widgets_init', 'register_city_temperature_widget');
