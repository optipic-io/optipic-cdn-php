# PHP / OptiPic
Lib for PHP to integrate with CDN OptiPic.io (automatic images optimization and compression service)
Use `ImgUrlConverter` class to **automatic convert all image URLs on your site**. 

## How to use
1. [Register](https://optipic.io/register/?cdn) your account on OptiPic.io site.
1. Add your site on OptiPic [CDN Control Panel](https://optipic.io/cdn/cp/).
1. Get your site **ID** from OptiPic CDN Control Panel sites list.
1. Download this lib to your site.
1. Use this library according use cases below

## Administration area to install lib and configure it
After downloaded all code of this repository you may open admin area in your browser:

https://mydomain.com/optipic-cdn-php/admin/admin.php?key=b9k7o34rnfc5kco6m7fmjrts7u
![cdn optipic admin area](https://cdn.optipic.io/site-520/img/cdn/optipic-cdn-php-admin-area-screen-1.png)


## Use case #1: Automatic convert image URLs using 'auto_prepend_file' php-directive

Inlude our `/optipic-cdn-php/auto_prepend_file.php` file in `auto_prepend_file` php.ini directive.

You may do it in `.user.ini` or `php.ini` or `.htaccess`.

### Example using `.htaccess`

```
php_value auto_prepend_file "<SITE_ROOT_DIRECTORY>/optipic-cdn-php/optipic-cdn-php/auto_prepend_file.php"
```

### Example using `php.ini` or `.user.ini`

```
; Automatically add files before PHP document.
; http://php.net/auto-prepend-file
auto_prepend_file = <SITE_ROOT_DIRECTORY>/optipic-cdn-php/optipic-cdn-php/auto_prepend_file.php
```

### Hosting limitations to using `auto_prepend_file` php-directive

Some hostings allow you to set `auto_prepend_file` only upon request to support.  
Examples of such hostings:
* ukraine.com.ua (https://www.ukraine.com.ua/wiki/hosting/php/auto_prepend_file/)

## Use case #2: Automatic convert image URLs using your site's entry point php-script

You may include our 1-line converter in on the top of your site's entry point (e.g. `/index.php`).

```php
require_once __DIR__.'/optipic-cdn-php/optipic-cdn-php/ImgUrlConverter.php';

ob_start(array('\optipic\cdn\ImgUrlConverter', 'convertHtml'));

....
<YOUR ENTRY POINT LOGIC>
....
```

## Use case #3: Manually using convert function of our lib in your code

You may use our lib to convert image URLs exactly where you want in your project to convert whole HTML-block contains image URLs.

```php
$converterOptiPic = new \optipic\cdn\ImgUrlConverter(array(
    'site_id' => 99999999,                                       // your SITE ID from CDN OptiPic controll panel
    'domains' => array('mydomain.com', 'www.mydomain.com'),      // list of domains should replace to cdn.optipic.io
    'exclusions_url' => array('/test/test/index.php',),          // list of URL exclusions - where is URL should not converted
    'whitelist_img_urls' => array(),                             // whitelist of images URL - what should to be converted (parts or full urls start from '/')
    'srcset_attrs' => array('srcset', 'data-srcset'),            // tag's srcset attributes // @see https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images
));

$htmlConverted = $converterOptiPic->convertHtml($html);
```

## Configuration

### Option #1: Using file config.php in lib's root

File format: 

```php
return array(
    'site_id' => '0',                // your SITE ID from CDN OptiPic controll panel
    'domains' => array(),            // list of domains should replace to cdn.optipic.io
    'exclusions_url' => array(),     // list of URL exclusions - where is URL should not converted
    'whitelist_img_urls' => array(), // whitelist of images URL - what should to be converted (parts or full urls start from '/')
    'srcset_attrs' => array('srcset', 'data-srcset'), // tag's srcset attributes // @see https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images
);
```

In this case config will be automatic loaded in `auto_prepend_file.php`.

### Option #2: Using ImgUrlConverter::loadConfig();

```php
// Autoload config from config.php
ImgUrlConverter::loadConfig();

// Load config from custom config file
ImgUrlConverter::loadConfig('<path-to-your-config-file.php>');

// Load config from array
ImgUrlConverter::loadConfig(array(
    'site_id' => '0',                // your SITE ID from CDN OptiPic controll panel
    'domains' => array(),            // list of domains should replace to cdn.optipic.io
    'exclusions_url' => array(),     // list of URL exclusions - where is URL should not converted
    'whitelist_img_urls' => array(), // whitelist of images URL - what should to be converted (parts or full urls start from '/')
    'srcset_attrs' => array('srcset', 'data-srcset'), // tag's srcset attributes // @see https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images
));
```

### Option #3: Pass config data into ImgUrlConverter::__constructor();

```php
$converterOptiPic = new \optipic\cdn\ImgUrlConverter(array(
    'site_id' => 99999999,                                       // your SITE ID from CDN OptiPic controll panel
    'domains' => array('mydomain.com', 'www.mydomain.com'),      // list of domains should replace to cdn.optipic.io
    'exclusions_url' => array('/test/test/index.php',),          // list of URL exclusions - where is URL should not converted
    'whitelist_img_urls' => array(),                             // whitelist of images URL - what should to be converted (parts or full urls start from '/')
));
```

### Option #1: Using file config.php in lib's root

## What will be converted (examples)
```
<img src="/foo/bar/img.png"/> ---CONVERT--> <img src="//cdn.optipic.io/site-99999999/foo/bar/img.png"/>
<img data-src="/foo/bar/img.png"/> ---CONVERT--> <img data-src="//cdn.optipic.io/site-99999999/foo/bar/img.png"/>
<img foo-bar-attr='/foo/bar/img.png'/> ---CONVERT--> <img foo-bar-attr='//cdn.optipic.io/site-99999999/foo/bar/img.png'/>
backhround: url(/foo/bar/img.jpg) ---CONVERT--> backhround: url(//cdn.optipic.io/site-99999999/foo/bar/img.jpg)
backhround: url("/foo/bar/img.jpg") ---CONVERT--> backhround: url("//cdn.optipic.io/site-99999999/foo/bar/img.jpg")
backhround: url('/foo/bar/img.jpg') ---CONVERT--> backhround: url('//cdn.optipic.io/site-99999999/foo/bar/img.jpg')
<img src="http://mydomain.com/foo/bar/img.png"/> ---CONVERT--> <img src="//cdn.optipic.io/site-99999999/foo/bar/img.png"/>
<img data-src="http://mydomain.com/foo/bar/img.png"/> ---CONVERT--> <img data-src="//cdn.optipic.io/site-99999999/foo/bar/img.png"/>
<img foo-bar-attr='http://mydomain.com/foo/bar/img.png'/> ---CONVERT--> <img foo-bar-attr='//cdn.optipic.io/site-99999999/foo/bar/img.png'/>
backhround: url(http://mydomain.com/foo/bar/img.jpg) ---CONVERT--> backhround: url(//cdn.optipic.io/site-99999999/foo/bar/img.jpg)
backhround: url("http://mydomain.com/foo/bar/img.jpg") ---CONVERT--> backhround: url("//cdn.optipic.io/site-99999999/foo/bar/img.jpg")
backhround: url('http://mydomain.com/foo/bar/img.jpg') ---CONVERT--> backhround: url('//cdn.optipic.io/site-99999999/foo/bar/img.jpg')
<img src="https://mydomain.com/foo/bar/img.png"/> ---CONVERT--> <img src="//cdn.optipic.io/site-99999999/foo/bar/img.png"/>
<img data-src="https://mydomain.com/foo/bar/img.png"/> ---CONVERT--> <img data-src="//cdn.optipic.io/site-99999999/foo/bar/img.png"/>
<img foo-bar-attr='https://mydomain.com/foo/bar/img.png'/> ---CONVERT--> <img foo-bar-attr='//cdn.optipic.io/site-99999999/foo/bar/img.png'/>
backhround: url(https://mydomain.com/foo/bar/img.jpg) ---CONVERT--> backhround: url(//cdn.optipic.io/site-99999999/foo/bar/img.jpg)
backhround: url("https://mydomain.com/foo/bar/img.jpg") ---CONVERT--> backhround: url("//cdn.optipic.io/site-99999999/foo/bar/img.jpg")
backhround: url('https://mydomain.com/foo/bar/img.jpg') ---CONVERT--> backhround: url('//cdn.optipic.io/site-99999999/foo/bar/img.jpg')
<img src="https://NOTmydomain.com/foo/bar/img.png"/> ---CONVERT--> <img src="https://NOTmydomain.com/foo/bar/img.png"/>
<img data-src="https://NOTmydomain.com/foo/bar/img.png"/> ---CONVERT--> <img data-src="https://NOTmydomain.com/foo/bar/img.png"/>
<img foo-bar-attr='https://NOTmydomain.com/foo/bar/img.png'/> ---CONVERT--> <img foo-bar-attr='https://NOTmydomain.com/foo/bar/img.png'/>
backhround: url(https://NOTmydomain.com/foo/bar/img.jpg) ---CONVERT--> backhround: url(https://NOTmydomain.com/foo/bar/img.jpg)
backhround: url("http://NOTmydomain.com/foo/bar/img.jpg") ---CONVERT--> backhround: url("http://NOTmydomain.com/foo/bar/img.jpg")
backhround: url('https://NOTmydomain.com/foo/bar/img.jpg') ---CONVERT--> backhround: url('https://NOTmydomain.com/foo/bar/img.jpg')
```

More live examples see in `/examples/example.php` script.

## Where is `php.ini` on different hostings

### Beget
`<SITE_ROOT_DIRECTORY>/cgi-bin/php.ini`

### Reg.ru
https://www.reg.ru/support/hosting-i-servery/yazyki-programmirovaniya-i-skripty/kak-izmenit-parametry-php

If it does not work you also may create/edit file `.user.ini` in the site's root directory - alternative of php.ini file.
https://www.php.net/manual/en/configuration.file.per-user.php

## `ionCube` Warning
If you use ionCube on your site you shouldn't add `auto_prepend_file` direcive in php settings.  
The `ionCube` does not support the directive `auto_prepend_file` and [throws an error](https://www.ioncube.com/sa/gui_docs/settings_restrictions.html).  
Instead, add the OptiPic connection directly to your site's entry point (usually an index.php) as stated above.

### Example for `ImageCMS` (it's using `ionCube`)
Into `index.php` insert OptiPic logic before including `core/CodeIgniter.php`
```php
// OptiPic
// --------------------------------------------------------------------
if(php_sapi_name() !== 'cli') {
    require_once __DIR__.'/optipic-cdn-php/optipic-cdn-php/ImgUrlConverter.php';
    ob_start(array('\optipic\cdn\ImgUrlConverter', 'convertHtml'));
}
// --------------------------------------------------------------------

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH . 'core/CodeIgniter.php';
```
## Which URLs we recommend to exclude based on your CMS

### Wordpress
`/wp-admin/*`

### Bitrix
`/bitrix/*`

### Joomla
`/administrator/*`

### Drupal
`/en/admin/*`  
`/ru/admin/*`  
/<YOUR_LANG>/admin/*

### MODX
`/manager/*`

### OpenCart
`/admin/*`

### HostCMS
`/admin/*`

### Other CMS
Exclude your CMS admin area (control panel) folder with mask `*` at the end to exclude all nested urls
