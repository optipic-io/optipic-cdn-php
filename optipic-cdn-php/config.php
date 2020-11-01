<?php
/**
 * OptiPic CDN configuration file
 * 
 * @author optipic.io
 * @package https://github.com/optipic-io/optipic-cdn-php
 * @copyright (c) 2020, https://optipic.io
 */
return array(
    'site_id' => '0',     // your SITE ID from CDN OptiPic controll panel
    'domains' => array(), // list of domains should replace to cdn.optipic.io
    'exclusions_url' => array(), // list of URL exclusions - where is URL should not converted
    'whitelist_img_urls' => array(), // whitelist of images URL - what should to be converted (parts or full urls start from '/')
);
?>