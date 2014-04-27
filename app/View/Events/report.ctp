<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Report Event Task</strong></div>
			</div>
			<div class="panel-body">
				<? echo $this->Bootstrap->create('EventTaskLog'); ?>
				<? echo $this->Bootstrap->input('event_id', array('class'=>'form-control', 'empty'=>'-')); ?>
				<? echo $this->Bootstrap->input('event_task_id', array('label' => 'Task', 'class'=>'form-control', 'empty'=>'-')); ?>
				<div id="event-task-description" class="alert alert-info">
					-
				</div>
				<div class="alert alert-warning">
					<strong>Note:</strong> You need to join an event before reporting its tasks.
				</div>
				<button type="submit" class="btn btn-success">Report</button>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		var events = <? echo json_encode($allEvents);?>;
		var getEventTasks = function(id) {
			for (var i in events) {
				if (events[i].Event.id == id) {
					return events[i].EventTask;
				}
			}
		}

		var $select = $('#EventTaskLogEventTaskId');
		$select.change(function(){
			var $option = $('option:selected', $(this));
			var description = '-';
			if ($option.length) {
				description = $option.attr('data-description');
			}
			$('#event-task-description').html(description);
		});
		
		$('#EventTaskLogEventId').change(function(){
			var eventId = $(this).val();
			var options = [];
			if (eventId) {
				var tasks = getEventTasks(eventId);
				for (var i in tasks) {
					var task = tasks[i];
					options[task['id']] = task;
				}
			}
			var selected = <? echo (int)@$this->request->data['EventTaskLog']['event_task_id'] ?>;
			$select.html('');
			$select.append('<option value="">-</option>');
			for (var id in options) {
				var name = options[id]['name'];
				var description = options[id]['description'];
				$select.append('<option data-description="' + description + '" value="' + id + '">' + name + '</option>');
			}
			if (selected) {
				$select.val(selected);
			}
		}).change();
	});
</script>
