<div id="container" style="height: 600px; margin: 0 auto"></div>
<script type="text/javascript">
	$(function () {
        $('#container').highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: 'Month Summary'
            },
            subtitle: {
                text: 'Activities reported, badges claimed, and comments added'
            },
            xAxis: {
                type: 'datetime',
                title: {
                    text: false
                }
            },
            yAxis: {
                title: {
                    text: false
                },
                min: 0
            },
            tooltip: {
                headerFormat: '<b>{series.name}</b><br>',
                pointFormat: '{point.x:%e. %b}: {point.y}'
            },
            plotOptions: {
            	series: {
            		lineWidth: 5,
                },
		        column: {
		        	pointPadding: 0,
			        groupPadding: 0,
		            borderWidth: 1
		        },
		    },
            series: [{
            	color: '#00a651',
            	type: 'column',
                name: 'Badges Claimed',
                data: <?= json_encode($badgesClaimed)?>
            }, {
            	color: '#21252c',
            	type: 'column',
                name: 'Comments Added',
                data: <?= json_encode($commentsAdded)?>
            }, {
            	color: '#21a9e1',
                name: 'Activities Reported',
                data: <?= json_encode($activitiesReported)?>
            }]
        });
    });
    
</script>
