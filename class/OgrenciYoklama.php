<?php

namespace class;

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


    /**
     * @return void
     * Eklenti etkinleştirildiğinde oluşturulması gereken tablolar
     */
    public static function pluginActivation(): void
    {
        OgrenciYoklama::getInstance()->tables();
    }

    public function tables()
    {
        ob_start();
        global $wpdb;
//        $wpdb->show_errors();
        $tableStudentsCheckList = "{$wpdb->prefix}students_check_list";
        $charset = $wpdb->get_charset_collate();
        $DDLQueries = [
            "CREATE TABLE $tableStudentsCheckList (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        attendance longtext NOT NULL,
        year smallint NOT NULL,
        month tinyint NOT NULL,
        teacher longtext null,
        reading longtext null,
        PRIMARY KEY  (id),
        UNIQUE KEY student_month (user_id,year,month)
    ) $charset;"
        ];
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($DDLQueries);
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log("WordPress Aktivasyon Çıktısı: " . $output);
        }
    }

    /**
     * @return void
     * ajax ile öğrencilerin yoklama request i buraya geliyor
     * nonce check(csrf) ve yetki denetlemeleri burada yapılıyor
     */
    public function ogrHereHandle()
    {
        check_ajax_referer('ogr-here-action', 'nonce');

        if (get_current_user_id() == $_POST['userId']) {
            //user için yoklama alındı bilgisi işlenecek
            wp_send_json_success(['status' => 'ok',]);
        } else {
            wp_send_json_error([
                'message' => 'You are not Authorized',
            ], 403);
        }
    }

    /**
     * @return void
     * css ,js assetleri load ediyor shortcode([ogr-takip]) varsa
     */
    public function ogrTakipLoadAssets()
    {
        global $wpdb;
        $tableStudentsCheckList = "{$wpdb->prefix}students_check_list";
        if (shortcode_exists("ogr-takip")) {
            $data['wp_nonce'] = wp_create_nonce("ogr-here-action");
            $data['ajax_url'] = admin_url('admin-ajax.php');
            $data['users'] = $wpdb->get_results("select users.ID,user_nicename,attendance,year,month from {$wpdb->users} as users  left outer join {$tableStudentsCheckList} as checklist on users.ID=checklist.user_id and year=year(curdate()) and month=month(curdate())");
            $data['days'] = date('t');
            wp_enqueue_script("ogrTakipTableJs", plugin_url . "/assets/js/ogr_takip_table.min.js");
            wp_add_inline_script("ogrTakipTableJs", 'const ogrTakipMainData=' . wp_json_encode($data));
            wp_enqueue_style("ogrTakipTableCss", plugin_url . "/assets/css/ogr_takip_table.css");
        }
    }


    public function ogrTakipCallback()
    {
        $days = date("t");
        ob_start();
        ?>
        <div class="ogr-takip-main">
            <table class="ogr-takip-table" id="ogr-takip-table">
                <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Öğrenci</th>
                    <th>Hoca</th>
                    <th class="color-green">Okuma</th>
                    <?php for ($i = 1; $i <= $days; $i++) { ?>
                        <th class="color-red"><?php echo $i; ?></th>
                    <?php } ?>
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