<?php
require_once __DIR__.'/../optipic-cdn-php/ImgUrlConverter.php';

$converterOptiPic = new \optipic\cdn\ImgUrlConverter(array(
    'site_id' => 99999999,                                       // your SITE ID from CDN OptiPic controll panel
    'domains' => array('mydomain.com', 'www.mydomain.com'),      // list of domains should replace to cdn.optipic.io
    'exclusions_url' => array('/test/test/index.php',),          // list of URL exclusions - where is URL should not converted
    //'whitelist_img_urls' => array('/foo/bar/'),
    'whitelist_img_urls' => array('/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/q/', '/foo/bar/im'),
));

var_dump(\optipic\cdn\ImgUrlConverter::isEnabled());

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