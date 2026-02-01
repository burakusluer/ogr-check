<?php

/*
Plugin Name: Ogr Check
Plugin URI: http://burakusluer.com.tr
Description: Öğrenci yoklaması için eklenti
Version: 1.0
Author: Burak Usluer
PHP version: 8.0
Author URI: http://burakusluer.com.tr
*/

class OgrenciYoklama
{
    private static $instance = null;

    /**
     * singleton pattern kullanacağız bu sebeple kapattım hocam
     */
    private function __construct()
    {
        add_shortcode("ogr-takip", [$this, "ogrTakipCallback"]);
        add_action("wp_enqueue_scripts", [$this, "ogrTakipLoadAssets"]);
        add_action("wp_ajax_ogr-here-action", [$this, "ogrHereHandle"]);
//        add_action("wp_ajax_nopriv_ogr-here-action", [$this, "ogrHereHandle"]);
    }

    public function ogrHereHandle()
    {
        check_ajax_referer('ogr_here_action', 'nonce');
        if (get_current_user_id() == $_POST['userId']) {
            //user için yoklama alındı bilgisi işlenecek
            wp_send_json_success(['status'=>'ok']);
        } else {
            wp_send_json_error([
                'message' =>'You are not Authorized'
            ],403);
        }
    }

    /**
     * @return void
     * css ,js assetleri load ediyor shortcode([ogr-takip]) varsa
     */
    public function ogrTakipLoadAssets()
    {
        if (shortcode_exists("ogr-takip")) {
            $data['wp_nonce'] = wp_create_nonce("ogr-here-action");
            $data['ajax_url'] = admin_url('admin-ajax.php');
            $data['users'] = get_users([
                'orderby' => ["ID", "ASC"]
            ]);
            wp_enqueue_script("ogrTakipTableJs", plugin_dir_url(__FILE__) . "/assets/js/ogr_takip_table.min.js");
            wp_add_inline_script("ogrTakipTableJs", 'const ogrTakipMainData=' . wp_json_encode($data), "after");
            wp_enqueue_style("ogrTakipTableCss", plugin_dir_url(__FILE__) . "/assets/css/ogr_takip_table.css");
        }
    }

    public function ogrTakipCallback()
    {

        ob_start();
        ?>
        <div class="ogr-takip-main">
            <table class="ogr-takip-table" id="ogr-takip-table">
                <thead>
                <tr>
                    <th>Öğrenci Adı</th>
                    <th>Öğrenci Numarası</th>
                    <th>Yoklama!</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
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
        self::$instance ??= new self();
        return self::$instance;
    }
}

OgrenciYoklama::getInstance();