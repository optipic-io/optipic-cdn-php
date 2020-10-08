<?php

/**
 * OptiPic CDN library to convert image urls contains in html/text data
 *
 * @author optipic.io
 * @package https://github.com/optipic-io/optipic-cdn-php
 * @copyright (c) 2020, https://optipic.io
 */

namespace optipic\cdn;

class ImgUrlConverter {
    
    /**
     * ID of your site on CDN OptiPic.io service
     */
    public static $siteId = 0;
    
    /**
     * List of domains should replace to cdn.optipic.io
     */
    public static $domains = array();
    
    /**
     * Constructor
     */
    public function __construct($siteId=false, $domains=false) {
        if($siteId!==false) {
            self::$siteId = $siteId;
        }
        if($domains!==false) {
            self::$domains = $domains;
        }
    }
    
    /**
     * Convert whole HTML-block contains image urls
     */
    public static function convertHtml($content) {
        
        // try auto load config from __DIR__.'config.php'
        if(empty(self::$siteId)) {
            self::loadConfig();
        }
        
        $domains = self::$domains;
        if(!is_array($domains)) {
            $domains = array();
        }
        $domains = array_merge(array(''), $domains);
        
        $hostsForRegexp = array();
        foreach($domains as $domain) {
            $domain = str_replace(".", "\.", $domain);
            if($domain && stripos($domain, 'http://')!==0 && stripos($domain, 'https://')!==0) {
                $hostsForRegexp[] = 'http://'.$domain;
                $hostsForRegexp[] = 'https://'.$domain;
            }
            else {
                $hostsForRegexp[] = $domain;
            }
            
        }
        foreach($hostsForRegexp as $host) {
            $content = preg_replace('#("|\'|\()'.$host.'(/[^/"\'\s]{1}[^"\']*\.(png|jpg|jpeg){1}(\?.*?)?)("|\'|\))#simS', '${1}//cdn.optipic.io/site-'.self::$siteId.'${2}${5}', $content);
        }
        
        return $content;
    }
    
    /**
     * Load config from file or array
     */
    public static function loadConfig($source=__DIR__.'/config.php') {
        if(is_array($source)) {
            self::$siteId = $source['site_id'];
            self::$domains = $source['domains'];
        }
        elseif(file_exists($source)) {
            $config = require($source);
            self::$siteId = $config['site_id'];
            self::$domains = $config['domains'];
        }
    }
}
?>