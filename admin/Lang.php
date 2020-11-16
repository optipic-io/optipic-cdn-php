<?php
namespace optipic\cdn;

class Lang {
    public static $lang = 'en';
    public static $langs = array('en', 'ru');
    public static $t = array();
    
    public static function init($langFilePath, $lang=false) {
        if(!empty($lang) && in_array($lang, self::$langs)) {
            self::$lang = $lang;
        }
        if($langFilePath) {
            $langFilePath = $langFilePath.'.'.self::$lang;
            if(file_exists($langFilePath)) {
                self::$t = require($langFilePath);
            }
        }
    }
    
    public static function t($index) {
        if(!empty(self::$t[$index])) {
            return self::$t[$index];
        }
        return '';
    }
}
?>