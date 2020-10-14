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
     * List of URL exclusions - where is URL should not converted
     */
    public static $exclusionsUrl = array();
    
    /**
     * Constructor
     */
    public function __construct($config=array()) {
        if(is_array($config) && count($config)>0) {
            self::loadConfig($config);
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
        
        if(!self::isEnabled()) {
            return $content;
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
    public static function loadConfig($source=false) {
        if($source===false) {
            $source = __DIR__ . '/config.php';
        }
        
        if(is_array($source)) {
            self::$siteId = $source['site_id'];
            self::$domains = $source['domains'];
            self::$exclusionsUrl = $source['exclusions_url'];
        }
        elseif(file_exists($source)) {
            $config = require($source);
            if(is_array($config)) {
                self::loadConfig($config);
            }
        }
    }
    
    /**
     * Check if convertation enabled on current URL
     */
    public static function isEnabled() {
        $url = $_SERVER['REQUEST_URI'];
        if(in_array($url, self::$exclusionsUrl)) {
            return false;
        }
        return true;
    }
}
?>