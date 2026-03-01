<?php
	session_start();
	require_once('_define.php');
	$preTally_user_id 	= $_SESSION['preTally_user_id']; 
	$preTally_user_name 	= $_SESSION['preTally_user_name']; 
        $preTally_user_empid    = $_SESSION['preTally_user_empid'];
  	$preTally_user_uname    = $_SESSION['preTally_user_uname'];
	$preTally_user_type 	= $_SESSION['preTally_user_type']; 
        $preTally_user_ofid 	= $_SESSION['preTally_user_ofid']; 
	$preTally_user_lcid 	= $_SESSION['preTally_user_lcid'];
        $preTally_user_ofname 	= $_SESSION['preTally_user_ofname']; 
	$preTally_user_lcname 	= $_SESSION['preTally_user_lcname'];
        $preTally_user_dpid 	= $_SESSION['preTally_user_dpid'];
        $ACL_Obj                = $_SESSION['preTally_user_acl'];
	$att_flag               = $_SESSION['attendance_flag'];
    $UserACLObj                = $_SESSION['preTally_user_sacl']; //04-07-2025

        
	//$preTally_user_id 			= 1; 
	//$preTally_user_name 		= "Administrator"; 
	//$preTally_user_type 		= 1; 
	//echo $preTally_user_id.'--'.$preTally_user_name.'--'.$preTally_user_type.'--';
	
	//define("BASE_PATH","http://127.0.0.1/pretally");
	//define("BASE_PATH","http://www.augmob.com/pretally/");
	if((!$preTally_user_id) || (!$preTally_user_name) || (!$preTally_user_type)) {
		header('location:'.$BASEPATH.'/logout.php');
	}
	//die($role.' -- '.$preTally_user_type);
	/*if($role== 'adm') {
		if($preTally_user_type != 1) {
			header('location:'.constant("BASE_PATH").'/logout.php');
		}
	} else if($role== 'mng') {
		if(($preTally_user_type != 1) && ($preTally_user_type != 2)) {
			header('location:'.constant("BASE_PATH").'/logout.php');
		}
	} else if($role== 'gst') {
		if(($preTally_user_type != 1) && ($preTally_user_type != 2) && ($preTally_user_type != 3)&& ($preTally_user_type != 4)) {
			header('location:'.constant("BASE_PATH").'/logout.php');
		}
	} else { header('location:'.constant("BASE_PATH").'/logout.php'); }
         
         */
	if($ajax== 'true') {
            $preTallyURL = parse_url($_SERVER['HTTP_REFERER']);
            if( !isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) || ( !$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ){
                //die('Access Not Permitted');
                header('location:'.$BASEPATH.'/logout.php');
            }
            if($preTallyURL['host'] !== 'pretally.in') {
              //die('Access Not Permitted');
              //header('location:'.constant("BASE_PATH").'/logout.php');
            }
	}
        //$_SERVER["HTTP_REFERER"]
?>
