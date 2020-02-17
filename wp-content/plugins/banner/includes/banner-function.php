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

function get_all_banner($args = array()){
    global $wpdb;
    $table_name = $wpdb->prefix.'banner';

    $defaults = array(
        'number'=>20,
        'offset' => 0,
        'orderby' => 'created_date',
        'order' => 'DESC',
    );

    $args = wp_parse_args($args , $defaults);
    $cache_key = 'banner-all';
    $items = wp_cache_get($cache_key, 'bnr');

    if(false === $items){


        $query = 'SELECT * FROM '.$table_name.' WHERE is_deleted = 0';

        if(isset($_REQUEST['s']) && $_REQUEST['s'] != ''){
            $query = $query .' AND '.$table_name.'.name LIKE "%' .$_REQUEST['s']. '%"';
        }

        $query = $query. ' ORDER BY '.$table_name.'.'  .$args['orderby']. ' '. $args['order'] . ' LIMIT '. $args['offset'] . ', ' .$args['number'];
        $items = $wpdb->get_results($query);

        wp_cache_set($cache_key, $items,'bnr');
    }

    return $items;

 }

function get_all_banner_api(){
    global $wpdb;
    $table_name = $wpdb->prefix.'banner';

    $query = "SELECT * FROM $table_name WHERE is_deleted = 0 AND status = 1";

    $items = $wpdb->get_results($query);

    return $items;
}
function get_banner_by_id($id){
    global $wpdb;
    $table_name = $wpdb->prefix.'banner';

    $query = "SELECT * FROM $table_name WHERE id = $id";

    $item = $wpdb->get_row($query);

    return $item;
 }
 /**
  * Fetch all audition event from database
  *
  * @return array
  */
  function get_count_banner(){
      global $wpdb;
      $table_name = $wpdb->prefix.'banner';

      $query = 'SELECT COUNT(*) FROM ' .$table_name .' WHERE is_deleted = 0';

      if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) {
        $query = $query . ' AND name LIKE "%' . $_REQUEST['s'] . '%"';
    }

    return (int)$wpdb->get_var($query);

}

function upload_image_blob()
{
    $settings = array(
        "container" => "major-minor",
        "protocol" => "https",
        "account_name" => "mijorminorstorage",
        "account_key" => "KEUWRZs62sQxfwyBNVrpHCCfW87Jhx953tXeyhZGa8sLtBu2XmijsyCOitQa/G7ksDXx+UCxmoowds1heCHjWw=="
    );

    
    $connectionString = "DefaultEndpointsProtocol=" . $settings["protocol"] .
        ";AccountName=" . $settings["account_name"] .
        ";AccountKey=" . $settings["account_key"] . ";";
    $tes = new WindowsAzureStorageUtil();
    $tes2 = $tes->uniqueBlobName($settings['container'],$_FILES['file']['name']);
    $blobRestProxy = BlobRestProxy::createBlobService($connectionString);
    
    $file_name = $_FILES['image']['tmp_name'];
    $blob_name = basename($file_name);
    
     # Create the BlobService that represents the Blob service for the storage account
     $createContainerOptions = new CreateContainerOptions();
    
     $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
     
     // Set container metadata.
     $createContainerOptions->addMetaData("key1", "value1");
     $createContainerOptions->addMetaData("key2", "value2");
 
     $containerName = $settings["container"] . generateRandomString();
 
         // Create container.
         $blobClient->createContainer($containerName, $createContainerOptions);
         $myfile = $file_name;
         fclose($myfile);
     
         # Upload file as a block blob
         echo "Uploading BlockBlob: ".PHP_EOL;
         echo $fileToUpload;
         echo "<br />";
         
         $content = fopen($fileToUpload, "r");
     
         //Upload blob
         $blobClient->createBlockBlob($containerName, $fileToUpload, $content);

}


function uploadBlob($blobName,$realPath,$file_name) {
    
    $accesskey = "KEUWRZs62sQxfwyBNVrpHCCfW87Jhx953tXeyhZGa8sLtBu2XmijsyCOitQa/G7ksDXx+UCxmoowds1heCHjWw==";
    $storageAccount = 'mijorminorstorage';
    // $filetoUpload = realpath('./' . $blobName);
    $filetoUpload = $_FILES[$file_name]['tmp_name'];
    $containerName = 'major-minor';
    // $blobName = 'image.jpg';
    $media_type =  $_FILES[$file_name]['type'];
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
function create_banner($args = array())
    {
        global $wpdb;
        $table_name= $wpdb->prefix.'banner';
        $current_user = wp_get_current_user();
        $defaults = array(
            'id' => null,
            'created_by' => $current_user->user_login,
            'is_deleted' => 0,
            'is_active' => 1
        );
        $args = wp_parse_args($args, $defaults);
        $wpdb->insert($table_name , $args);
        if($wpdb->result){
            return $wpdb->insert_id;
        } else {
            return new WP_Error('sql-failed', __('Error : ' . $wpdb->last_error, 'bnr'));
        }        
        return false;
    }
function update_banner($args = array()){
        global $wpdb;
        $table_name= $wpdb->prefix.'banner';
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
            return new WP_Error('sql-failed', __('Warning : ' . $wpdb->last_error, 'bnr'));
        }  
    }

function delete_banner($id){
        global $wpdb;
        $table_name= $wpdb->prefix.'banner';
        $current_user = wp_get_current_user();
        $args = array(
            'id' => $id,
            'modified_by' => $current_user->user_login,
            'is_deleted' => 1,
            'is_active' => 0
        );
        $wpdb->update($table_name, $args, array('id' => $id));
        if (!$wpdb->result) {
            return new WP_Error('sql-failed', __('Warning : ' . $wpdb->last_error, 'bnr'));
        }
}
    

?>