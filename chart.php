<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>pie3D</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <link rel="stylesheet" type="text/css" href="assets/codebase/dhtmlx.css"/>
	<script src="assets/codebase/dhtmlx.js"></script>
	
        <script src="testdata.js"></script>
	<script>
        var chart;
        var barChart2;
         window.onload = function(){
		
               init();
	}    
        function init()
        {
            chart =  new dhtmlXChart({
			view:"donut",
			container:"chart1",
			value:"#sales#",
            color:"#color#",
            label:"#month#",
		tooltip:"#sales#",
                gradient:1,
             shadow:false
		});
		chart.parse(month_dataset1,"json");    
            barChart2 =  new dhtmlXChart({
		view:"bar",
		container:"chart2",
	    value:"#sales#",
        label:"'#year#",
        color:"#66ccff",
        gradient:"rising",
        barWidth:25,
        padding:{
            top:50,
            bottom:50,
            right:50,
            left:50
        }
	});
	barChart2.parse(dataset1,"json");
        }
	function one()
        {  chart.clearAll();
            chart.parse(month_dataset1,"json");
            barChart2.parse(dataset1,"json");
        }
        function two()
        {chart.clearAll();
            chart.parse(month_dataset2,"json");
            barChart2.parse(dataset2,"json");
        }
        function three()
        {chart.clearAll();
            chart.parse(month_dataset3,"json");
            barChart2.parse(dataset3,"json");
        }
	function four()
        {chart.clearAll();
            chart.parse(month_dataset4,"json");
            barChart2.parse(dataset4,"json");
        }
       

	</script>
</head>
<body>
	 <div id="chart1" style="width:300px;height:250px;border:1px solid #A4BED4;float:left;"></div>
        <div id="chart2" style="width:900px;height:300px;border:1px solid #A4BED4;float:right;"></div>
        <input type="button" onclick="one();" value="1"></input>
            <input type="button" onclick="two();" value="2"></input>
            <input type="button" onclick="three();" value="3"></input>
            <input type="button" onclick="four();" value="4"></input>
</body>
</html>
