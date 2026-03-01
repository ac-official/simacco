;(function($, window, undefined) {
    preTally.Salary = {
        listSalary: function() {

            if (!dhxMiddleBlockTabs.cells("listSalary")) {
                dhxMiddleBlockTabs.addTab("listSalary", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Salary Details", 160);
                //dhxMiddleBlockTabs.setContentHref("listSalary", "http://127.0.0.1/ss/samples/01_simple_init.html");
                dhxMiddleBlockTabs.tabs("listSalary").attachURL("spreadsheet.php")
                dhxMiddleBlockTabs.tabs("listSalary").setActive();
            } else {
                dhxMiddleBlockTabs.tabs("listSalary").setActive();
            }
        }
    };
})(jQuery, this);