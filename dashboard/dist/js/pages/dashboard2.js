'use strict';
$(function () {

  /* ChartJS
   * -------
   * Here we will create a few charts using ChartJS
   */

  //-----------------------
  //- MONTHLY SALES CHART -
  //-----------------------

  // Get context with jQuery - using jQuery's .get() method.
  var salesChartCanvas = $("#salesChart").get(0).getContext("2d");
  // This will get the first returned node in the jQuery collection.
  var salesChart = new Chart(salesChartCanvas);
  var months_arr=[];
  var bussamtArrayValues=[];
  var payamtArrayValues=[];
  var cncamtArrayValues=[];
  var d = new Date();
  var n = d.getMonth()+1;   
  var months =["January", "February", "March", "April", "May", "June","July","August","September","October","November","December"];
  var st=n-6; 
  if(st<0)st=12+st; 
  var flg=0;
  for(var i=st;flg!=1;i++){      
      if(i>11)i=0;
      if(n==i+1)flg=1;
      months_arr.push(months[i]);
      if(bussamtArray[i].TOTAL_BUSS) bussamtArrayValues.push(bussamtArray[i].TOTAL_BUSS);
      else bussamtArrayValues.push(0);
      if(payamtArray[i].TOTAL_PAY) payamtArrayValues.push(payamtArray[i].TOTAL_PAY);    
      else payamtArrayValues.push(0);
       if(cncamtArray[i].TOTAL_CNC) cncamtArrayValues.push(cncamtArray[i].TOTAL_CNC);    
      else cncamtArrayValues.push(0);
  }  
  //alert(bussamtArray[6].MNTH+":"+bussamtArray[6].TOTAL_BUSS);
  var salesChartData = {
    labels:months_arr,
    datasets: [
        {
        label: "Cancellation",
        fillColor: "rgba(100,150,0,1)",
        strokeColor: "rgba(100,150,0,0.1)",
        lineColor: "#669966",
        pointColor: "#669966",
        pointStrokeColor: "#99CC33",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(100,150,0,1)",
        data: cncamtArrayValues
      },
      {
        label: "Jobs",
        fillColor: "rgba(255,102,0,0.5)",
        strokeColor: "rgba(255,102,0,0.1)",
        pointColor: "#FF6600",
        lineColor: "#FF6600",
        pointStrokeColor: "#c1c7d1",
        pointHighlightFill: "#FF6600",
        pointHighlightStroke: "rgba(255,102,0,0.5)",
        data: bussamtArrayValues
      },      
      {
        label: "Income",
        fillColor: "rgba(60,141,188,0.3)",
        strokeColor: "rgba(60,141,188,0.1)",
        pointColor: "#3b8bba",
        lineColor: "#3b8bba",
        pointStrokeColor: "rgba(60,141,188,0.3)",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(60,141,188,0.3)",
        data: payamtArrayValues
      }
    ]
  };

  var salesChartOptions = {
    //Boolean - If we should show the scale at all
    showScale: true,
    //Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines: true,
    //String - Colour of the grid lines
    scaleGridLineColor: "rgba(0,0,0,.05)",
    //Number - Width of the grid lines
    scaleGridLineWidth: 1,
    //Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,
    //Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines: true,
    //Boolean - Whether the line is curved between points
    bezierCurve: true,
    //Number - Tension of the bezier curve between points
    bezierCurveTension: 0.3,
    //Boolean - Whether to show a dot for each point
    pointDot: false,
    //Number - Radius of each point dot in pixels
    pointDotRadius: 4,
    //Number - Pixel width of point dot stroke
    pointDotStrokeWidth: 1,
    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius: 20,
    //Boolean - Whether to show a stroke for datasets
    datasetStroke: true,
    //Number - Pixel width of dataset stroke
    datasetStrokeWidth: 2,
    //Boolean - Whether to fill the dataset with a color
    datasetFill: true,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\" style=\"list-style-type: none;\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"width:100px;height:50px;background-color:<%=datasets[i].pointColor%>\">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;<%=datasets[i].label%></li><%}%></ul>",
    //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true
  };

  //Create the line chart
  var chart=salesChart.Line(salesChartData, salesChartOptions);
  var chartlegends=chart.generateLegend();  
  $("#salesLegend").append(chartlegends);
  //---------------------------
  //- END MONTHLY SALES CHART -
  //---------------------------

  //-------------
  //- PIE CHART -
  //-------------
  // Get context with jQuery - using jQuery's .get() method.

var pieChartCanvas_TDBJ = $("#pieChart-trackBD_ByJob").get(0).getContext("2d");
var pieChart_TDBJ = new Chart(pieChartCanvas_TDBJ);
  
var pieChartCanvas_TDBI = $("#pieChart-trackBD_ByIncome").get(0).getContext("2d");
    var pieChart_TDBI = new Chart(pieChartCanvas_TDBI);
  
var pieChartCanvas_TDBC = $("#pieChart-trackBD_ByCancel").get(0).getContext("2d");
var pieChart_TDBC = new Chart(pieChartCanvas_TDBC);
var pie_Colors=["#f56954","#00a65a","#f39c12","#00c0ef","#3c8dbc","#d2d6de"];
var TDBJ_length=Object.keys(pieDataJobs).length;
var TDBI_length=Object.keys(pieDataIncome).length;
var TDBC_length=Object.keys(pieDataCancel).length;
var PieData_TDBJ =[];
var PieData_TDBI =[];
var PieData_TDBC =[];
  for(var i=0;i<TDBJ_length;i++)
  {
      var attrib=
          {
      value: parseInt(pieDataJobs[i].TOTAL_BUSS),
      color: pie_Colors[i],
      highlight: pie_Colors[i],
      label: pieDataJobs[i].LC_Name
    }
      PieData_TDBJ.push(attrib);
  }
  for(var i=0;i<TDBI_length;i++)
  {
      var attrib=
          {
      value: parseInt(pieDataIncome[i].TOTAL_PAY),
      color: pie_Colors[i],
      highlight: pie_Colors[i],
      label: pieDataIncome[i].LC_Name
    }
      PieData_TDBI.push(attrib);
  }
  for(var i=0;i<TDBC_length;i++)
  {
      var attrib=
          {
      value: parseInt(pieDataCancel[i].TOTAL_CNC),
      color: pie_Colors[i],
      highlight: pie_Colors[i],
      label: pieDataCancel[i].LC_Name
    }
      PieData_TDBC.push(attrib);
  }
  
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%>"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.  
  
  pieChart_TDBJ.Doughnut(PieData_TDBJ, pieOptions);
  pieChart_TDBI.Doughnut(PieData_TDBI, pieOptions);
  pieChart_TDBC.Doughnut(PieData_TDBC, pieOptions);
  //-----------------
  //- END PIE CHART -
  //-----------------

  /* jVector Maps
   * ------------
   * Create a world map with markers
   */
  $('#world-map-markers').vectorMap({
    map: 'world_mill_en',
    normalizeFunction: 'polynomial',
    hoverOpacity: 0.7,
    hoverColor: false,
    backgroundColor: 'transparent',
    regionStyle: {
      initial: {
        fill: 'rgba(210, 214, 222, 1)',
        "fill-opacity": 1,
        stroke: 'none',
        "stroke-width": 0,
        "stroke-opacity": 1
      },
      hover: {
        "fill-opacity": 0.7,
        cursor: 'pointer'
      },
      selected: {
        fill: 'yellow'
      },
      selectedHover: {
      }
    },
    markerStyle: {
      initial: {
        fill: '#00a65a',
        stroke: '#111'
      }
    },
    markers: [
      {latLng: [41.90, 12.45], name: 'Vatican City'},
      {latLng: [43.73, 7.41], name: 'Monaco'},
      {latLng: [-0.52, 166.93], name: 'Nauru'},
      {latLng: [-8.51, 179.21], name: 'Tuvalu'},
      {latLng: [43.93, 12.46], name: 'San Marino'},
      {latLng: [47.14, 9.52], name: 'Liechtenstein'},
      {latLng: [7.11, 171.06], name: 'Marshall Islands'},
      {latLng: [17.3, -62.73], name: 'Saint Kitts and Nevis'},
      {latLng: [3.2, 73.22], name: 'Maldives'},
      {latLng: [35.88, 14.5], name: 'Malta'},
      {latLng: [12.05, -61.75], name: 'Grenada'},
      {latLng: [13.16, -61.23], name: 'Saint Vincent and the Grenadines'},
      {latLng: [13.16, -59.55], name: 'Barbados'},
      {latLng: [17.11, -61.85], name: 'Antigua and Barbuda'},
      {latLng: [-4.61, 55.45], name: 'Seychelles'},
      {latLng: [7.35, 134.46], name: 'Palau'},
      {latLng: [42.5, 1.51], name: 'Andorra'},
      {latLng: [14.01, -60.98], name: 'Saint Lucia'},
      {latLng: [6.91, 158.18], name: 'Federated States of Micronesia'},
      {latLng: [1.3, 103.8], name: 'Singapore'},
      {latLng: [1.46, 173.03], name: 'Kiribati'},
      {latLng: [-21.13, -175.2], name: 'Tonga'},
      {latLng: [15.3, -61.38], name: 'Dominica'},
      {latLng: [-20.2, 57.5], name: 'Mauritius'},
      {latLng: [26.02, 50.55], name: 'Bahrain'},
      {latLng: [0.33, 6.73], name: 'São Tomé and Príncipe'}
    ]
  });

  /* SPARKLINE CHARTS
   * ----------------
   * Create a inline charts with spark line
   */

  //-----------------
  //- SPARKLINE BAR -
  //-----------------
  $('.sparkbar').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'bar',
      height: $this.data('height') ? $this.data('height') : '30',
      barColor: $this.data('color')
    });
  });

  //-----------------
  //- SPARKLINE PIE -
  //-----------------
  $('.sparkpie').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'pie',
      height: $this.data('height') ? $this.data('height') : '90',
      sliceColors: $this.data('color')
    });
  });

  //------------------
  //- SPARKLINE LINE -
  //------------------
  $('.sparkline').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'line',
      height: $this.data('height') ? $this.data('height') : '90',
      width: '100%',
      lineColor: $this.data('linecolor'),
      fillColor: $this.data('fillcolor'),
      spotColor: $this.data('spotcolor')
    });
  });
});