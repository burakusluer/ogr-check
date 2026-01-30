<?php

/*
Plugin Name: Ogr Check
Plugin URI: http://burakusluer.com.tr
Description: Öğrenci yoklaması için eklenti
Version: 1.0
Author: Burak Usluer
Requires at least: 7.2
Author URI: http://burakusluer.com.tr
*/

class OgrenciYoklama
{
    private static $instance=null;

    /**
     * singleton pattern kullanacağız bu sebeple kapattım hocam
     */
    private function __construct()
    {

    }

    /**
     * @return void
     * burası fonksiyonun instance ı klonlanmasın diye kapatıldı
     */
    private function __clone()
    {

    }

    /**
     * @return self|null
     * Tek nesne referansı singleton Pattern
     */
    public static function getInstance()
    {
        self::$instance??= new self();
        return self::$instance;
    }
}
OgrenciYoklama::getInstance();