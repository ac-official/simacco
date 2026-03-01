<?php
require_once('includes/sessiondetails.php');
require_once('includes/header.php');

require_once("myClass/BannerClass.php");

$BannerObj = new BannerClass();

$msg = '';

if(isset($_POST['BI_Id'])) {
	
	
	$BannerObj->BI_Text1    = $_POST['BI_Text1'];
	$BannerObj->BI_Text2	= $_POST['BI_Text2'];
	$BannerObj->BI_Link		= $_POST['BI_Link'];
	$BannerObj->BI_Target   = $_POST['BI_Target'];
	$BannerObj->BI_Image    = $_POST['BI_Image'];
		
	$msg = $BannerObj->updateBanner($_POST['BI_Id']);
	
}

$frm = $_GET['frm'];
if($frm == 1) {
	$filter = ' WHERE BI_Id=1 OR BI_Id=2 OR BI_Id=3 ORDER BY BI_Id';
	$title = 'Home';
} else if($frm == 2) {
	$filter = ' WHERE BI_Id=4 OR BI_Id=5 OR BI_Id=6 ORDER BY BI_Id';
	$title = 'Hotel';
} else if($frm == 3) {
	$filter = ' WHERE BI_Id=7 OR BI_Id=8 OR BI_Id=9 ORDER BY BI_Id';
	$title = 'Destination';
} else if($frm == 4) {
	$filter = ' WHERE BI_Id=10 OR BI_Id=11 OR BI_Id=12 ORDER BY BI_Id';
	$title = 'Activity';
} else {
	$filter = ' WHERE BI_Id=100';
}
	

$BannerObj->viewBanner($filter);
$BI_Obj = $BannerObj->BannerArray;
?>
<script type="text/javascript" src="javascripts/ajaxupload.js"></script>

<?php if($msg) { ?>
<div class="alert_warning" id="msg_area" style="margin:10px 30px 10px 30px;">
  <p> <img src="images_template/icon_accept.png" alt="success" class="mid_align"/> <?php echo $msg; ?>. </p>
</div>
<?php } ?>

<!-- Begin three column window -->
<div style="display:none;">
  <form action="Upload-Image.php?filename=name&amp;maxSize=9999999999&amp;maxW=1000&amp;relPath=uploads/banner/&amp;colorR=255&amp;colorG=255&amp;colorB=255&amp;maxH=400" method="post" name="form2" id="form2">
    <input type="file" name="name" id="uptext" onChange="ajaxUpload(this.form,'Upload-Image.php?filename=name&amp;maxSize=9999999999&amp;maxW=1000&amp;relPath=uploads/banner/&amp;colorR=255&amp;colorG=255&amp;colorB=255&amp;maxH=400','upload_area','File Uploading Please Wait...&lt;br /&gt;&lt;img src=\'images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;','&lt;img src=\'images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; Error in Upload, check settings and path info in source code.'); return false;" />
  </form>
</div>

<div id="threecolumn" class="threecolumn">
<?php 
$j = 1;
foreach($BI_Obj as $rw) { ?>
  <div class="threecolumn_each">
    <div class="header"> <span><?php echo $title; ?> Banner <?php echo $j; ?></span> </div>
    <br class="clear"/>
    <div class="content">
    <form name="banner_form<?php echo $rw->BI_Id; ?>" id="banner_form<?php echo $rw->BI_Id; ?>"  method="post" action="">
      <table width="100%" border="0">
        <tr>
          <td colspan="2" style="text-align:center;"><img id="banner<?php echo $rw->BI_Id; ?>" src="uploads/banner/<?php echo $rw->BI_Image; ?>" width="183px" height="111"  onClick='callUpload("banner<?php echo $rw->BI_Id; ?>");' style="cursor:pointer;"></td>
        </tr>
        <tr>
          <td>Text 1</td>
          <td><input type="text" name="BI_Text1" id="BI_Text1" value="<?php echo $rw->BI_Text1; ?>" style="width:200px;" ></td>
        </tr>
        <tr>
          <td>Text 2</td>
          <td><input type="text" name="BI_Text2" id="BI_Text2" value="<?php echo $rw->BI_Text2; ?>" style="width:200px;" ></td>
        </tr>
        <tr>
          <td>Link</td>
          <td><input type="text" name="BI_Link" id="BI_Link" value="<?php echo $rw->BI_Link; ?>" style="width:200px;" ></td>
        </tr>
        <tr>
          <td>Target</td>
          <td><select name="BI_Target" id="BI_Target" style="width:205px;">
              <option value="_self" <?php if($rw->BI_Target == '_self') echo 'selected="selected"'; ?> >_self</option>
              <option value="_blank"  <?php if($rw->BI_Target == '_blank') echo 'selected="selected"'; ?> >_blank</option>
            </select></td>
        </tr>
        <tr>
          <td></td>
          <td><input type="submit" value="Update" ></td>
        </tr>
         
        
      </table>
      <input type="hidden" name="BI_Id" value="<?php echo $rw->BI_Id; ?>">
      <input type="hidden" name="BI_Image" id="banner<?php echo $rw->BI_Id; ?>Image" value="<?php echo $rw->BI_Image; ?>">
      </form>
    </div>
  </div>
  <?php 
  $j++;
  } ?>
  <!-- <div class="threecolumn_each">
    <div class="header"> <span>Banner 2</span> </div>
    <br class="clear"/>
    <div class="content"> Your contents go here. </div>
  </div>
  <div class="threecolumn_each">
    <div class="header"> <span>Banner 3</span> </div>
    <br class="clear"/>
    <div class="content"> Your contents go here. </div>
  </div> -->
</div>
<div id="upload_area" style="text-align:center;"></div>
<!-- End three column window --> 

<br class="clear"/>
<br class="clear"/>
<script type="text/javascript" language="javascript">
var field;
function callUpload(banner) {
	$('#uptext').trigger('click');
	field=banner;
}

function updatescbphoto(nm){
	$('#'+field).attr('src','uploads/banner/'+nm);
	$('#'+field+'Image').val(nm);	
}
//$('#msg_area').delay(5000).fadeOut('slow', function() { });
</script>
<?php
require_once('includes/footer.php');
?>
