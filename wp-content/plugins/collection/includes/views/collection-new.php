<div class ="wrap">
<h1><?php 
    _e('Create Collection &nbsp', 'cln');
?>
 <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=collection'); ?>"><?php _e('Back to List', 'cln')?></a>
 </h1>

<?php 

$id = $_GET['id'];
$isNew = $id == 0? true: false;
if(!$isNew){
    $item = get_collection_by_id($id);
    $collection_medias = get_collection_media_by_collection_id($id);
}
?>
<input type="hidden" name="label" id="label" value="" />
 <form action="" method="post" enctype="multipart/form-data">
    <table class="form-table">
        <tbody>
        <tr class="row-title">
                <th scope="row">
                    <label for="title"><?php _e('Title : ', 'cln'); ?></label>
                </th>
                <td colspan="2">
                    <textarea name="title" id="title" class="regular-text" placeholder="Title" rows="3" cols="30"></textarea>                    
                </td>
            </tr>
            <tr class="row-description">
                <th scope="row">
                    <label for="description"><?php _e('Description : ', 'cln'); ?></label>
                </th>
                <td colspan="2">
                    <textarea name="description" id="description" class="regular-text" placeholder="Description" rows="3" cols="30"></textarea>                    
                </td>
            </tr>
            <tr class="row-release-date">
                <th scope="row">
                    <label for="release-date"><?php _e('Release Date : ', 'cln'); ?></label>
                </th>
                <td colspan="2">
                   <input type="date" name="release_date" id="release_date" class="regular-text" style="width:225px;" placeholder="<?php echo esc_attr('DD MMM YYYY', 'cln'); ?>" value="" autocomplete="off" />                   
                </td>
            </tr>
            <tr class="row-media_type">
                <th scope="row">
                    <label for="media_type"><?php _e('Media Type Collection Banner :', 'cln');?></label>
                </th>
                <td style="width: 33%;">
                <select name="media_type"
                id="media_type" class="regular-text" style="height: 24pt" >
                <option value='' disabled selected hidden>- Select Type -</option>
                <option value='1'>Image</option>
                <option value='2'>Video</option> 
                </select>
                 </td>
                <td></td>
            </tr>
            <tr class="row-file_banner">
                    <th scope="row">
                        <label for="file_banner"><?php _e('File Collection Banner :', 'cln'); ?></label>
                    </th>
                    <td>
                    <?php
                            if($item->url_file != null || $item->url_file != ''){
                                echo "<img style='height:300px; width:300px; padding-left:21px;' src='$item->url_file' />";
                            }
                        ?>   
                        <input type="file" name="file_banner" id="file_banner" class="regular-text" style="width:225px;" placeholder="<?php echo esc_attr('Choose Image or Video..', 'scdl'); ?>" accept="image/png, image/jpg, image/jpeg,video/mp4" onchange="return checkImage();"/>
                        <br><span style="font-size:8pt"><strong>Note :</strong> only *.jpeg, *.jpg,  *.png and *.mp4 file type can be supported.</span>
                    </td>
                </tr>
            <tr>
            <tr class="row-file_collection">
                    <th scope="row">
                        <label for="file_collection"><?php _e('File Collection (W: 6016px  H: 4016 px) :', 'cln'); ?></label>
                    </th>
                    <td>
                    <?php
                        if($collection_medias != null){
                            
                            foreach($collection_medias as $collection_media){
                                echo "<img style='height:300px; width:300px; padding-left:21px;' src='$collection_media->url_file' />";
                            }
                        }
                    ?>   
                        <input type="file" name="file_collection[]" id="file_collection" class="regular-text" style="width:225px;" placeholder="<?php echo esc_attr('Choose Image or Video..', 'scdl'); ?>" accept="image/png, image/jpg, image/jpeg" onchange="return checkImage();" multiple/>
                        <!-- <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="Select Files" class="button" style="position: relative; z-index: 1;"></p> -->
                        <!-- <input id="plupload-browse-button" type="button" value="Select Files" class="button" style="position: relative; z-index: 1;"> -->
                        <br><span style="font-size:8pt"><strong>Note :</strong> only *.jpeg, *.jpg and *.png file type can be supported.</span>
                    </td>
                </tr>
            <tr>
            <tr class="row-status">
                <th scope="row">
                    <label for="status"><?php _e('Active : ', 'cln'); ?></label>
                </th>
                <td colspan="2">
                <?php
                    $is_checked_vote =($item->status) ?'checked="checked"':'';
                    echo "<input type='checkbox' name='status' id='status' class='regular-text' value='1' {$is_checked_vote} />"; 
                 
                ?>
                </td>
            </tr> 
            <td></td>
                <td style="float: center; padding-right: 30px;">
                <?php wp_nonce_field('collection-new'); ?>
                <?php submit_button(__('Save', 'cln'), 'primary', 'submit_collection'); ?>
                </td>
            </tr> 
        </tbody>
    </table>
    <input type="hidden" name="field_id" value="0">
    </form>
 </div>

 <script>

 function check(t){
     let value= parseInt(t.value, 10);
     let event = <?php echo json_encode($event); ?>;

     let a = event.find(q => q.id == value);
    if(a != undefined){
        document.getElementById('event').innerHTML = a.event;
    }
    else{
        document.getElementById('event').innerHTML = '';
    }


 }
    // CKEDITOR.replace('description' , {
    //     toolbarGroups: [
    //         { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
    //         { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
    //         { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
    //         { name: 'forms', groups: [ 'forms' ] },
    //         '/',
    //         { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    //         { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
    //         { name: 'links', groups: [ 'links' ] },
    //         { name: 'insert', groups: [ 'insert' ] },
    //         '/',
    //         { name: 'styles', groups: [ 'styles' ] },
    //         { name: 'colors', groups: [ 'colors' ] },
    //         { name: 'tools', groups: [ 'tools' ] },
    //         { name: 'others', groups: [ 'others' ] },
    //         { name: 'description', groups: [ 'description' ] },
    //         {name: 'Image' , groups:['Image']}
    //     ],
    //     removeButtons: 'Smiley,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Language,Flash'
    // });


    function checkImage() {
        var fileElement = document.getElementById("file");
        var fileExtension = "";
        if (fileElement.value.lastIndexOf(".") > 0) {
            fileExtension = fileElement.value.substring(fileElement.value.lastIndexOf(".") + 1, fileElement.value.length);

            if (fileExtension.toLowerCase() == "jpg") {
                return true;
            } else if (fileExtension.toLowerCase() == "jpeg") {
                return true;
            } else if (fileExtension.toLowerCase() == "png") {
                return true;
            } else if (fileExtension.toLowerCase() == "mp4") {
                return true;
            } else {
                alert("Error : File formet not Supported.");
                return fileElement.value = "";
            }
        }
    }
    
 </script>
