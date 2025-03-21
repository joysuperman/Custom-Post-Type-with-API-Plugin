# Custom Article API WordPress Plugin

This plugin enables a Laravel application to manage WordPress articles through a custom REST API.

## WordPress Plugin Installation

1. Upload the plugin files to `/wp-content/plugins/laravel-application-manage`
2. Activate the plugin through the WordPress admin panel
3. Generate an Application Password for authentication:
    - Go to Users → Your Profile
    - Scroll to Application Passwords section
    - Add a new application password
    - Save the generated password

## API Endpoints

-   Create Article: `POST /wp-json/custom-api/v1/article`
-   Get Article: `GET /wp-json/custom-api/v1/article/{id}`
-   Update Article: `PUT /wp-json/custom-api/v1/article/{id}`

## Authentication

The API uses WordPress Application Passwords for authentication. Send the credentials using Basic Auth in the request headers.
