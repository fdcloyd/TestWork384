# TestWork384


#Table of Contents
1. [Description](#Description)
2. [Instructions](#Instructions)

## Description
This WordPress child theme extends the Storefront theme by introducing custom functionalities for managing cities and countries, along with weather data integration

Key Features:

Custom Post Type: Cities

Adds a custom post type called "Cities" to the WordPress admin panel.
Each "City" post allows users to add detailed information about various cities.
Meta Box with Custom Fields:

On the city post editing page, a meta box titled "City Coordinates" is available.
This meta box includes custom fields:
Latitude: Text field for entering the city's latitude.
Longitude: Text field for entering the city's longitude.

Custom Taxonomy: Countries
A custom taxonomy titled "Countries" is created and associated with the "Cities" post type.
Users can categorize cities by their respective countries.

City Weather Widget:
A custom widget is created to display information about a selected city from the "Cities" custom post type.
The widget shows the city's name and its current temperature, which is fetched using the WeatherStack API.
Custom Template: Countries and Cities Table

A custom page template is provided to display a table listing countries, their associated cities, and the current temperatures for each city.
Data is retrieved using a database query via the global $wpdb variable.
The page includes a search field above the table to allow users to search for cities using AJAX.
Custom action hooks are added before and after the table to facilitate further customization.

### This child theme provides enhanced functionality tailored for managing and displaying city and country data, along with real-time weather information.

## Instructions
Create a DEFINE named WEATHERSTACKAPI and put your API from WeatherStack as the value. You can make it in wp-config.php or functions.php\
eg.\
DEFINE('WEATHERSTACKAPI', 'your_api_here');
