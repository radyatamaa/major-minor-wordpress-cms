<?php

defined('ABSPATH') or die();
/**
 * Handle the form submissions
 *
 * @package Package
 * @subpackage Sub Package
 */
class Banner_Form_Handler
{

    public function __construct()
    {
        add_action('admin_init', array($this, 'handle_form'));
    }
/**
 * Handle the audition event new and edit form
 *
 * @return void
 */

    public function handle_form()
    {
        if (!isset($_POST['submit_banner'])) {
            return;
        }

        // if (!wp_verify_nonce($_POST['_wpnonce'], 'audition-event-new')) {
        //     die(__('Are you cheating?', 'avt'));
        // }

        // if (!current_user_can('read')) {
        //     wp_die(__('Permission Denied!', 'avt'));
        // }

        $error = array();
        $page_url = admin_url('admin.php?page=banner');

        $page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : '';

        $field_id = isset($_GET['id']) ?intval($_GET['id']) : 0;
        $title = isset($_POST['title']) ? stripslashes(sanitize_text_field( $_POST['title'] )) : '';
        $file = file_exists($_FILES['file']['tmp_name']) ? $_FILES["file"] : '';
        $type = isset($_POST['type']) ? intval($_POST['type']) : 0;
        $status = isset($_POST['status']) ?intval( $_POST['status']) : 0 ;
        $user = wp_get_current_user();
        
        if ($file !== '')  {
            $media_type =  $_FILES['file']['type'];
            if($media_type == 'image/jpeg' || $media_type == 'image/jpg' || $media_type == 'image/png'){
            $unique_file = uniqid() . '.jpeg';
            $file['name'] =  $unique_file;
            }elseif($media_type == 'video/mp4'){
            $unique_file = uniqid() . '.mp4';
            $file['name'] =  $unique_file;
            }
            $imagePath = uploadBlob($file['name'],$file['tmp_name'],'file');
            if (!$file && is_wp_error($imagePath)) {
            $errors[] = __('Error : ' . $imagePath->get_error_messages(), 'bnr');
        }
        } 
        //is current_event?
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        //some basic validation
        // if (!$event_id) {
        //     $error[] = __('Error : Event is required', 'avt');
        // }

        // if (!$name) {
        //     $error[] = __('Error : Name is required', 'avt');
        // }

        // if (!$description) {
        //     $error[] = __('Failed : description  cannot be empty', 'avt');
        // }
        // if (strlen($description) < 4) {
        //     $error[] = __('Failed : description must be more than 3 character', 'avt');
        // }

        //bail out if error found
        if ($error) {
            $first_error = reset($error);
            $error = str_replace(' ', '_', $first_error);
            $redirect_to = add_query_arg(array('error' => $error), $page_url);
            wp_safe_redirect($redirect_to);
            exit;
        }

        $fields = array(
            'title' => $title,
            'url_file' =>  isset($imagePath) ? strval($imagePath) : '#',
            'status' => $status,
            'type' => $type
        );

        //New or Edit
        if (!$field_id) {
            $insert_id = create_banner($fields);
        } else {
            $fields['id'] = $field_id;
            $insert_id = update_banner($fields);
        }

        if (is_wp_error($insert_id)) {
            $error_codes =
            $insert_id->get_error_messages();
            $first_error = reset($error_codes);
            $error = str_replace(' ', '_', $first_error);
            $redirect_to = add_query_arg(array('error' => $error), $page_url);
        } else {
            $redirect_to = add_query_arg(array('message' => 'Success!'), $page_url);
        }
        wp_safe_redirect($redirect_to);
        exit;

    }
}

new Banner_Form_Handler();
