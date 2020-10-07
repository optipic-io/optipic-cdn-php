# PHP / OptiPic
Lib for PHP to integrate with CDN OptiPic.io (automatic images optimization and compression service)
Use `ImgUrlConverter` class to **automatic convert all image URLSs in your HTML block/string**. 

## How to use
1. [Register](https://optipic.io/register/?cdn) your account on OptiPic.io site.
1. Add your site on OptiPic [CDN Control Panel](https://optipic.io/cdn/cp/).
1. Get your site **ID** from OptiPic CDN Control Panel sites list.
1. Download this lib to your site.
1. Use ImgUrlConverter::convertHtml() to convert whole HTML-block contains image urls.

## Example
```php
$converterOptiPic = new \optipic\cdn\ImgUrlConverter(
    99999999,                                       // your SITE ID from CDN OptiPic controll panel
    array('mydomain.com', 'www.mydomain.com')       // list of domains should replace to cdn.optipic.io
);

echo $converterOptiPic->convertHtml($html);
```

More live examples see in `/examples/example.php` script.