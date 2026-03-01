<?php
if(($ACL_Obj->ACL_SidebarMH == 1) && ($REQUEST['r'] == 'rbAcdn')) {
?>
    dhxAccord.addItem("a2", "<img src='images/icon/filter_icon.png' />&nbsp;&nbsp;&nbsp;Main Account Heads"); 
    dhxAccHeadTree = dhxAccord.cells("a2").attachTree();
    dhxAccHeadTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/");
    dhxAccHeadTree.enableDragAndDrop(true);
    //dhxAccHeadTree.loadXML(preTally.Initialize.encryptURL("requisites/accHead.php"), function(){
    //dhxAccHeadTree.setDragHandler(preTally.Settings.dragACL);	
    //});
<?php
}
if(($ACL_Obj->ACL_SidebarOFF == 1) && ($REQUEST['r'] == 'rbAcdn')) {
?>
    dhxAccord.addItem("a3", "<img src='images/icon/channel_icon.png' />&nbsp;&nbsp;&nbsp;Offices");
    dhxOffzTree = dhxAccord.cells("a3").attachTree();
    dhxOffzTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/");
    dhxOffzTree.enableDragAndDrop(true);
    /*dhxOffzTree.loadXML(preTally.Initialize.encryptURL("requisites/offzTree.php&f=HR&r=OF_H"), function(){
        dhxOffzTree.setDragHandler(preTally.Settings.dragACL);	
    });
    var OFType_Options = [
        ['OF_H', 'obj', 'Hierarchy Map', 'hierarchy.gif'],
    ];*/
<?php
}
?>