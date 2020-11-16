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
    'srcset_attrs' => array('srcset', 'data-srcset'), // tag's srcset attributes // @see https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images
    'admin_key' => 'b9k7o34rnfc5kco6m7fmjrts7u', // access key for admin area (installation and configuration helper)
);
?>