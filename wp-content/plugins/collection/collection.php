<?php
/*
* Plugin Name: Collection
* Description: This plugin for Collection Management.
* Version: 1.0
* Author: PT. Moonlay Technologies
* Author URI: http://www.moonlay.com/
* License: GPL2
* Text Domain: Collection
* Domain Path: /languages
*/

defined('ABSPATH') or die();

require(ABSPATH . '/wp-load.php');


function collection_install(){
    global $wpdb;
    global $collection_db_version;

    $table_name = $wpdb->prefix .'collection';
    $collection_media = $wpdb->prefix .'collection_media';

    $sql ="CREATE TABLE  {$table_name} (
    id int(11) NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    description LONGTEXT NOT NULL,
    url_file LONGTEXT NOT NULL,
    release_date DATE NOT NULL,
    status TINYINT(1) NOT NULL,
    media_type int(11) NOT NULL,
    created_by varchar(50) NOT NULL,
    modified_by varchar(50) NOT NULL,
    created_date TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
    modified_date TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) NOT NULL,
    is_active TINYINT(1) NOT NULL,
    PRIMARY KEY(id)
    ) ENGINE=INNODB;
    
    CREATE TABLE  {$collection_media} (
    id int(11) NOT NULL AUTO_INCREMENT,
    collection_id int(11) NOT NULL,
    url_file LONGTEXT NOT NULL,
    media_type int(11) NOT NULL,
    is_front_image TINYINT(1) NOT NULL,
    created_by varchar(50) NOT NULL,
    modified_by varchar(50) NOT NULL,
    created_date TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
    modified_date TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) NOT NULL,
    is_active TINYINT(1) NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (collection_id) REFERENCES wp_collection(id)
    ) ENGINE=INNODB;";


    require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('collection_db_version', $collection_db_version);

    $installed_ver = get_option('collection_db_version');
    
    if($installed_ver != $collection_db_version){
        require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option('collection_db_version', $collection_db_version);


    }


}


register_activation_hook(__FILE__, 'collection_install');


add_action('init', function(){

    include dirname(__FILE__) . '/includes/class-collection-admin-menu.php';
    include dirname(__FILE__) . '/includes/class-collection-list-table.php';
    include dirname(__FILE__).'/includes/class-form-handle.php';
    include dirname(__FILE__). '/includes/collection-function.php';

    new collection_admin_menu();
 });

 function collection_script(){
      //jQuery UI file
    wp_enqueue_script('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'), '1.12.1');
    //CSS UI theme css file
    wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css', false, '1.12.1');
    // //jQuery Time Picker
    // wp_enqueue_script('jquery-timepicker', 'https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js', array('jquery'), '2.2.0');
    // //CSS Time Picker theme css file
    // wp_enqueue_style('jquery-timepicker-css', 'https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css', false, '2.2.0');
    //jQuery HTML Editor by tiny mce
    wp_enqueue_script('jquery-html-editor', 'https://cdn.ckeditor.com/4.11.1/standard/ckeditor.js', array('jquery'),'4.11.1');
 }


 add_action('admin_enqueue_scripts' , 'collection_script');


 add_action('rest_api_init', function(){
    register_rest_route(
        'mm',
        '/collection',
        array(
            'methods' => 'GET',
            'callback' => 'get_all_collection_api',
        )
    );

    
    register_rest_route(
        'mm',
        '/collection_id',
        array(
            'methods' => 'GET',
            'callback' => 'get_by_id_collection_api',
        )
    );
});

