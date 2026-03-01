<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");



if(isset($_REQUEST['path']) && $_REQUEST['path'] != '')
    $basePath = '../wallet/MW'.$preTally_user_id.'/'.$_REQUEST['path'].'/';
else
    $basePath = '../wallet/MW'.$preTally_user_id.'/';


function isNodeExists($basePath, $folder = "New folder", $count = 1){        
    if (file_exists($basePath.$folder) || is_dir($basePath.$folder)) {
        $count ++ ;
        $folder = "New folder (".$count.")";
        isNodeExists($basePath, $folder, $count);
    } else {
        if($count != 1) {
            $folder = "New folder (".$count.")";  
        }
        if (!file_exists($basePath.$folder) || !is_dir($basePath.$folder)) 
            mkdir($basePath.$folder, 0755);
    }    
}

if(isset($_REQUEST['nf']) && $_REQUEST['nf'] == 1){   
    $folder = isNodeExists($basePath);
}
if(isset($_REQUEST['df']) && $_REQUEST['df'] == 1){   
    if(!rmdir($basePath.$_REQUEST['f'])) {
        
    }
}
//mkdir('../wallet/MW1/New folder (3)', 0755);
echo '<data>';
if ($handle = opendir($basePath)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            if(is_dir($basePath.$entry)) $type = 'dir'; else $type = 'file';
            echo '<item name="'.$entry.'" type="'.$type.'">
                    <modifdate>'.date ("F d Y H:i:s.", filemtime($basePath.$entry)).'</modifdate>
                  </item>';
        }
    }
    closedir($handle);
}
echo '</data>';
?>