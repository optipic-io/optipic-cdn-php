<?php

/**
 * OptiPic CDN library to convert image urls contains in html/text data
 *
 * @author optipic.io
 * @copyright (c) 2020, https://optipic.io
 */

namespace optipic\cdn;

class ImgUrlConverter {
    
    /**
     * ID of your site on CDN OptiPic.io service
     */
    public $siteId = 0;
    
    /**
     * List of domains should replace to cdn.optipic.io
     */
    public $domains = array();
    
    /**
     * Constructor
     */
    public function __construct($siteId=false, $domains=false) {
        if($siteId!==false) {
            $this->siteId = $siteId;
        }
        if($domains!==false) {
            $this->domains = $domains;
        }
    }
    
    /**
     * Convert whole HTML-block contains image urls
     */
    public function convertHtml($content) {
        $domains = $this->domains;
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
            $content = preg_replace('#("|\'|\()'.$host.'(/[^/"\'\s]{1}[^"\']*\.(png|jpg|jpeg){1}(\?.*?)?)("|\'|\))#simS', '${1}//cdn.optipic.io/site-'.$this->siteId.'${2}${5}', $content);
        }
        
        return $content;
    }
}
?>