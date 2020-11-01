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
     * Whitelist of images URL - what should to be converted 
     */
    public static $whitelistImgUrls = array();
    
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
            //$domain = str_replace(".", "\.", $domain);
            if($domain && stripos($domain, 'http://')!==0 && stripos($domain, 'https://')!==0) {
                $hostsForRegexp[] = 'http://'.$domain;
                $hostsForRegexp[] = 'https://'.$domain;
            }
            else {
                $hostsForRegexp[] = $domain;
            }
            
        }
        foreach($hostsForRegexp as $host) {
            
            /*$firstPartsOfUrl = array();
            foreach(self::$whitelistImgUrls as $whiteImgUrl) {
                if(substr($whiteImgUrl, -1, 1)=='/') {
                    $whiteImgUrl = substr($whiteImgUrl, 0, -1);
                }
                $firstPartsOfUrl[] = preg_quote($host.$whiteImgUrl, '#');
            }
            if(count($firstPartsOfUrl)==0) {
                $firstPartsOfUrl[] = preg_quote($host, '#');
            }
            //var_dump($firstPartsOfUrl);
            //$host = preg_quote($host, '#');
            //var_dump(self::$whitelistImgUrls);
            
            $host = implode('|', $firstPartsOfUrl);
            var_dump($host);*/
            
            /*$firstPartsOfUrl = array();
            foreach(self::$whitelistImgUrls as $whiteImgUrl) {
                $firstPartsOfUrl[] = preg_quote($whiteImgUrl, '#');
            }
            if(empty($firstPartsOfUrl)) {
                $firstPartsOfUrl = array('/');
            }
            
            $firstPartOfUrl = implode('|', $firstPartsOfUrl);
            */
            
            $host = preg_quote($host, '#');
            
            $firstPartOfUrl = '/';
            
            $regexp = '#("|\'|\()'.$host.'('.$firstPartOfUrl.'[^/"\'\s]{1}[^"\']*\.(png|jpg|jpeg){1}(\?.*?)?)("|\'|\))#simS';
            //$regexp = str_replace('//', '/');
            
            //$content = preg_replace($regexp, '${1}//cdn.optipic.io/site-'.self::$siteId.'${2}${5}', $content);
            $content = preg_replace_callback($regexp, array(__NAMESPACE__ .'\ImgUrlConverter', 'callbackForPregReplace'), $content);
            
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
            if(!is_array(self::$domains)) {
                self::$domains = array();
            }
            self::$domains = array_unique(self::$domains);
            
            self::$exclusionsUrl = $source['exclusions_url'];
            if(!is_array(self::$exclusionsUrl)) {
                self::$exclusionsUrl = array();
            }
            self::$exclusionsUrl = array_unique(self::$exclusionsUrl);
            
            self::$whitelistImgUrls = $source['whitelist_img_urls'];
            if(!is_array(self::$whitelistImgUrls)) {
                self::$whitelistImgUrls = array();
            }
            self::$whitelistImgUrls = array_unique(self::$whitelistImgUrls);
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
    
    /**
     * Callback-function for preg_replace() to replace image URLs
     */
    public static function callbackForPregReplace($matches) {
        $urlOriginal = $matches[2];
        
        $replaceWithoutOptiPic = $matches[0];
        $replaceWithOptiPic = $matches[1].'//cdn.optipic.io/site-'.self::$siteId.$urlOriginal.$matches[5];
        
        if(empty(self::$whitelistImgUrls)) {
            return $replaceWithOptiPic;
        }
        
        if(in_array($urlOriginal, self::$whitelistImgUrls)) {
            return $replaceWithOptiPic;
        }
        
        foreach(self::$whitelistImgUrls as $whiteUrl) {
            if(strpos($urlOriginal, $whiteUrl)===0) {
                return $replaceWithOptiPic;
            }
        }
        
        return $replaceWithoutOptiPic;
        
    }
}
?>