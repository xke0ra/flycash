/*

 Custom Chart javascript functions
 
 #0. COLOR VARIABLES
 #1. CHARTJS CHARTS
 #1.1 DOUGHNUT CHART
 #1.2 LINE CHART
 #2 SPARKLINE -- CHARTS

 */

'use strict';
$(function () {
    /*----------------------------------------
     // - #0. COLOR VARIABLES
     ----------------------------------------*/

    var primaryColor = '#1880c9';
    var primaryAlphaDot5 = 'rgba(24,128,201,0.5)';
    var primaryAlpha = 'rgba(24,128,201,0)';

    var whiteColor = '#fff';
    var whiteAlphaDot5 = 'rgba(255,255,255,0.5)';
    var whiteAlphaDot25 = 'rgba(255,255,255,0.25)';
    var whiteAlpha = 'rgba(255,255,255,0)';

    var lightColor ='#f1f1f1';

    var darkColor = '#2a3f5a';

    var secondaryColor = '#a5b5c5';
    var secondaryAlphaDot5 = 'rgba(165,181,197,0.5)';
	
    var warningColor = '#f8bc34';
    var warningAlphaDot5 = 'rgba(254,201,90, 0.5)';

    var successColor = '#66bb6a';
    var successAlphaDot5 = 'rgba(102,187,106,0.5)';

    var dangerColor = '#f65f6e';
    var dangerAlphaDot5 = 'rgba(246,95,110,0.5)';

    /*----------------------------------------
     // #1. CHARTJS CHARTS http://www.chartjs.org/
     ----------------------------------------*/

    if (typeof Chart !== 'undefined') {

        var fontFamily = 'Roboto';
        // set defaults
        Chart.defaults.global.defaultFontFamily = fontFamily;
        Chart.defaults.global.tooltips.titleFontSize = 14;
        Chart.defaults.global.tooltips.titleMarginBottom = 4;
        Chart.defaults.global.tooltips.displayColors = false;
        Chart.defaults.global.tooltips.bodyFontSize = 12;
        Chart.defaults.global.tooltips.xPadding = 10;
        Chart.defaults.global.tooltips.yPadding = 8;

        // init lite line chart if element exists

        // - #1.1 DOUGHNUT CHART


        // Doughnut Chart
        if($('#ordersChart').length){
            var doghnutChart = document.getElementById("ordersChart").getContext('2d');

            var doghnutData = {
                labels: ["Processing ", "Pending ", "Rejected ","Completed "],
                datasets: [{
                    data: [window.processing, window.pending,window.rejected,window.completed],
                    backgroundColor: [warningColor, secondaryColor,dangerColor,successColor],
                    hoverBackgroundColor: [warningColor, secondaryColor,dangerColor,successColor],
                    borderWidth: 0,
                    hoverBorderWidth: 6,
                    hoverBorderColor: [warningAlphaDot5, secondaryAlphaDot5,dangerAlphaDot5,successAlphaDot5]
                }]
            };

            var doughnutChartData = new Chart(doghnutChart, {
                type: 'doughnut',
                data: doghnutData,
                options: {
                    legend: {
                        display: false
                    },
                    animation: {
                        animateScale: true
                    },
                    cutoutPercentage: 80
                }
            });
        }
		
		// - #1.2 LINE CHARTS

        // Filled Line Chart //

        if ($("#sessionAanalyticsChart").length) {
			
			var jsonData = $.parseJSON( window.sessionsjsondata ).data;
			var sessionsData = [];
			var labelsData = [];
			
			for ( var i = 0; i < jsonData.length; i++ ) {
				var jsonExtracteddata = jsonData[i];
				sessionsData.push(jsonExtracteddata.sessions);
				labelsData.push(jsonExtracteddata.date);
				
			}
			
			sessionsData.reverse();
			labelsData.reverse();
			
			//var sessionsData = ["16", "120", "400", "300", 200, 750, 326, 45, 326, 45];
			//var labelsData = ["19 Sep 2018", "20 Sep 2018", "21 Sep 2018", "22 Sep 2018", "23 Sep 2018", "24 Sep 2018", "25 Sep 2018", "26 Sep 2018", "27 Sep 2018", "28 Sep 2018"];
			var largest = sessionsData[0];

			for (var i = 0; i < sessionsData.length; i++) {
				if (largest < sessionsData[i] ) {
					largest = sessionsData[i];
				}
			}
			
			// var sessionsMax = 1000;
			// var stepSizeMax = 200;
			
			if(largest < 10){
				
				var sessionsMax = 10;
				var stepSizeMax = 2;
				
			}else if(largest < 50){
				
				var sessionsMax = 50;
				var stepSizeMax = 10;
				
			}else if(largest < 100){
				
				var sessionsMax = 100;
				var stepSizeMax = 20;
				
			}else if(largest < 250){
				
				var sessionsMax = 250;
				var stepSizeMax = 50;
				
			}else if(largest < 500){
				
				var sessionsMax = 500;
				var stepSizeMax = 100;
				
			}else if(largest < 750){
				
				var sessionsMax = 750;
				var stepSizeMax = 150;
				
			}else if(largest < 1000){
				
				var sessionsMax = 1000;
				var stepSizeMax = 200;
				
			}else if(largest < 2500){
				
				var sessionsMax = 2500;
				var stepSizeMax = 500;
				
			}else if(largest < 5000){
				
				var sessionsMax = 5000;
				var stepSizeMax = 1000;
				
			}else if(largest < 7500){
				
				var sessionsMax = 7500;
				var stepSizeMax = 1500;
				
			}else if(largest < 10000){
				
				var sessionsMax = 10000;
				var stepSizeMax = 2000;
				
			}else if(largest < 25000){
				
				var sessionsMax = 25000;
				var stepSizeMax = 5000;
				
			}else if(largest < 50000){
				
				var sessionsMax = 50000;
				var stepSizeMax = 10000;
				
			}else if(largest < 75000){
				
				var sessionsMax = 75000;
				var stepSizeMax = 15000;
				
			}else if(largest < 100000){
				
				var sessionsMax = 100000;
				var stepSizeMax = 10000;
				
			}else if(largest < 1000000){
				
				var sessionsMax = 1000000;
				var stepSizeMax = 100000;
				
			}else if(largest >= 1000000){
				
				var sessionsMax = 1000000;
				var stepSizeMax = 100000;
				
			}else{
				
				var sessionsMax = 10;
				var stepSizeMax = 2;
				
				var sessionsData = [0,0,0,0];
				var labelsData = ["No Data Found", "No Data Found", "No Data Found", "No Data Found"];
			
				
			}

            var sessionAanalyticsChart = document.getElementById("sessionAanalyticsChart").getContext('2d');

            var primaryGradient = sessionAanalyticsChart.createLinearGradient(0, 0, 0, 200);
            primaryGradient.addColorStop(0, primaryAlphaDot5);
            primaryGradient.addColorStop(1, primaryAlpha);


            // line chart data
            var filledLineData = {
                labels: labelsData,
                datasets: [{
                    label: "Sessions ",
                    fill: true,
                    backgroundColor: primaryGradient,
                    borderColor: primaryColor,
                    borderCapStyle: 'butt',
                    borderWidth: 2,
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "transparent",
                    pointBackgroundColor: primaryColor,
                    pointBorderWidth: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: primaryColor,
                    pointHoverBorderColor: primaryColor,
                    pointHoverBorderWidth: 0,
                    pointRadius: 3,
                    pointHitRadius: 10,
                    data: sessionsData
                }]
            };

            // line chart init
            var sessionAanalyticsChart = new Chart(sessionAanalyticsChart, {
                type: 'line',
                data: filledLineData,
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                fontSize: '11',
                                fontColor: secondaryColor
                            },
                            gridLines: {
                                color: lightColor,
                                zeroLineColor: lightColor
                            }
                        }],
                        yAxes: [{
                            /*display: true,*/
                            ticks: {
                                beginAtZero: true,
                                max: sessionsMax,
                                stepSize: stepSizeMax,
                                fontSize: '11',
                                fontColor: secondaryColor
                            },
                            gridLines: {
                                color: 'transparent',
                                zeroLineColor: 'transparent'
                            }
                        }]
                    }
                }
            });
        }

    }
	
	

    /*----------------------------------------
     // - #2. SPARKLINE -- CHARTS
     ----------------------------------------*/

    if($('.allTimeProfitChart').length){
		
        var myValue = [48, 75, 90, 68, 78, 68, 28];
		var barSpacing = 8;
		var barWidth = 3;

        $('.allTimeProfitChart').sparkline(myValue,{
            type:'bar',
            barColor: whiteColor,
            height: "60",
            barWidth: barWidth,
            resize: false,
			disableTooltips: true,
            barSpacing: barSpacing
        });
    }
});
