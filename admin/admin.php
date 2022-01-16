<?php
/**
 * Admin script for PHP-library of OptiPic CDN integration
 * version x.y.z
 * |------ x.y   - version of main lib \optipic\cdn\ImgUrlConverter
 * |---------- z - version of admin script
 */
define("OPTIPIC_PHP_CDN_ADMIN_VERSION", "9");

include_once __DIR__.'/Lang.php';

use \optipic\cdn\Lang;

Lang::init(__FILE__, (!empty($_GET['lang'])? $_GET['lang']: false));

$tempDetectionFiles = array(
    'php.ini.for-test',
    'php.ini',
    '.user.ini.for-test',
    '.user.ini',
    '.htaccess.for-test',
    '.htaccess',
);
foreach($tempDetectionFiles as $tempDetectionFile) {
    $tempDetectionFilePath = __DIR__ . '/detect-connection/'.$tempDetectionFile;
    if(file_exists($tempDetectionFilePath)) {
        @unlink($tempDetectionFilePath);
    }
}

$classExists = class_exists('\optipic\cdn\ImgUrlConverter');

if(!$classExists) {
    $fullpathToLib = __DIR__ . '/../optipic-cdn-php/ImgUrlConverter.php';
    if(file_exists($fullpathToLib)) {
        require_once $fullpathToLib;
    }
}

$OPTIPIC_PHP_CDN_ADMIN_VERSION_FULL = \optipic\cdn\ImgUrlConverter::VERSION.'.'.OPTIPIC_PHP_CDN_ADMIN_VERSION;

$pathToClass = '';
$pathToPrependFileRight = '';

    $converterClassReflection = new \ReflectionClass('\optipic\cdn\ImgUrlConverter');
    $pathToClass = realpath($converterClassReflection->getFileName());
    $pathToPrependFileRight = dirname($pathToClass).'/auto_prepend_file.php';

$pathToPrependFileInDirective = realpath(ini_get('auto_prepend_file'));

$isOk = $pathToPrependFileInDirective && ($pathToPrependFileRight==$pathToPrependFileInDirective);
//var_dump($isOk);

$successCssClasses = array(
    0 => 'text-danger',
    1 => 'text-success',
);

$integrationVariants = array(
    '.htaccess' => false,
    '.user.ini' => false,
    'php.ini' => false,
);

define('DEF_KEY', 'b9k7o34rnfc5kco6m7fmjrts7u');
$configFullPath = '';
$config = array(
    'admin_key' => DEF_KEY,
    'site_id' => '',     // your SITE ID from CDN OptiPic controll panel
    'domains' => array(), // list of domains should replace to cdn.optipic.io
    'exclusions_url' => array(), // list of URL exclusions - where is URL should not converted
    'whitelist_img_urls' => array(), // whitelist of images URL - what should to be converted (parts or full urls start from '/')
);

if(class_exists('\optipic\cdn\ImgUrlConverter')) {
    \optipic\cdn\ImgUrlConverter::loadConfig();
    if(!empty(\optipic\cdn\ImgUrlConverter::$adminKey)) {
        $config['admin_key'] = \optipic\cdn\ImgUrlConverter::$adminKey;
    }
}

$currentUrlLocal = $_SERVER['QUERY_STRING'];
$currentKey = (!empty($_GET['key']))? $_GET['key']: '';
//var_dump($currentKey);
//var_dump($config['admin_key']);
//var_dump(DEF_KEY);

if($currentKey!=$config['admin_key']) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}

if($classExists) {
    $configFullPath = \optipic\cdn\ImgUrlConverter::$configFullPath;
    
    if(!empty($_POST['optipicConfig']) && is_array($_POST['optipicConfig'])) {
        $_POST['optipicConfig']['site_id'] = intval($_POST['optipicConfig']['site_id']);
        $_POST['optipicConfig']['domains'] = explode("\n", $_POST['optipicConfig']['domains']);
        $_POST['optipicConfig']['exclusions_url'] = explode("\n", $_POST['optipicConfig']['exclusions_url']);
        $_POST['optipicConfig']['whitelist_img_urls'] = explode("\n", $_POST['optipicConfig']['whitelist_img_urls']);
        $_POST['optipicConfig']['srcset_attrs'] = explode("\n", $_POST['optipicConfig']['srcset_attrs']);
        $_POST['optipicConfig']['admin_key'] = trim($_POST['optipicConfig']['admin_key']);
        $_POST['optipicConfig']['cdn_domain'] = trim($_POST['optipicConfig']['cdn_domain']);
        
        $newConfig = $_POST['optipicConfig'];
        foreach(array('domains', 'exclusions_url', 'whitelist_img_urls', 'srcset_attrs') as $configKey) {
            if(is_array($newConfig[$configKey])) {
                foreach($newConfig[$configKey] as $ind=>$row) {
                    $newConfig[$configKey][$ind] = trim($row);
                    if(empty($newConfig[$configKey][$ind])) {
                        unset($newConfig[$configKey][$ind]);
                    }
                }
            }
            else {
                $newConfig[$configKey] = array();
            }
            $newConfig[$configKey] = array_unique($newConfig[$configKey]);
        }
        
        $configStr = "<?php\nreturn ".var_export($newConfig, true).";\n?>";
        file_put_contents($configFullPath, $configStr);
        \optipic\cdn\ImgUrlConverter::loadConfig();
        
        if($currentKey!=\optipic\cdn\ImgUrlConverter::$adminKey) {
            header('Location: '.basename(__FILE__).'?lang='.Lang::$lang.'&key='.\optipic\cdn\ImgUrlConverter::$adminKey);
            exit;
        }
    }
    
    $config['site_id'] = \optipic\cdn\ImgUrlConverter::$siteId;
    $config['domains'] = \optipic\cdn\ImgUrlConverter::$domains;
    $config['exclusions_url'] = \optipic\cdn\ImgUrlConverter::$exclusionsUrl;
    $config['whitelist_img_urls'] = \optipic\cdn\ImgUrlConverter::$whitelistImgUrls;
    if(!empty(\optipic\cdn\ImgUrlConverter::$adminKey)) {
        $config['admin_key'] = \optipic\cdn\ImgUrlConverter::$adminKey;
    }
    $config['srcset_attrs'] = \optipic\cdn\ImgUrlConverter::$srcsetAttrs;
    
    if(empty($config['domains'])) {
        $config['domains'] = \optipic\cdn\ImgUrlConverter::getDefaultSettings('domains');
    }
    if(empty($config['srcset_attrs'])) {
        $config['srcset_attrs'] = \optipic\cdn\ImgUrlConverter::getDefaultSettings('srcset_attrs');
    }
    if(empty($config['exclusions_url'])) {
        $config['exclusions_url'] = array(
            '/wp-admin/*',
            '/bitrix/*',
            '/administrator/*',
            '/en/admin/*',
            '/ru/admin/*',
            '/es/admin/*',
            '/manager/*',
            '/admin.php',
            '/admin.php*',
            '/admin*',
            '/backend/*',
            '/webasyst/*',
        );
    }
    $config['cdn_domain'] = \optipic\cdn\ImgUrlConverter::$cdnDomain;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo Lang::t('title')?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="https://optipic.io/favicon.ico" type="image/x-icon">
    <style>
    .optipic-settings label {
        font-weight: bold;
    }
    #tabs-list.nav-tabs a {
        background-color: inherit;
        color: #007bff;
    }
    h2, .h2 {
        font-size: 2rem !important;
    }
    h2 small {
        font-size: 20px;
    }
    pre {
        margin: 0 10px 10px 10px;
        padding: 10px;
        border: 1px dashed #6472a9;
    }
    #accordionInstall .card-header .badge.show-details, 
    #accordionInstall .card-header .badge.hide-details {
        position: absolute;
        top: 10px;
        right: 7px;
        
        border-bottom: 1px dashed #000;
        padding: 0 0 2px 0;
        border-radius: 0;
    }
    #accordionInstall .card-header .show-details {
        display: none;
    }
    #accordionInstall .card-header .hide-details {
        display: block;
    }
    #accordionInstall .card-header.collapsed .show-details {
        display: block;
    }
    #accordionInstall .card-header.collapsed .hide-details {
        display: none;
    }
    #accordionInstall .card-header h3 .badge {
        font-size: 15px;
    }
    </style>
</head>

<body>

    <div class="position-absolute small p-2" style="top:0; right: 0;"><a href="https://github.com/optipic-io/optipic-cdn-php" target="_blank">v<?=$OPTIPIC_PHP_CDN_ADMIN_VERSION_FULL?></a></div>

    <div class="container">
        <div class="text-center">
            <a class="navbar-brand" href="https://optipic.io/<?php echo Lang::$lang?>/cdn/" target="_blank">
                <img src="https://optipic.io/optipic-logo.png" class="d-inline-block align-top" alt="" loading="lazy">
            </a>
        </div>
        <div class="text-center">
        <?php foreach(Lang::$langs as $l): ?>
            <a href="?lang=<?php echo $l?>&key=<?php echo $currentKey?>" class="btn btn-outline-dark btn-sm"><?php echo strtoupper($l)?></a>
        <?php endforeach; ?>
            <a href="https://github.com/optipic-io/optipic-cdn-php" target="_blank" class="btn btn-outline-dark btn-sm"><?php echo Lang::t('lib_on_github')?></a>
        </div>
        
        <h1 class="text-center mt-3"><?php echo Lang::t('title')?> <a href="https://optipic.io/<?php echo Lang::$lang?>/cdn/" target="_blank">CDN OptiPic</a></h1>
        
        <ul id="tabs-list" class="nav nav-tabs mt-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?php if(!$isOk):?>active<?php endif;?>" id="tab-install" data-toggle="tab" href="#tabInstall" role="tab" aria-selected="true">
                    <?php echo Lang::t('tab_install')?>
                    <?php if($isOk):?>
                    <span class="badge badge-info"><?php echo Lang::t('installed')?></span>
                    <?php endif;?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if($isOk):?>active<?php endif;?>" data-toggle="tab" href="#tabConfig" role="tab"><?php echo Lang::t('tab_config')?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabPhpInfo" role="tab"><?php echo Lang::t('tab_phpinfo')?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabSupport" role="tab"><?php echo Lang::t('tab_support')?></a>
            </li>
        </ul>
        
        <div class="tab-content mt-3 mb-5">
            <div class="tab-pane <?php if(!$isOk):?>active<?php endif;?>" id="tabInstall" role="tabpanel">

                <h2 class="text-center mb-5"><?php echo Lang::t(4)?>: 
                    <span class="<?php echo $successCssClasses[intval($isOk)]?>">
                    <?php echo ($isOk? Lang::t(2): Lang::t(3))?>
                    </span>
                </h2>

                <h2>
                    <?php echo Lang::t(5)?>
                    <small class="text-muted"><?php echo Lang::t('5_small')?></small>
                </h2>
                
                <div class="accordion" id="accordionInstall">
                    <div class="card">
                        <div class="card-header collapsed" id="install-htaccess-head" type="button" data-toggle="collapse" data-target="#install-htaccess-collapse" aria-expanded="true" aria-controls="install-htaccess-collapse">
                            <h3>
                                <?php echo Lang::t(6)?> <code>.htaccess</code> <span id="htaccess-pass" class="badge badge-success d-none"><?php echo Lang::t(0)?></span>
                            </h3>
                            <span class="badge show-details"><?php echo Lang::t('show_instructions')?></span>
                            <span class="badge hide-details"><?php echo Lang::t('hide_instructions')?></span>
                        </div>
                        <div id="install-htaccess-collapse" class="collapse" aria-labelledby="install-htaccess-head" data-parent="#accordionInstall">
                            <div class="card-body">
                                <p><?php echo Lang::t(11)?> <code><?php echo $_SERVER['DOCUMENT_ROOT']?>/.htaccess</code>:</p>
                                <pre>php_value auto_prepend_file "<?php echo $pathToPrependFileRight?>"</pre>
                                <p><small><?php echo Lang::t(12)?></small></p>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header collapsed" id="install-userini-head" type="button" data-toggle="collapse" data-target="#install-userini-collapse" aria-expanded="true" aria-controls="install-userini-collapse">
                            <h3>
                                <?php echo Lang::t(6)?> <code>.user.ini</code> <span id="user-ini-pass" class="badge badge-success d-none"><?php echo Lang::t(0)?></span>
                            </h3>
                            <span class="badge show-details"><?php echo Lang::t('show_instructions')?></span>
                            <span class="badge hide-details"><?php echo Lang::t('hide_instructions')?></span>
                        </div>
                        <div id="install-userini-collapse" class="collapse" aria-labelledby="install-userini-head" data-parent="#accordionInstall">
                            <div class="card-body">
                                <p><?php echo Lang::t(11)?> <code><?php echo $_SERVER['DOCUMENT_ROOT']?>/.user.ini</code>:</p>
                                <pre>
; Automatically add files before PHP document.
; http://php.net/auto-prepend-file
auto_prepend_file = <?php echo $pathToPrependFileRight?></pre>
                                <p><small><?php echo Lang::t(12)?></small></p>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header collapsed" id="install-phpini-head" type="button" data-toggle="collapse" data-target="#install-phpini-collapse" aria-expanded="true" aria-controls="install-phpini-collapse">
                            <h3>
                                <?php echo Lang::t(6)?> <code>php.ini</code> <span id="php-ini-pass" class="badge badge-success d-none"><?php echo Lang::t(0)?></span>
                            </h3>
                            <span class="badge show-details"><?php echo Lang::t('show_instructions')?></span>
                            <span class="badge hide-details"><?php echo Lang::t('hide_instructions')?></span>
                        </div>
                        <div id="install-phpini-collapse" class="collapse" aria-labelledby="install-phpini-head" data-parent="#accordionInstall">
                            <div class="card-body">
                                <p><?php echo Lang::t(11)?> <code><?php echo $_SERVER['DOCUMENT_ROOT']?>/php.ini</code>:</p>
                                <pre>
; Automatically add files before PHP document.
; http://php.net/auto-prepend-file
auto_prepend_file = <?php echo $pathToPrependFileRight?></pre>
                                <p><small><?php echo Lang::t(12)?></small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="mt-5"><?php echo Lang::t(7)?></h2>

                <h4 class="mt-3"><?php echo Lang::t(8)?></h4>
                <pre><?php echo $_SERVER['DOCUMENT_ROOT']?></pre>

                <h4 class="mt-3"><?php echo Lang::t(9)?> auto_prepend_file</h4>
                <pre><?php echo $pathToPrependFileRight?></pre>
            </div>
            
            <div class="tab-pane <?php if($isOk):?>active<?php endif;?>" id="tabConfig" role="tabpanel">
                
                <?php if(!$classExists):?>
                <div class="alert alert-danger" role="alert">
                    <?php echo Lang::t('error_no_class')?>
                </div>
                <div class="text-center text-muted">
                    <?php echo Lang::t('no_form')?>
                </div>
                <?php else:?>
                <form method="post" class="optipic-settings">
                    
                    <div class="form-group">
                        <label for="optipic_admin_key"><?php echo Lang::t('admin_key')?></label>
                        <input type="text" name="optipicConfig[admin_key]" class="form-control" id="optipic_admin_key" aria-describedby="optipic_admin_key_help" value="<?php echo $config['admin_key']?>">
                        <?php if($currentKey==DEF_KEY):?>
                        <small id="optipic_admin_key_help" class="form-text text-danger"><?php echo Lang::t('admin_key_helper')?></small>
                        <?php endif;?>
                    </div>
                
                    <div class="form-group">
                        <label for="optipic_site_id"><?php echo Lang::t('sid')?></label>
                        <input type="number" name="optipicConfig[site_id]" class="form-control" id="optipic_site_id" aria-describedby="optipic_site_id_help" value="<?php echo $config['site_id']?>">
                        <small id="optipic_site_id_help" class="form-text text-muted"><?php echo Lang::t('sid_helper')?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="optipic_domains"><?php echo Lang::t('domains')?></label>
                        <textarea name="optipicConfig[domains]" class="form-control" id="optipic_domains" rows="3"><?php echo implode("\n", $config['domains'])?></textarea>
                        <small id="emailHelp" class="form-text text-muted">
                            <?php echo Lang::t('domains_helper')?>
                            <?php echo Lang::t('examples:')?><br/>
                            mydomain.com<br/>
                            www.mydomain.com
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="optipic_exclusions_url"><?php echo Lang::t('exclusions_url')?></label>
                        <textarea name="optipicConfig[exclusions_url]" class="form-control" id="optipic_exclusions_url" rows="3"><?php echo implode("\n", $config['exclusions_url'])?></textarea>
                        <small id="emailHelp" class="form-text text-muted">
                            <?php echo Lang::t('exclusions_url_helper')?>
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="optipic_whitelist_img_urls"><?php echo Lang::t('whitelist_img_urls')?></label>
                        <textarea name="optipicConfig[whitelist_img_urls]" class="form-control" id="optipic_whitelist_img_urls" rows="3"><?php echo implode("\n", $config['whitelist_img_urls'])?></textarea>
                        <small id="emailHelp" class="form-text text-muted">
                            <?php echo Lang::t('whitelist_img_urls_helper')?>
                            <?php echo Lang::t('examples:')?><br/>
                            /upload/<br/>
                            /upload/test.jpeg
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="optipic_srcset_attrs"><?php echo Lang::t('srcset_attrs')?></label>
                        <textarea name="optipicConfig[srcset_attrs]" class="form-control" id="optipic_srcset_attrs" rows="3"><?php echo implode("\n", $config['srcset_attrs'])?></textarea>
                        <small id="emailHelp" class="form-text text-muted">
                            <?php echo Lang::t('srcset_attrs_helper')?><br/>
                            <a href="https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images" target="_blank"><?php echo Lang::t('srcset_definition')?></a><br/>
                            <?php echo Lang::t('examples:')?><br/>
                            srcset<br/>
                            data-srcset
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="optipic_cdn_domain"><?php echo Lang::t('cdn_domain')?></label>
                        <input type="text" name="optipicConfig[cdn_domain]" class="form-control" id="optipic_cdn_domain" aria-describedby="optipic_cdn_domain_help" value="<?php echo $config['cdn_domain']?>">
                        <small id="optipic_cdn_domain_help" class="form-text text-muted"><?php echo Lang::t('cdn_domain_helper')?></small>
                    </div>
                    
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-lg"><?php echo Lang::t('submit')?></button>
                        <div class="small text-muted mt-1">
                            <?php echo Lang::t('cfg_path')?>:<br/>
                            <code><?php echo $configFullPath?></code>
                        </div>
                    </div>
                </form>
                <?php endif;?>
            </div>
            <div class="tab-pane" id="tabPhpInfo" role="tabpanel">
                <?php if(function_exists('phpinfo')): ?>
                    <?php echo phpinfo(
                        INFO_GENERAL 
                        | INFO_CREDITS 
                        | INFO_CONFIGURATION 
                        // This part of phpinfo() may be blocked on some hostings - so phpinfo() should not use without params.
                        /*| INFO_MODULES */
                        | INFO_ENVIRONMENT 
                        | INFO_VARIABLES 
                        | INFO_LICENSE
                    );?>
                <?php else: ?>
                    WARNING: PHP-function 'phpinfo()' disabled on your hosting.
                <?php endif; ?>
            </div>
            <div class="tab-pane text-center" id="tabSupport" role="tabpanel">
                <a href="https://optipic.io/get-free-help/?cdn=1" target="_blank" class="btn btn-primary btn-lg"><?php echo Lang::t('get_support')?></a>
            </div>
        </div>
        
        
        
        
    </div>

<?php if(!$isOk):?>
<script>
$(function() {
    $("#tab-install").click(function() {
        $("#accordionInstall h3 .badge").addClass("d-none");
        $.get('detect-connection/detect.php', {find_variant: 'htaccess'}, function(data) {
            console.log(data);
            if(typeof data.ok != 'undefined' && data.ok==true) {
                $("#htaccess-pass").removeClass("d-none");
            }
            
            $.get('detect-connection/detect.php', {find_variant: 'user-ini'}, function(data) {
                console.log(data);
                if(typeof data.ok != 'undefined' && data.ok==true) {
                    $("#user-ini-pass").removeClass("d-none");
                }
                
                $.get('detect-connection/detect.php', {find_variant: 'php-ini'}, function(data) {
                    console.log(data);
                    if(typeof data.ok != 'undefined' && data.ok==true) {
                        $("#php-ini-pass").removeClass("d-none");
                    }
                });
            });
        });
    });
    $("#tab-install").trigger("click");
});
</script>
<?php endif;?>
<?
$siteId = !empty($config['site_id'])? $config['site_id']: '';
?>
<script src="https://optipic.io/api/cp/stat?domain=<?=$_SERVER["HTTP_HOST"]?>&sid=<?=$siteId?>&cms=php-cdn&stype=cdn&append_to=.container:first&version=<?=$OPTIPIC_PHP_CDN_ADMIN_VERSION_FULL?>&source=<?=\optipic\cdn\ImgUrlConverter::getDownloadSource()?>"></script>

</body>
</html>