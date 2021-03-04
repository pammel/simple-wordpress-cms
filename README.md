# Simple-Wordpress-CMS

## Use case
You have an existing PHP project and want to use Wordpress as a CMS for specific pages. Wordpress can run on any external server. Embed the pages created with Wordpress into your PHP project. Simple-Wordpress-CMS performs the following tasks:

- Wordpress CSS files are automatically taken over. But the design of your existing site (e.g. header and footer) will remain unaffected. This is achieved by encapsulating CSS with Shadow DOM.
- Links and URLs are fixed automatically. Except images URLs, these remain the same, because images are stored on the Wordpress server.

## Installation

```console
$ composer require pammel/simple-wordpress-cms
```

## Quick start
- You have installed Wordpress and already created a page. The URL is e.g. https://my-wordpress.com/wp/my-first-page
- In your PHP project the Wordpress pages are accessible under the URL https://my-php-project.com/wp/<slug>

Use this code to embed the Wordpress page into your PHP project:

```php
use pammel\SimpleWordpressCms\Config;
use pammel\SimpleWordpressCms\Client;

$config = (new Config())
    ->setWordpressUrl('https://my-wordpress.com')
    ->setProjectUrl('https://my-php-project.com/wp')
    ->setCssFolderLocal('/var/www/my-php-project/public/css')
    ->setCssFolderPublicUrl('/css')
;

 $page = (new Client($config))->getPage('my-first-page');  
?>

<html>
 <head>
    <!-- 
       Here is your existing html-head, which probably already has some head-tags in it. 
    -->
    
    <? echo $page->getHtmlHead() ?>
 </head>
 
 <body>
  <!-- 
     This is your existing html-body. There is probably a header and footer and more. 
  -->
  
  <?php echo $page->getHtmlBody() ?>

 </body>
</html>
```

## Features
For more functionality look inside the two main classes 
- [Page](https://github.com/pammel/simple-wordpress-cms/blob/master/src/Page.php) 
- [Config](https://github.com/pammel/simple-wordpress-cms/blob/master/src/Config.php)

## Caching
It is highly recommended to cache the page object because parsing the WordPress page takes a few seconds. This is an example using Laravel:

```php
use Illuminate\Support\Facades\Cache;
use pammel\SimpleWordpressCms\Client;
use pammel\SimpleWordpressCms\Config;

$slug = 'my-first-page';

$cacheKey = md5('wordpress:' . $slug);
$page = Cache::remember($cacheKey, 3600, function() use($slug){
    $config = (new Config())
        ->setWordpressUrl('https://my-wordpress.com')
        ->setProjectUrl('https://my-php-project.com/wp')
        ->setCssFolderLocal('/var/www/my-php-project/public/css')
        ->setCssFolderPublicUrl('/css')
    ;

    return (new Client($config))->getPage($slug)->preprocess();
});

```

## Troubleshooting
- Don't use Wordpress themes that rely on JavaScript, especially for rendering.
- I recommend lightweight themes such as https://generatepress.com. It does not use Javascript and it does not cause problems with shortcodes.
- If there are not decoded "shortcodes" in your page, e.g. [vc_column_text]Hello World[/vc_column_text] you have to edit the function.php file of your wordpress theme and add something like this at the end of the file:
```php
# wp-content\themes\<your-theme>\function.php
 
 ...
 
/**
 * Modify REST API content for pages to force
 * shortcodes to render since Visual Composer does not
 * do this
 */
add_action( 'rest_api_init', function ()
{
    register_rest_field(
       'page',
       'content',
       array(
          'get_callback'    => 'compasshb_do_shortcodes',
          'update_callback' => null,
          'schema'          => null,
       )
    );
});

function compasshb_do_shortcodes( $object, $field_name, $request )
{
    WPBMap::addAllMappedShortcodes(); // This does all the work

    global $post;
    $post = get_post ($object['id']);
    $output['rendered'] = apply_filters( 'the_content', $post->post_content );

    // EDIT: add custom CSS to $output:
    $output[ 'yb_wpb_post_custom_css' ] = get_post_meta( $object[ 'id' ], '_wpb_post_custom_css', true);

    return $output;
}
```