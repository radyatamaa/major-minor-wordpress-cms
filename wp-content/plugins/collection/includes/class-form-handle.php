<?php

defined('ABSPATH') or die();
/**
 * Handle the form submissions
 *
 * @package Package
 * @subpackage Sub Package
 */
class Collection_Form_Handler
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
        if (!isset($_POST['submit_collection'])) {
          if (!isset($_POST['delete_image'])) {
                return;
            }
        }

        // if (!wp_verify_nonce($_POST['_wpnonce'], 'audition-event-new')) {
        //     die(__('Are you cheating?', 'avt'));
        // }

        // if (!current_user_can('read')) {
        //     wp_die(__('Permission Denied!', 'avt'));
        // }
        if(isset($_POST['delete_image'])){
            $id = $_POST['delete_image'];
            $id = str_replace('(', '', $id);
            $id = str_replace(')', '', $id);
            $id = str_replace('X', '', $id);
            $deleted = delete_image_collection_media($id);
            $url = admin_url('admin.php?page=collection&action=edit&id=' . $_GET['id']);
            wp_safe_redirect($url);
            return;
        }
        $error = array();
        $page_url = admin_url('admin.php?page=collection');

        $page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : '';

        $field_id = isset($_GET['id']) ?intval($_GET['id']) : 0;
        $title = isset($_POST['title']) ? stripslashes(sanitize_text_field( $_POST['title'] )) : '';
        $description = isset($_POST['description']) ? stripslashes(sanitize_text_field( $_POST['description'] )) : '';
        $file_banner = file_exists($_FILES['file_banner']['tmp_name']) ? $_FILES["file_banner"] : '';
        $file_collections = file_exists($_FILES['file_collection']['tmp_name'][0]) ? $_FILES["file_collection"] : '';
        $media_type = isset($_POST['media_type']) ? intval($_POST['media_type']) : 0;
        $status = isset($_POST['status']) ?intval( $_POST['status']) : 0 ;
        $is_front_image = isset($_POST['is_front_image']) ?intval( $_POST['is_front_image']) : 0 ;
        if($_POST['release_date'] !== '') { $release_date = date('Y-m-d',strtotime($_POST['release_date'])); } else { $release_date = ''; }
        // $file_collection = reArrayFiles($_FILES['file_collection']['tmp_name']) ? $_FILES["file_collection"] : [];
        $user = wp_get_current_user();
       
        if ($file_banner !== '')  {
            $media_type =  $_FILES['file_banner']['type'];
            if($media_type == 'image/jpeg' || $media_type == 'image/jpg' || $media_type == 'image/png'){
            $unique_file = uniqid() . '.jpeg';
            $file_banner['name'] =  $unique_file;
            }elseif($media_type == 'video/mp4'){
            $unique_file = uniqid() . '.mp4';
            $file_banner['name'] =  $unique_file;
            }
            $imagePath = uploadImageCollectionBlob($file_banner['name'],$file_banner['tmp_name'],'file_banner');
            if (!$file_banner && is_wp_error($imagePath)) {
            $errors[] = __('Error : ' . $imagePath->get_error_messages(), 'bnr');
            }
            if($media_type != 'video/mp4'){               
            $deleteImageTemp =  dirname(__FILE__)."/".$unique_file;
            unlink($deleteImageTemp);
            }
            
        } 
        if($file_collections !== ''){
            $imagePathCollections = [];
            foreach($file_collections['tmp_name'] as $i => $val){
                $unique_file = uniqid() . '.jpeg';
                $file_collections['name'][$i] =  $unique_file;
                $imagePathCollection = uploadBlobCollectionList($file_collections['name'][$i],$val,'file_collection',$i);
                 if (!$file_collections && is_wp_error($imagePath)) {
                 $errors[] = __('Error : ' . $imagePath->get_error_messages(), 'bnr');
            }
            $deleteImageTemp =  dirname(__FILE__)."/".$unique_file;
            unlink($deleteImageTemp);
            array_push($imagePathCollections,$imagePathCollection);
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
            'description' => $description,
            'url_file' => isset($imagePath) ? strval($imagePath) : '#',
            'status' => $status,
            'media_type' => $media_type,
            'release_date' => $release_date
        );

        //New or Edit
        if (!$field_id) {
            $insert_id = create_collection($fields,$imagePathCollections);
        } else {
            $fields['id'] = $field_id;
            $insert_id = update_collection($fields,$imagePathCollections,$is_front_image);
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

new Collection_Form_Handler();
