<?php
/**
 * Script to auto convert image urls using 'auto_prepend_file' directive (use it into php.ini or .htaccess).
 * Also you may include this file on the top of your site's entry point (e.g. main /index.php)
 * 
 * @see https://github.com/optipic-io/optipic-cdn-php
 * @see https://www.php.net/manual/ru/ini.core.php#ini.auto-prepend-file
 *
 * @author optipic.io
 * @package https://github.com/optipic-io/optipic-cdn-php
 * @copyright (c) 2020, https://optipic.io
 */

require_once __DIR__.'/ImgUrlConverter.php';

\optipic\cdn\ImgUrlConverter::loadConfig();

if(\optipic\cdn\ImgUrlConverter::isEnabled()) {
    ob_start(array('\optipic\cdn\ImgUrlConverter', 'convertHtml'));
}
?>