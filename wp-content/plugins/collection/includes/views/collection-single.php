<div class ="wrap">
<h1><?php 
$action = $_GET['action'];
if($action == 'view'){    
    _e('View Collection &nbsp', 'cln');
}elseif($action == 'edit'){
    _e('Edit Collection &nbsp', 'cln');
}
$id = $_GET['id'];
$isNew = $id == 0? true: false;
if(!$isNew){
    $item = get_collection_by_id($id);
    $collection_medias = get_collection_media_by_collection_id($id);
}
?>

 <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=collection'); ?>"><?php _e('Back to List', 'cln')?></a>
 </h1>
<input type="hidden" name="label" id="label" value="" />
 <form action="" method="post" enctype="multipart/form-data">
    <table class="form-table">
        <tbody>
        <tr class="row-title">
                <th scope="row">
                    <label for="title"><?php _e('Title : ', 'cln'); ?></label>
                </th>
                <td colspan="2"><?php 
                    if($action == 'view'){
                        
                ?>
                    <textarea name="title" id="title" class="regular-text" placeholder="Title" rows="3" cols="30" readonly><?php echo ($isNew ? '' : esc_attr($item->title)); ?></textarea>
                    <?php }
                    else if($action == 'edit'){
                        ?>
                        <textarea name="title" id="title" class="regular-text" placeholder="Title" rows="3" cols="30"><?php echo ($isNew ? '' : esc_attr($item->title)); ?></textarea>
                    <?php } ?>
                </td>
            </tr>
            <tr class="row-description">
                <th scope="row">
                    <label for="description"><?php _e('Description : ', 'cln'); ?></label>
                </th>
                <td colspan="2"><?php 
                    if($action == 'view'){
                        
                ?>
                    <textarea name="description" id="description" class="regular-text" placeholder="Description" rows="3" cols="30" readonly><?php echo ($isNew ? '' : esc_attr($item->description)); ?></textarea>
                    <?php }
                    else if($action == 'edit'){
                        ?>
                        <textarea name="description" id="description" class="regular-text" placeholder="Description" rows="3" cols="30"><?php echo ($isNew ? '' : esc_attr($item->description)); ?></textarea>
                    <?php } ?>
                </td>
            </tr>
            <tr class="row-media_type">
                <th scope="row">
                    <label for="media_type"><?php _e('Media Type Collection Banner :', 'cln');?></label>
                </th>
                <td style="width: 33%;">
                <select name="media_type"
                id="media_type" class="regular-text" style="height: 24pt" >
                <?php 
            if($action == 'view'){
                if($item->media_type == '' || $item->media_type == null){
                    echo "<option value='' disabled selected hidden>- Select Media Type -</option>";
                }else if($item->media_type == 1){
                    echo "<option value='1' disabled selected hidden>Image</option>";
                }else{                    
                    echo "<option value='2' disabled selected hidden>Video</option>";
                }
            }elseif($action == 'edit'){
                if($item->media_type == '' || $item->media_type == null){
                    echo "<option value='' disabled selected hidden>- Select Type -</option>";
                    echo "<option value='1'>Image</option>";
                    echo "<option value='2'>Video</option>";
                }else if($item->media_type == 1){
                    echo "<option value='1' selected>Image</option>";
                    echo "<option value='2'>Video</option>";
                }else{                    
                    echo "<option value='1'>Image</option>";
                    echo "<option value='2' selected>Video</option>";
                }
            }
                ?>               
                
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
                            echo "<div style='padding-top:8px'><i>Choose 1 image as profile picture<i></div>";
                            foreach($collection_medias as $index => $collection_media){
                                $id = 5 . $index;
                                echo "<br>";
                                if($action == 'edit'){
                                submit_button('X(' . $collection_media->id . ')', 'primary', 'delete_image'); 
                                }                                
                                echo "<img style='height:300px; width:300px; margin-top:-20px' name='preview' id='$index' src='$collection_media->url_file' />";

                                if($action == 'view'){
                                    $is_checked_profile =($collection_media->is_front_image) ?'checked="checked"':'';
                                    echo "<input type='checkbox' style='margin-bottom: 5px' name='is_front_image' id='is_front_image' class='regular-text' value='$collection_media->id' {$is_checked_profile} disabled/>"; 
                                 }elseif($action == 'edit'){
                                    $is_checked_profile =($collection_media->is_front_image) ?'checked="checked"':'';
                                    echo "<input type='checkbox' style='margin-bottom: 5px' name='is_front_image' id='is_front_image' class='regular-text' value='$collection_media->id' {$is_checked_profile} />"; 
                                 }
                                // echo "<button type='button' id='$id' onClick='deleteImage($index,$id)'>X</button>";
                                // echo "<input type='submit' id='$id' value='X'>";
                            }
                        }
                    ?>   
                        <input type="file" name="file_collection[]" id="file_collection" class="regular-text" style="width:225px;" placeholder="<?php echo esc_attr('Choose Image or Video..', 'scdl'); ?>" accept="image/png, image/jpg, image/jpeg" multiple/>
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
                 if($action == 'view'){
                    $is_checked_vote =($item->status) ?'checked="checked"':'';
                    echo "<input type='checkbox' name='status' id='status' class='regular-text' value='1' {$is_checked_vote} disabled/>"; 
                 }elseif($action == 'edit'){
                    $is_checked_vote =($item->status) ?'checked="checked"':'';
                    echo "<input type='checkbox' name='status' id='status' class='regular-text' value='1' {$is_checked_vote} />"; 
                 }
                ?>
                </td>
            </tr> 
            <td></td>
                 <?php if($action == 'edit'){ ?>
                <td style="float: center; padding-right: 30px;">
                <?php wp_nonce_field('collection-new'); ?>
                <?php submit_button(__('Save', 'cln'), 'primary', 'submit_collection'); ?>
                </td>
                 <?php }?>
            </tr> 
        </tbody>
    </table>
    <input type="hidden" name="field_id" value="0">
    </form>
 </div>

 <script>

//  function check(t){
//      let value= parseInt(t.value, 10);
//      

//      let a = event.find(q => q.id == value);
//     if(a != undefined){
//         document.getElementById('event').innerHTML = a.event;
//     }
//     else{
//         document.getElementById('event').innerHTML = '';
//     }


//  }
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

    function deleteImage(id,button_id){
        debugger
        document.getElementById(id).remove();
        return document.getElementById(button_id).remove();
    }

    $(".regular-text").change(function() {
     $(".regular-text").prop('checked', false);
     $(this).prop('checked', true);
    });

    function previewImages() {
    debugger
    var image_files = document.getElementById('file_collection');
    // var test = [new File(), new File(), new File()];
    var preview = document.getElementsByName('preview');
    for(var i=0;i<image_files.files.length;i++) {
        var newEl = document.createElement('input');
        newEl.name = "something[]";
        newEl.id = '...';
        newEl.type = "file";
        // newEl.value = image_files.files[i]; // <-- assignment of file object
        document.appendChild(newEl); //add for upload
        //create some UI to remove this input
        //...
    }
    //remove the multi-input
    image_files.parent.removeChild(image_files);
    // preview.appendChild();
    // preview.forEach(myFunction);

    // function myFunction(item) {
    //     debugger
    //     var test = item.files;
    //     var file = {
    //         name : test,
    //         lastModified : 1581528040380,
    //         lastModifiedDate : Date.now(),
    //         webkitRelativePath : "",
    //         size : 23292,
    //         type : "image/jpeg"
    //     };
    //     var push = image_files.files;
    //     // image_files.files.push(file);
    // }
    // debugger

    // if (image_files.files) {
    //  [].forEach.call(image_files.files, readAndPreview);
// }

    function readAndPreview(file) {
        debugger
  // Make sure `file.name` matches our extensions criteria
  if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
    return alert(file.name + " is not an image");
  } // else...
  
  var reader = new FileReader();
  
  reader.addEventListener("load", function() {
    var image = new Image();
    image.height = 100;
    image.title  = file.name;
    image.src    = this.result;
    image_files.appendChild(image);
  });
  
  reader.readAsDataURL(file);
  
}

}

document.querySelector('#file_collection').addEventListener("change", previewImages);

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
