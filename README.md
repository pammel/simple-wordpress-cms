# Simple-Wordpress-CMS

## Use case
You have an existing PHP project and want to use Wordpress as CMS for certain pages. This is an simple approach that allows Wordpress to be installed on any external server with any URL. 

## Installation

```console
$ composer require pammel/simple-wordpress-cms
```

## Quick start
- You have installed Wordpress and already created a page. 
- The URL is e.g. https://my-wordpress.com/wp/my-first-page

With this code you embedd your Wordpress page into your PHP project:

```php
use pammel\SimpleWordpressCms\Config;
use pammel\SimpleWordpressCms\Client;

$config = (new Config())
    ->setBlogUrl('https://my-wordpress.com')
    ->setCssFolderLocal('/var/www/my-php-project/public/css')
    ->setCssFolderPublicUrl('/css')
;

 $page = (new Client($config))->getPage('my-first-page');  
 $wordpressHtml = $page->getHtml();
 
?>

<html>
 <head>...</head>
 <body>
  This is your existing page. There is probably a header and footer here and more. 
  If you now embedd the Wordpress-HTML here, the design of your existing site will 
  remain unaffected. This is achieved by encapsulating CSS with Shadow DOM.
  
  <?php echo $wordpressHtml ?>

 </body>
</html>
```
