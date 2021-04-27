<?php
/**
 * Detect script for Admin PHP-library of OptiPic CDN integration
 */
 
$classExists = class_exists('\optipic\cdn\ImgUrlConverter');

if(!$classExists) {
    $fullpathToLib = __DIR__ . '/../../optipic-cdn-php/ImgUrlConverter.php';
    if(file_exists($fullpathToLib)) {
        //var_dump(file_exists($fullpathToLib)); 
        require_once $fullpathToLib;
    }
}

$converterClassReflection = new \ReflectionClass('\optipic\cdn\ImgUrlConverter');
$pathToClass = realpath($converterClassReflection->getFileName());
//var_dump($pathToClass);
$pathToPrependFileRight = dirname($pathToClass).'/auto_prepend_file.php';

$pathToPrependFileInDirective = realpath(ini_get('auto_prepend_file'));

$isOk = $pathToPrependFileInDirective && ($pathToPrependFileRight==$pathToPrependFileInDirective);

if(!empty($_GET['find_variant'])) {
    //echo $_GET['find_variant'];
    if($_GET['find_variant']=='htaccess') {
        if(empty($_GET['find_variant_run'])) {
            file_put_contents(__DIR__ . '/.htaccess.for-test', 'php_value auto_prepend_file "'.$pathToPrependFileRight.'"');
            @rename(__DIR__ . '/.htaccess.for-test', __DIR__ . '/.htaccess');
            header("Location: detect.php?find_variant={$_GET['find_variant']}&find_variant_run=1&key=".$currentKey);
        }
        else {
            @rename(__DIR__ . '/.htaccess', __DIR__ . '/.htaccess.for-test');
            header('Content-Type: application/json');
            echo json_encode(array('ok' => $isOk));
            exit;
        }
        
    }
    
    if($_GET['find_variant']=='user-ini') {
        if(empty($_GET['find_variant_run'])) {
            file_put_contents(__DIR__ . '/.user.ini.for-test', 'auto_prepend_file = '.$pathToPrependFileRight);
            @rename(__DIR__ . '/.user.ini.for-test', __DIR__ . '/.user.ini');
            header("Location: detect.php?find_variant={$_GET['find_variant']}&find_variant_run=1&key=".$currentKey);
        }
        else {
            @rename(__DIR__ . '/.user.ini', __DIR__ . '/.user.ini.for-test');
            header('Content-Type: application/json');
            echo json_encode(array('ok' => $isOk));
            exit;
        }
        
    }
    
    if($_GET['find_variant']=='php-ini') {
        if(empty($_GET['find_variant_run'])) {
            file_put_contents(__DIR__ . '/php.ini.for-test', 'auto_prepend_file = '.$pathToPrependFileRight);
            @rename(__DIR__ . '/php.ini.for-test', __DIR__ . '/php.ini');
            header("Location: detect.php?find_variant={$_GET['find_variant']}&find_variant_run=1&key=".$currentKey);
        }
        else {
            @rename(__DIR__ . '/php.ini', __DIR__ . '/php.ini.for-test');
            header('Content-Type: application/json');
            echo json_encode(array('ok' => $isOk));
            exit;
        }
        
    }
    
    
    
    exit;
}

header('Content-Type: application/json');
echo json_encode(array('ok' => $isOk));
//exit;
?>