<?php
/*
Plugin Name: TRD: Core
Plugin URI: https://www.therealdeal.com
Description: Core Features for The Real Deal Website access all the marker website. 
Version: 1.0
Author: Sunil Patel
Author URI: https://www.sunil523.com
Text Domain: trd
*/

define('TRD_CORE_PATH', plugin_dir_path( __FILE__ ) );

// autoload the class from the plugin
spl_autoload_register(function($className){
    $className = str_replace('TRD\\', '', $className);
    $className = str_replace('\\', '/', $className);
    $filePath = plugin_dir_path(__FILE__).$className.'.php';
    if(file_exists($filePath)) require_once $filePath;
});

new \TRD\Core\Init();
new \TRD\Widgets\Init();
new \TRD\ShortCode\Init();
?>