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
