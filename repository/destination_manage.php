<?php
require_once('includes/sessiondetails.php');
include_once("myClass/StateClass.php");
include_once("myClass/DestinationClass.php");

$StateObj = new StateClass();
$StateObj->viewState(' WHERE cn.CN_Id=st.CN_Id ORDER BY st.ST_State');
$ST_Obj = $StateObj->StateArray;

$msg = '';

if(isset($_POST['DS_Id'])) {
	
	$DestnObj = new DestinationClass();
	
	$DestnObj->DS_Title     = $_POST['DS_Title'];
	$DestnObj->DS_Address	= $_POST['DS_Address'];
	$DestnObj->DS_Details	= $_POST['DS_Details'];
	$DestnObj->DS_Top		= $_POST['DS_Top'] ? 1 : 0;
	$DestnObj->ST_Id        = $_POST['ST_Id'];
	$DestnObj->DS_Images    = $_POST['DS_Images'];
	$DestnObj->TP_Id	    = $_POST['TP_Id'];
	$DestnObj->DC_Checkout	= $_POST['DC_Checkout'];
			
	if($_POST['DS_Id'] == 0)
		$msg = $DestnObj->newDestination();
	else
		$msg = $DestnObj->updateDestination($_POST['DS_Id']);
	
}
if(isset($_GET['DS_Id'])) {
	
	$DestnObj = new DestinationClass();
	$DestnObj->viewDestination(' WHERE DS_Id='.$_GET['DS_Id']);
	$DestnDetailsObj = $DestnObj->DestinationArray;
	
	$DestnObj->viewDestinationCheckout(' WHERE DS_Id='.$_GET['DS_Id'].' AND DC_Status = 1 ');
	$DestnCheckoutObj = $DestnObj->DestinationArray;
	
	$DestnObj->viewDestinationTripIdea(' WHERE dt.DS_Id='.$_GET['DS_Id'].' AND dt.DT_Status = 1 AND dt.TP_Id=tp.TP_Id ');
	$DestnTripIdeaObj = $DestnObj->DestinationArray;
	
	$DestnObj->viewDestinationImages(' WHERE DS_Id='.$_GET['DS_Id'].' AND DI_Status = 1 ');
	$DestnImageObj = $DestnObj->DestinationArray;
	
}
require_once('includes/header.php');
?>
<script language="javascript">
var dest_images		= new Array(); 
</script>

<!------------ Editor -------------------->
<script src="javascripts/nicEdit.js" type="text/javascript"></script>
<script type="text/javascript" src="javascripts/ajaxupload.js"></script>
<script type="text/javascript" src="javascripts/jquery.tokeninput.js"></script>
<link rel="stylesheet" href="css/token-input.css" type="text/css" />
<link rel="stylesheet" href="css/token-input-facebook.css" type="text/css" />
<style type="text/css">
.replace {
	display: none;
}
fieldset {
	border: 1px solid #999 !important;
	padding: 10px;
	width: 480px;
}
ul.token-input-list {
	width: 500px;
}
ul.token-input-list {
	width: 500px;
}
.normal_table input[type="text"] {
	width: 496px;
}
.normal_table textarea {
	width: 500px;
	height: 100px;
}
.normal_table select {
	width: 500px;
}
</style>
<?php if($msg) { ?>
<div class="alert_warning" id="msg_area" style="margin:10px 30px 10px 30px;">
  <p> <img src="images_template/icon_accept.png" alt="success" class="mid_align"/> <?php echo $msg; ?>. </p>
</div>
<?php } ?>
<div style="display:none;">
  <form action="Upload-Image.php?filename=name&amp;maxSize=9999999999&amp;maxW=1000&amp;relPath=uploads/destination/&amp;colorR=255&amp;colorG=255&amp;colorB=255&amp;maxH=400" method="post" name="form2" id="form2">
    <input type="file" name="name" id="uptext" onChange="ajaxUpload(this.form,'Upload-Image.php?filename=name&amp;maxSize=9999999999&amp;maxW=1000&amp;relPath=uploads/destination/&amp;colorR=255&amp;colorG=255&amp;colorB=255&amp;maxH=400','upload_area','File Uploading Please Wait...&lt;br /&gt;&lt;img src=\'images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;','&lt;img src=\'images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; Error in Upload, check settings and path info in source code.'); return false;" />
  </form>
</div>
<div style="clear:both; height:20px;" ></div>
<form name="destination_form" id="destination_form"  method="post" action="">
  <table width="100%" border="1" cellpadding="5" cellspacing="8" class="normal_table">
    <tr>
      <td>Title</td>
      <td><input type="text" name="DS_Title" id="DS_Title" value="<?php echo $DestnDetailsObj[0]->DS_Title; ?>" class="validate_text" err_msg="Destination Title Required" /></td>
    </tr>
    <tr>
      <td>Trip Idea</td>
      <td><input type="text" name="TP_Id" id="TP_Id" value="" class="validate_text" err_msg="Trip Idea Required"  /></td>
    </tr>
    <tr>
      <td>Address</td>
      <td><textarea name="DS_Address" id="DS_Address" ><?php echo $DestnDetailsObj[0]->DS_Address; ?></textarea></td>
    </tr>
    <tr>
      <td>Details</td>
      <td><textarea name="DS_Details" id="DS_Details" ><?php echo $DestnDetailsObj[0]->DS_Details; ?></textarea></td>
    </tr>
    <tr>
      <td> List in Top 5</td>
      <td><input type="checkbox" name="DS_Top" id="DS_Top" value="1" <?php if($DestnDetailsObj[0]->DS_Top != 0) echo 'checked="checked"'; ?> /></td>
    </tr>
    <tr>
      <td>Checkout</td>
      <td><textarea name="DC_Checkout" id="DC_Checkout" value="" >
      <?php
	  foreach($DestnCheckoutObj as $rw)
	  {
		  echo $rw->DC_Checkout.'<br/>';
	  }
	  ?>
      </textarea></td>
    </tr>
    <tr>
      <td>State</td>
      <td><select name="ST_Id" id="ST_Id" class="validate_text" err_msg="Destination State Required">
          <option value="">Select State</option>
          <?php
	if($ST_Obj)
	{
		$i = 1;
		foreach($ST_Obj as $rw)
		{
			$selected = '';
			if($DestnDetailsObj[0]->ST_Id == $rw->ST_Id) $selected = ' selected="selected" ';
			echo '<option value="'.$rw->ST_Id.'" '.$selected.'>'.$rw->ST_State.'</option>';
		}
	}
	?>
        </select></td>
    </tr>
    <tr>
      <td>Gallery</td>
      <td><fieldset id="destination_gallery">
          <legend><a href="javascript:void(0);" onClick='callUpload();' class="text_black">New Image</a></legend>
			<?php
			if($DestnImageObj) {
				$random_number = 100000;
				foreach($DestnImageObj as $rw)
				{
					$random_number++;
					echo '<div style="width:100px; height:75px; float:left; margin:0 0 10px 16px; position:relative;" id="'.$random_number.'_div"><img src="uploads/destination/'.$rw->DI_Image.'"  style="width:100px; height:75px;"/><img src="images/delete.gif" style="position:absolute; bottom:3px; right:3px; cursor:pointer; " id="'.$random_number.'" imgsrc="'.$rw->DI_Image.'" /></div>';
					?>
					<script type="text/javascript" language="javascript">
					dest_images.push("<?php echo $rw->DI_Image; ?>");
					$('#<?php echo $random_number; ?>').click(function () { 
						var img_src = $(this).attr('imgsrc');
						var index = dest_images.indexOf(img_src);
						dest_images.splice(index, 1);
						$('#'+this.id+'_div').remove();
						removeImage(img_src) 
					});
					</script>
					<?php
				}
			}
            ?>
        </fieldset>
        <div id="upload_area" style="text-align:center;"></div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="button" value="<?php echo $_GET['DS_Id'] ? 'UPDATE' : 'ADD'; ?>" name="destination_save_update" id="destination_save_update" /></td>
    </tr>
  </table>
  <input type="hidden" name="DS_Id" id="DS_Id" value="<?php echo $_GET['DS_Id'] ? $_GET['DS_Id'] : 0; ?>"  />
  <input type="hidden" name="DS_Images" id="DS_Images" value=""  />
</form>
<div style="clear:both; height:20px;"></div>
<script type="text/javascript" language="javascript">

var random_number 	= 10000;

bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['fontSize','bold','italic','underline','strikeThrough','subscript','superscript','html','image']}).panelInstance('DS_Details');
	new nicEditor({buttonList : ['fontSize','bold','italic','underline','strikeThrough','subscript','superscript','html','image']}).panelInstance('DS_Address');
	new nicEditor({buttonList : ['fontSize','bold','italic','underline','strikeThrough','subscript','superscript','html','image']}).panelInstance('DC_Checkout');
});


function callUpload() {
	$('#uptext').trigger('click');
}

function updatescbphoto(nm)
{
	
	random_number += 1 ;
	$('#destination_gallery').append('<div style="width:100px; height:75px; float:left; margin:0 0 10px 16px; position:relative;" id="'+random_number+'_div"><img src="uploads/destination/'+nm+'"  style="width:100px; height:75px;"/><img src="images/delete.gif" style="position:absolute; bottom:3px; right:3px; cursor:pointer; " id="'+random_number+'" imgsrc="'+nm+'" /></div>');
	dest_images.push(nm);
	//alert(dest_images);
	
	$('#'+random_number).click(function () { 
		var img_src = $(this).attr('imgsrc');
		var index = dest_images.indexOf(img_src);
		dest_images.splice(index, 1);
		$('#'+this.id+'_div').remove();
		removeImage(img_src);
	});
	
	
}

$(document).ready(function() {
	
	$("#TP_Id").tokenInput("ajax_request.php?request=get_tripidea", {
		preventDuplicates: true <?php if(isset($_GET['DS_Id'])) { echo ', 
		prePopulate: [';
		foreach($DestnTripIdeaObj as $rw) {
			echo '{id: '.$rw->TP_Id.', name: "'.$rw->TP_Title.'"},';
		}
		echo ']'; }
		?> 
	});
	
	
	$("#destination_save_update").click(function (){
		
		var flg 	= '';
		var err_msg = '';  
		//alert(dest_images);
		nicEditors.findEditor('DS_Address').saveContent();
		nicEditors.findEditor('DS_Details').saveContent();
		nicEditors.findEditor('DC_Checkout').saveContent();
		
		$('#DS_Images').val(dest_images);
		
    	$('#destination_form .validate_text').each(function(index, obj){
			var return_msg = validate_text(obj);
			if(return_msg != 0)
				flg += return_msg+'\n';
		});

		if(flg) {
			alert(flg);
		} else {
			$('#destination_form').submit();
		}
	});
	
	$('#msg_area').delay(5000).fadeOut('slow', function() { });
});



</script>
<?php require_once('includes/footer.php'); ?>