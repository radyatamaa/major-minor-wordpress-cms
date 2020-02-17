<div class ="wrap">
<h1><?php 
$action = $_GET['action'];
if($action == 'view'){    
    _e('View Banner &nbsp', 'bnr');
}elseif($action == 'edit'){
    _e('Edit Banner &nbsp', 'bnr');
}
?>
 <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=banner'); ?>"><?php _e('Back to List', 'bnr')?></a>
 </h1>

<?php 

$id = $_GET['id'];
$isNew = $id == 0? true: false;
if(!$isNew){
    $item = get_banner_by_id($id);
}
?>
<input type="hidden" name="label" id="label" value="" />
 <form action="" method="post" enctype="multipart/form-data">
    <table class="form-table">
        <tbody>
        <tr class="row-title">
                <th scope="row">
                    <label for="title"><?php _e('Title : ', 'bnr'); ?></label>
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
            <tr class="row-file">
                    <th scope="row">
                        <label for="file"><?php _e('File :', 'bnr'); ?></label>
                    </th>
                    <td>
                    <?php
                            if($item->url_file != null || $item->url_file != ''){
                                if((strstr( $item->url_file, '.jpeg' )) || (strstr( $item->url_file, '.jpg' )) || (strstr( $item->url_file, '.png' ))){                                   
                                echo "<img style='height:300px; width:300px; padding-left:21px;' src='$item->url_file' />";
                                }elseif(strstr( $item->url_file, '.mp4' )){
                                echo "<video width='400' controls><source src='$item->url_file' type='video/mp4'></video>";
                                }
                            }
                        ?>   
                        <input type="file" name="file" id="file" class="regular-text" style="width:225px;" placeholder="<?php echo esc_attr('Choose Image or Video..', 'scdl'); ?>" accept="image/png, image/jpg, image/jpeg,video/mp4" onchange="return checkImage();" />
                        <br><span style="font-size:8pt"><strong>Note :</strong> only *.jpeg, *.jpg,  *.png and *.mp4 file type can be supported.</span>
                    </td>
                </tr>
            <tr>
            <tr class="row-type">
                <th scope="row">
                    <label for="type"><?php _e('Type :', 'bnr');?></label>
                </th>
                <td style="width: 33%;">
                <select name="type"
                id="type" class="regular-text" style="height: 24pt" >
                <?php 
            if($action == 'view'){
                if($item->type == '' || $item->type == null){
                    echo "<option value='' disabled selected hidden>- Select Type -</option>";
                }else if($item->type == 1){
                    echo "<option value='1' disabled selected hidden>Banner</option>";
                }else{                    
                    echo "<option value='2' disabled selected hidden>Banner Slider</option>";
                }
            }elseif($action == 'edit'){
                if($item->type == '' || $item->type == null){
                    echo "<option value='' disabled selected hidden>- Select Type -</option>";
                    echo "<option value='1'>Banner</option>";
                    echo "<option value='2'>Banner Slider</option>";
                }else if($item->type == 1){
                    echo "<option value='1' selected>Banner</option>";
                    echo "<option value='2'>Banner Slider</option>";
                }else{                    
                    echo "<option value='1'>Banner</option>";
                    echo "<option value='2' selected>Banner Slider</option>";
                }
            }
                ?>               
                
                </select>
                 </td>
                <td></td>
            </tr>
            <tr class="row-status">
                <th scope="row">
                    <label for="status"><?php _e('Active : ', 'bnr'); ?></label>
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
                <?php wp_nonce_field('banner-new'); ?>
                <?php submit_button(__('Save', 'bnr'), 'primary', 'submit_banner'); ?>
                </td>
                 <?php }?>
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
    CKEDITOR.replace('description' , {
        toolbarGroups: [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
            { name: 'forms', groups: [ 'forms' ] },
            '/',
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
            { name: 'links', groups: [ 'links' ] },
            { name: 'insert', groups: [ 'insert' ] },
            '/',
            { name: 'styles', groups: [ 'styles' ] },
            { name: 'colors', groups: [ 'colors' ] },
            { name: 'tools', groups: [ 'tools' ] },
            { name: 'others', groups: [ 'others' ] },
            { name: 'description', groups: [ 'description' ] },
            {name: 'Image' , groups:['Image']}
        ],
        removeButtons: 'Smiley,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Language,Flash'
    });


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
