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

## Laravel Integration

1. Copy the `WordPressArticleService.php` file to your Laravel project's `app/Services` directory
2. Install the required Guzzle HTTP client:

    ```bash
    composer require guzzlehttp/guzzle
    ```

3. Usage example:

    ```php
    use App\Services\WordPressArticleService;

    $wordpress = new WordPressArticleService(
        'https://your-wordpress-site.com',
        'your-username',
        'your-application-password'
    );

    // Create an article
    $article = $wordpress->createArticle([
        'title' => 'My Article',
        'content' => 'Article content',
        'excerpt' => 'Article excerpt',
        'status' => 'publish'
    ]);

    // Get an article
    $article = $wordpress->getArticle(123);

    // Update an article
    $updated = $wordpress->updateArticle(123, [
        'title' => 'Updated Title',
        'content' => 'Updated content'
    ]);
    ```

## API Endpoints

-   Create Article: `POST /wp-json/custom-api/v1/article`
-   Get Article: `GET /wp-json/custom-api/v1/article/{id}`
-   Update Article: `PUT /wp-json/custom-api/v1/article/{id}`

## Authentication

The API uses WordPress Application Passwords for authentication. Send the credentials using Basic Auth in the request headers.
