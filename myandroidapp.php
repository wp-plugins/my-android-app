<?php
/**
 * @package My Android App
 * @version 1.0
 */
/*
Plugin Name: My Android App
Plugin URI: http://myandroidapp.ahsan.pk
Description: Get a Native android app for wordpress blog. Send notification to users.
Author: Alpha Developers
Version: 1.0
Author URI: http://myandroidapp.ahsan.pk
*/

register_activation_hook( __FILE__, 'myandroidapp_activate' );
add_action( 'publish_post', 'myandroidapp_push_notification', 10, 2 );
// Add options page on WP admin panel

add_action('admin_menu', 'myandroidapp_add_page');
//call register settings function

add_action( 'admin_init', 'myandroidapp_admin_init' );

// handle registration requests
add_action( 'parse_request', 'handle_registration_requests', 0 );

function myandroidapp_admin_init() {

	//register our settings

	register_setting( 'myandroidapp_options', 'myandroidapp_plugin_options');

}

function myandroidapp_add_page() {

add_menu_page('My Android App', 'My Android App', 'administrator', 'myandroidapp', 'myandroidapp_page', plugins_url('/images/icon.png', __FILE__));

}

function myandroidapp_page() {
	
	 include 'myoptions.php';
}

// add devices table to database on activation.
function myandroidapp_activate(){
     global $wpdb;

   $table_name = $wpdb->prefix . "devices"; 
   

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      device_id varchar(200) DEFAULT '' NOT NULL,
      UNIQUE KEY id (id)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
   
    return;
}

//send push notification to devices when new post is published.

function myandroidapp_push_notification( $ID, $post ) {
     global $wpdb;

   $devices_table = $wpdb->prefix . "devices"; 
    $author = $post->post_author; /* Post author ID. */
    $name = get_the_author_meta( 'display_name', $author );
    //$email = get_the_author_meta( 'user_email', $author );
    $title = $post->post_title;
    $permalink = get_permalink( $ID );
    $msg = $title.' by '.$name;
    //$edit = get_edit_post_link( $ID, '' );
    
             include_once 'GCM.php';
             $url=get_site_url();
             $registatration_ids = array();
             
            $ids = $wpdb->get_results( "SELECT device_id FROM $devices_table" );
            if ($ids == NULL){
                $result = 'No Registered devices';
            }
            else
            {
                foreach ($ids as $reg_id){
                  //  $ids =$id['device_id'];
                    $registration_ids [] = $reg_id->device_id;
                }
                $gcm = new GCM();
             $message = array( "url" => $permalink,
                    "message" => $msg );
             $result = $gcm->send_notification($registration_ids, $message);
            }
    
}

//function to handle device registrations
 function handle_registration_requests() {
     
       $options = get_option('myandroidapp_plugin_options');
        
        if (isset($_GET['action']) && $_GET['action'] == 'register_app') {
           
            if (isset($_POST['key']) ) {
                $secret_key = sanitize_text_field($_POST['key']);
                $device_id= sanitize_text_field($_POST['regid']);
                
                if ($secret_key != $options['secretkey']){
                    return;
                }
            }
            else return;

            if (strlen($device_id) < 100){
                return;
            }

            global $wpdb;

            $devices_table = $wpdb->prefix . "devices";

            //insert id to database
            $wpdb->insert( $devices_table, array( 
                            'device_id' => $device_id
                    ), 
                    array( 
                            '%s'
                    ) 
                     );
            
            die('1');
            
        }

    }
