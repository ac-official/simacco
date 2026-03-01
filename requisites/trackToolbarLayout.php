<?php
echo '[
    { id: "track_automate_enquiry", type: "button", img: "enquiry_24.png", imgdis: "new_registration.png", text: "Enquiry / New Registration", enabled: "false"},
    { type: "separator" },
    { id: "new_track_user_registration", type: "hidden", img: "new_registration.png", imgdis: "new_registration.png", text: "New Registration"},
//    { type: "separator" },
    
    { type: "buttonSelect", id: "listings", text: "Track Listing", img: "list.png", options:[
        { id: "list_track_jobs", type: "obj", img: "list.png", imgdis: "list.png", text: "Jobs List"},
        { type: "separator" },
        { id: "list_track_document", type: "obj", img: "list2.png", imgdis: "list2.png", text: "Document Wise List" },
        { type: "separator" },
        { id: "list_track_enquiry", type: "obj", img: "enquiry.png", imgdis: "enquiry.png", text: "Enquiry List" }
    ]},
    { type: "separator" },';

    if($ACL_Obj->ACL_Att_Master == 1) {
        echo '  { type: "buttonSelect", id: "master_data", text: "Master Data", img: "settings.gif", options:[
                    { id: "documents", type: "obj", text: "Document", img: "settings_1.png"},
                    { id: "supporting_documents", type: "obj", text: "Supporting Document", img: "settings_1.png"},
                    { id: "mainprocess", type: "obj", text: "Main Process", img: "settings_1.png"},
                    { id: "subprocess", type: "obj", text: "Sub Process", img: "settings_1.png"},
                    { id: "balSettings", type: "obj", text: "Balance Sheet Settings", img: "settings_1.png"},
                    { type: "separator" },
                    { id: "trackAutomate", type: "obj", text: "Automate Track Process", img: "track_automate.png"},
                    { id: "ATPInstruction", type: "obj", text: "Automate Track Instructions", img: "instn_24.png"},
                ]},
                { type: "separator" },';
    }
    
    echo '
    { id: "trackNotification", type: "button", text: "Notifications", img: "trNotifi.png", imgdis: "trNotifi.png"},
    { type: "separator" },
    { id: "trackSummary", type: "button", text: "Track Summary", img: "summary.png", imgdis: "summary.png"},';
    if($ACL_Obj->ACL_Att_Master == 1) {
        echo '{ type: "separator" },
        { id: "track_dashboard", type: "button", img: "dashboards16.png", imgdis: "dashboards16.png", text: "Dashboard", enabled: "false"},';
    }
    echo '{ type: "spacer"},
    { type: "separator" },
   
    { id: "cancelNewTrackRegistration", type: "button", text: "Close", img: "cross.png"},
    { type: "separator" },
    { id: "saveNewTrackRegistration", type: "button", text: "Save", img: "save.gif"},
    { type: "separator" },
    { id: "nextNewTrackRegistration", type: "button", text: "Next", img: "arrow_r_16.gif"},
    { type: "separator" },
    { id: "previousNewTrackRegistration", type: "button", text: "Previous", img: "arrow_lr_16.gif"},
    { type: "separator" },
    
]';
    /*
     * 
     * 
     *  
    { id: "newPhoneRegistration", type: "button", text: "Phone Registration", img: "phone_24.png"},
    { type: "separator" },
     
    { type: "separator" },
    { id: "list_track_jobs", type: "button", img: "list.png", imgdis: "list.png", text: "Jobs List"},
    { type: "separator" },
    { id: "list_track_document", type: "button", img: "list2.png", imgdis: "list2.png", text: "Document Wise List" },
    { type: "separator" },
    { id: "list_track_enquiry", type: "button", img: "enquiry.png", imgdis: "enquiry.png", text: "Enquiry List" },
     */
?>
