<style type="text/css">
	.fc-header-left, .fc-header-center, .fc-header-right {
		background-color: #fff;
	}
	.fc-day {
		background-color: #fff;
		border: 1px solid #eee;
		cursor: pointer;
	}
	.fc-event {
		cursor: pointer;
	}
</style>
<div class="calendar-env">
	<div class="calendar-body">
		<div id="calendar"></div>
	</div>
</div>
<?
	$events = array();
	foreach ($calendarLogs as $log) {
		$events[] = array(
			'title' => $log['CalendarLog']['coins'] . 'x ' . $log['Activity']['name'],
			'date' => $log['CalendarLog']['acquired'],
			'color' => $log['Domain']['color'],
		);
	}
?>

<script type="text/javascript">
	$(function(){
		Date.prototype.format = function() {
		   var yyyy = this.getFullYear().toString();
		   var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
		   var dd  = this.getDate().toString();
		   return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]); // padding
		};
		var playerName = '<? echo $player['Player']['name']?>';

		$('#calendar').fullCalendar({
			dayClick: function(date, allDay, jsEvent, view) {
				var formattedDate = date.format();
				window.location.href = '<? echo $this->Html->url('/activities/day/'); ?>' + formattedDate + '/<? echo $player['Player']['id']?>';
			},
			eventClick: function(calEvent, jsEvent, view) {
				var formattedDate = calEvent.start.format();
				window.location.href = '<? echo $this->Html->url('/activities/day/'); ?>' + formattedDate + '/<? echo $player['Player']['id']?>';
			},
			header: {
				left: 'title',
				right: 'month, today prev,next'
			},
			titleFormat: {
			    month: 'MMMM yyyy\' - ' + playerName + '\'',                             // September 2009
			    week: "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}", // Sep 7 - 13 2009
			    day: 'dddd, MMM d, yyyy'                  // Tuesday, Sep 8, 2009
			},
			editable: false,
			firstDay: 1,
			height: 600,
			droppable: false,
		    events: <? echo json_encode($events); ?>
		});
	});
</script>