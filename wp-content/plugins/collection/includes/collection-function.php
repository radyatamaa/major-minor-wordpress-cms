<?php 
//Audition Event function
/**
 * Get all audition event
 * 
 * @param $args array
 * 
 * @return array
 */
// spl_autoload_register(function ($class) {
//     require_once str_replace("\\", "/", $class) . ".php";
// });

use windows_azure_storage\Common\ServicesBuilder;
use windows_azure_storage\Common\ServiceException;
use windows_azure_storage\Blob\Models\Block;
use windows_azure_storage\Blob\Models\BlockList;
use windows_azure_storage\Blob\Models\BlobBlockType;

function get_all_collection($args = array()){
    global $wpdb;
    $table_name = $wpdb->prefix.'collection';

    $defaults = array(
        'number'=>20,
        'offset' => 0,
        'orderby' => 'created_date',
        'order' => 'DESC',
    );

    $args = wp_parse_args($args , $defaults);
    $cache_key = 'collection-all';
    $items = wp_cache_get($cache_key, 'cln');

    if(false === $items){


        $query = 'SELECT * FROM '.$table_name.' WHERE is_deleted = 0';

        if(isset($_REQUEST['s']) && $_REQUEST['s'] != ''){
            $query = $query .' AND '.$table_name.'.name LIKE "%' .$_REQUEST['s']. '%"';
        }

        $query = $query. ' ORDER BY '.$table_name.'.'  .$args['orderby']. ' '. $args['order'] . ' LIMIT '. $args['offset'] . ', ' .$args['number'];
        $items = $wpdb->get_results($query);

        wp_cache_set($cache_key, $items,'cln');
    }

    return $items;

 }

function get_all_collection_api(){
    global $wpdb;
    $table_name = $wpdb->prefix.'collection';
    $collection_media = $wpdb->prefix.'collection_media';

    $query = "SELECT * FROM $table_name WHERE is_deleted = 0 AND status = 1";

    $items = $wpdb->get_results($query);

    foreach($items as $item){
        $query_collection_media = "SELECT * FROM $collection_media WHERE is_deleted = 0 AND collection_id = $item->id";

        $items_collection_media = $wpdb->get_results($query_collection_media);

        $item->collection_media =  $items_collection_media;
    }

    return $items;

} 

function get_by_id_collection_api($request = array()){
    global $wpdb;
    $table_name = $wpdb->prefix.'collection';
    $collection_media = $wpdb->prefix.'collection_media';
    $id = $request->get_param('id');
    $query = "SELECT * FROM $table_name WHERE is_deleted = 0 AND status = 1 AND id = $id";

    $item = $wpdb->get_row($query);

    $query_collection_media = "SELECT * FROM $collection_media WHERE is_deleted = 0 AND collection_id = $item->id";

    $items_collection_media = $wpdb->get_results($query_collection_media);

    $item->collection_media =  $items_collection_media;
    

    return $item;

} 
function get_collection_by_id($id){
    global $wpdb;
    $table_name = $wpdb->prefix.'collection';

    $query = "SELECT * FROM $table_name WHERE id = $id";

    $item = $wpdb->get_row($query);

    return $item;
 }
 function get_collection_media_by_collection_id($id){

    global $wpdb;
    $table_name = $wpdb->prefix.'collection_media';

    $query = "SELECT * FROM $table_name WHERE collection_id = $id AND is_deleted = 0";

    $items = $wpdb->get_results($query);

    return $items;
 }
 

function uploadBlobCollectionList($blobName,$realPath,$file_name,$index) {
    
    $accesskey = "KEUWRZs62sQxfwyBNVrpHCCfW87Jhx953tXeyhZGa8sLtBu2XmijsyCOitQa/G7ksDXx+UCxmoowds1heCHjWw==";
    $storageAccount = 'mijorminorstorage';
    // $filetoUpload = realpath('./' . $blobName);
    $filetoUpload = $_FILES[$file_name]['tmp_name'][$index];
    $containerName = 'major-minor';
    // $blobName = 'image.jpg';
    $media_type =  $_FILES[$file_name]['type'][$index];
    // if($media_type == 'image/jpeg' || $media_type == 'image/jpg' || $media_type == 'image/png'){
    //     $unique_file = uniqid() . '.jpeg';
    // }elseif($media_type == 'video/mp4'){
    //     $unique_file = uniqid() . '.mp4';
    // }
   
    $destinationURL = "https://$storageAccount.blob.core.windows.net/$containerName/$blobName";

    $currentDate = gmdate("D, d M Y H:i:s T", time());
    $handle = fopen($filetoUpload, "r");
    $fileLen = filesize($filetoUpload);

    $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$currentDate\nx-ms-version:2015-12-11";
    $urlResource = "/$storageAccount/$containerName/$blobName";

    

if($media_type == 'image/jpeg' || $media_type == 'image/jpg' || $media_type == 'image/png'){
    $arraysign = array();
    $arraysign[] = 'PUT';               /*HTTP Verb*/  
    $arraysign[] = '';                  /*Content-Encoding*/  
    $arraysign[] = '';                  /*Content-Language*/  
    $arraysign[] = $fileLen;            /*Content-Length (include value when zero)*/  
    $arraysign[] = '';                  /*Content-MD5*/  
    $arraysign[] = 'image/png';         /*Content-Type*/  
    $arraysign[] = '';                  /*Date*/  
    $arraysign[] = '';                  /*If-Modified-Since */  
    $arraysign[] = '';                  /*If-Match*/  
    $arraysign[] = '';                  /*If-None-Match*/  
    $arraysign[] = '';                  /*If-Unmodified-Since*/  
    $arraysign[] = '';                  /*Range*/  
    $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
    $arraysign[] = $urlResource;        /*CanonicalizedResource*/

    $str2sign = implode("\n", $arraysign);

    $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));  
    $authHeader = "SharedKey $storageAccount:$sig";

    $headers = [
        'Authorization: ' . $authHeader,
        'x-ms-blob-cache-control: max-age=3600',
        'x-ms-blob-type: BlockBlob',
        'x-ms-date: ' . $currentDate,
        'x-ms-version: 2015-12-11',
        'Content-Type: image/png',
        'Content-Length: ' . $fileLen
    ];

    $ch = curl_init($destinationURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_INFILE, $handle); 
    curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen); 
    curl_setopt($ch, CURLOPT_UPLOAD, true); 
    $result = curl_exec($ch);

    echo ('Result<br/>');
    print_r($result);

    echo ('Error<br/>');
    print_r(curl_error($ch));

    curl_close($ch);

}elseif($media_type == 'video/mp4'){
        $arraysign = array();
        $arraysign[] = 'PUT';               /*HTTP Verb*/  
        $arraysign[] = '';                  /*Content-Encoding*/  
        $arraysign[] = '';                  /*Content-Language*/  
        $arraysign[] = $fileLen;            /*Content-Length (include value when zero)*/  
        $arraysign[] = '';                  /*Content-MD5*/  
        $arraysign[] = 'video/mp4';         /*Content-Type*/  
        $arraysign[] = '';                  /*Date*/  
        $arraysign[] = '';                  /*If-Modified-Since */  
        $arraysign[] = '';                  /*If-Match*/  
        $arraysign[] = '';                  /*If-None-Match*/  
        $arraysign[] = '';                  /*If-Unmodified-Since*/  
        $arraysign[] = '';                  /*Range*/  
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

    $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));  
    $authHeader = "SharedKey $storageAccount:$sig";

    $headers = [
        'Authorization: ' . $authHeader,
        'x-ms-blob-cache-control: max-age=3600',
        'x-ms-blob-type: BlockBlob',
        'x-ms-date: ' . $currentDate,
        'x-ms-version: 2015-12-11',
        'Content-Type: video/mp4',
        'Content-Length: ' . $fileLen
    ];

    $ch = curl_init($destinationURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_INFILE, $handle); 
    curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen); 
    curl_setopt($ch, CURLOPT_UPLOAD, true); 
    $result = curl_exec($ch);

    echo ('Result<br/>');
    print_r($result);

    echo ('Error<br/>');
    print_r(curl_error($ch));

    curl_close($ch);
    }
    
    return $destinationURL;

    
}
 function delete_image_collection_media($id){
        global $wpdb;
        $collection_media = $wpdb->prefix.'collection_media';
        $current_user = wp_get_current_user();
        $args = array(
            // 'id' => $id,
            'modified_by' => $current_user->user_login,
            'is_deleted' => 1,
            'is_active' => 0
        );
        $wpdb->update($collection_media, $args, array('id' => $id));
        if (!$wpdb->result) {
            return new WP_Error('sql-failed', __('Warning : ' . $wpdb->last_error, 'cln'));
        }
 }
 /**
  * Fetch all audition event from database
  *
  * @return array
  */
  function get_count_collection(){
      global $wpdb;
      $table_name = $wpdb->prefix.'collection';

      $query = 'SELECT COUNT(*) FROM ' .$table_name .' WHERE is_deleted = 0';

      if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) {
        $query = $query . ' AND name LIKE "%' . $_REQUEST['s'] . '%"';
    }

    return (int)$wpdb->get_var($query);

}
function create_collection($args = array(),$listImage)
    {
        global $wpdb;
        $table_name= $wpdb->prefix.'collection';
        $collection_media= $wpdb->prefix.'collection_media';
        $current_user = wp_get_current_user();
        $defaults = array(
            'id' => null,
            'created_by' => $current_user->user_login,
            'is_deleted' => 0,
            'is_active' => 1
        );
        $args = wp_parse_args($args, $defaults);
        $wpdb->insert($table_name , $args);
        if(!$wpdb->result){            
            return new WP_Error('sql-failed', __('Error : ' . $wpdb->last_error, 'cln'));
        }
        
        $id = $wpdb->insert_id;
        foreach($listImage as $image){
            $create = array(
            'created_by' => $current_user->user_login,
            'media_type' => 1,
            'collection_id' => $id,
            'is_deleted' => 0,
            'is_active' => 1,
            'url_file' => $image           
        );
        $wpdb->insert($collection_media , $create);          
        }        
        return false;
    }
function update_collection($args = array(),$listImage){
        global $wpdb;
        $table_name= $wpdb->prefix.'collection';
        $collection_media= $wpdb->prefix.'collection_media';
        $current_user = wp_get_current_user();
        $defaults = array(
            'id' => null,
            'modified_by' => $current_user->user_login,
            'is_deleted' => 0,
            'is_active' => 1
        );
        $args = wp_parse_args($args, $defaults);
        $id = $args['id'];
        if (isset($args['url_file']) && $args['url_file'] === '#') unset($args['url_file']);
        $wpdb->update($table_name, $args, array('id' => $id));
        if (!$wpdb->result) {
            return new WP_Error('sql-failed', __('Warning : ' . $wpdb->last_error, 'cln'));
        }  

        foreach($listImage as $image){
            $create = array(
            'created_by' => $current_user->user_login,
            'media_type' => 1,
            'collection_id' => $id,
            'is_deleted' => 0,
            'is_active' => 1,
            'url_file' => $image           
        );
        $wpdb->insert($collection_media , $create);          
        }
    }

function delete_collection($id){
        global $wpdb;
        $table_name= $wpdb->prefix.'collection';
        $collection_media = $wpdb->prefix.'collection_media';
        $current_user = wp_get_current_user();
        $args = array(
            // 'id' => $id,
            'modified_by' => $current_user->user_login,
            'is_deleted' => 1,
            'is_active' => 0
        );
        $wpdb->update($table_name, $args, array('id' => $id));
        $wpdb->update($collection_media, $args, array('collection_id' => $id));
        if (!$wpdb->result) {
            return new WP_Error('sql-failed', __('Warning : ' . $wpdb->last_error, 'cln'));
        }
}
    

?>