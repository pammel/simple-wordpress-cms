# Simple-Wordpress-CMS

## Use case
You have an existing PHP project and want to use Wordpress as a CMS for specific pages. Wordpress can run on any external server. Embed the pages created with Wordpress into your PHP project. Simple-Wordpress-CMS performs the following tasks:

- Wordpress CSS files are automatically taken over. But the design of your existing site (e.g. header and footer) will remain unaffected. This is achieved by encapsulating CSS with Shadow DOM.
- Links and URLs are converted automatically. Except images URLs, these remain the same, because images are stored on the Wordpress server.

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
    
    <? echo $page->getHtmlHeader() ?>
 </head>
 
 <body>
  <!-- 
     This is your existing html-body. There is probably a header and footer and more. 
  -->
  
  <?php echo $page->getHtmlBody() ?>

 </body>
</html>
```

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


# Troubleshooting
- Don't use Wordpress themes that rely on JavaScript. JavaScript inside Wordpress is not supported with this approach.
- I recommend lightweight themes such as https://generatepress.com
