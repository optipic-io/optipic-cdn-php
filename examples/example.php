<?php
require_once __DIR__.'/../optipic-cdn-php/ImgUrlConverter.php';

$converterOptiPic = new \optipic\cdn\ImgUrlConverter(/*array(
    'site_id' => 99999999,                                       // your SITE ID from CDN OptiPic controll panel
    'domains' => array('mydomain.com', 'www.mydomain.com'),      // list of domains should replace to cdn.optipic.io
    'srcset_attrs' => array('srcset', 'data-srcset'),
    //'exclusions_url' => array('/test/test/index.php',),          // list of URL exclusions - where is URL should not converted
    //'whitelist_img_urls' => array('/foo/bar/'),
    //'whitelist_img_urls' => array('/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/foo/bar/im'),
)*/);

$html = <<<HTML
<!DOCTYPE html> <html dir="ltr" prefix="og: http://ogp.me/ns#" lang="ru"> <head> <base asd href='https://test.com/test"> <base asd href='https://test.com/test"> <meta http-equiv="X-UA-Compatible" content="IE=edge"/> <meta name="yandex-verification" content="a74333f59aaa0616" /> <title>test</title> <meta name="description" content="test"> <meta name="keywords" content="test">
					<style>
		@font-face {
		  font-family: 'PT Sans';
		  font-style: normal;
		  font-display: swap;
		  font-weight: 400;
		  src: local('PT Sans'), local('PTSans-Regular'),
			   url('fonts/pt-sans-v11-latin_cyrillic-regular.woff2') format('woff2'), 
			   url('fonts/pt-sans-v11-latin_cyrillic-regular.woff') format('woff');
		}
HTML;

var_dump(\optipic\cdn\ImgUrlConverter::getBaseUrlFromHtml($html));

var_dump(\optipic\cdn\ImgUrlConverter::isEnabled());
var_dump(\optipic\cdn\ImgUrlConverter::getUrlFromRelative('test.png'));
var_dump(\optipic\cdn\ImgUrlConverter::getUrlFromRelative('test/test.png', '/foo/bar/'));
var_dump(\optipic\cdn\ImgUrlConverter::getUrlFromRelative('test.png', '/asdasd'));
var_dump(\optipic\cdn\ImgUrlConverter::getUrlFromRelative('test.png'));
var_dump(\optipic\cdn\ImgUrlConverter::getUrlFromRelative('/test.png'));

$htmls = array(
    // local urls
    '<img src="/foo/bar/img.png"/>',
    '<img data-src="/foo/bar/img.png"/>',
    "<img foo-bar-attr='/foo/bar/img.png'/>",
    '<img src="/foo/bar1/img.png"/>',
    '<img data-src="/foo/bar2/img.png"/>',
    "<img foo-bar-attr='/foo/bar3/img.png'/>",
    'backhround: url(/foo/bar/img.jpg)',
    'backhround: url("/foo/bar/img.jpg")',
    "backhround: url('/foo/bar/img.jpg')",
    // urls with your domain (http)
    '<img src="http://mydomain.com/foo/bar/img.png"/>',
    '<img data-src="http://mydomain.com/foo/bar/img.png"/>',
    "<img foo-bar-attr='http://mydomain.com/foo/bar/img.png'/>",
    'backhround: url(http://mydomain.com/foo/bar/img.jpg)',
    'backhround: url("http://mydomain.com/foo/bar/img.jpg")',
    "backhround: url('http://mydomain.com/foo/bar/img.jpg')",
    // urls with your domain (https)
    '<img src="https://mydomain.com/foo/bar/img.png"/>',
    '<img data-src="https://mydomain.com/foo/bar/img.png"/>',
    "<img foo-bar-attr='https://mydomain.com/foo/bar/img.png'/>",
    'backhround: url(https://mydomain.com/foo/bar/img.jpg)',
    'backhround: url("https://mydomain.com/foo/bar/img.jpg")',
    "backhround: url('https://mydomain.com/foo/bar/img.jpg')",
    // urls with third party domains (http & https)
    '<img src="https://NOTmydomain.com/foo/bar/img.png"/>',
    '<img data-src="https://NOTmydomain.com/foo/bar/img.png"/>',
    "<img foo-bar-attr='https://NOTmydomain.com/foo/bar/img.png'/>",
    'backhround: url(https://NOTmydomain.com/foo/bar/img.jpg)',
    'backhround: url("http://NOTmydomain.com/foo/bar/img.jpg")',
    "backhround: url('https://NOTmydomain.com/foo/bar/img.jpg')",
    '<img foo="bar" srcset="/foo/bar/img.png, /foo/bar/imgx1.5.png 1.5x, /foo/bar/imgx2.png 2x" src="/foo/bar/img.png" alt="test"/>',
    '<img foo="bar" srcset="https://mydomain.com/foo/bar/img.png, https://mydomain.com/foo/bar/imgx1.5.png 1.5x, https://mydomain.com/foo/bar/imgx2.png 2x" src="/foo/bar/img.png" alt="test" >',
    '<img foo="bar" srcset="https://NOTmydomain.com/foo/bar/img.png, https://NOTmydomain.com/foo/bar/imgx1.5.png 1.5x, https://NOTmydomain.com/foo/bar/imgx2.png 2x" src="https://NOTmydomain.com/foo/bar/img.png" alt="test"/>',
    '<picture>
        <source class="owl-lazy" data-srcset="/image/cache/catalog/foo.JPG 1x, /image/cache/catalog/foo-1600x1600.JPG 2x, /image/cache/catalog/foo-2400x2400.JPG 3x, /image/cache/catalog/foo-3200x3200.JPG 4x" srcset="/image/cache/catalog/frametheme/src_holder-800x800.png">
        <img src="/image/cache/catalog/frametheme/src_holder-800x800.png" data-src="/image/cache/catalog/foo-800x800.JPG" alt="foo bar" title="bar bar" class="img-fluid d-block mx-auto w-auto owl-lazy">
    </picture>',
    '<img src="/тест/кириллицы/картинка.png"/>',
    '<img data-src="https://mydomain.com/тест/кириллицы/картинка.png"/>',
    '<div class="grid-item  grid-item-v2" style="background-image: url(/wp-content/uploads/2020/07/type_apart.jpg)"></div>',
    '<img src="foo/bar/img.png"/>',
    '<div class="grid-item  grid-item-v2" style="background-image: url(wp-content/uploads/2020/07/type_apart.jpg)"></div>',
);

$results = array();
foreach($htmls as $html) {
    $results[] = $html." ---CONVERT--> ".$converterOptiPic->convertHtml($html)."\n";
}

$isCLI = php_sapi_name() === 'cli';

foreach($results as $result) {
    if($isCLI) {
        echo $result."\n";
    }
    else {
        
        echo htmlspecialchars($result)."<br/>";
    }
}


//echo implode($lineDelimiter, $results);
?>