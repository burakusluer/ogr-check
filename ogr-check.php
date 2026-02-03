<?php

use class\OgrenciYoklama;

defined("ABSPATH") or die();
/*
Plugin Name: Ogr Check
Plugin URI: http://burakusluer.com.tr
Description: Öğrenci yoklaması için eklenti
Version: 1.0
Author: Burak Usluer
PHP version: 8.0
Author URI: http://burakusluer.com.tr
*/
require_once plugin_dir_path(__FILE__)."./class/OgrenciYoklama.php";
register_activation_hook(__FILE__, ["class\\OgrenciYoklama","pluginActivation"]);

OgrenciYoklama::getInstance();