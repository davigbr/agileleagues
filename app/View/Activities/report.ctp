<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Report Activity</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Log'); ?>
				<?= $this->Bootstrap->input('activity_id', array('class'=>'form-control', 'empty'=>'-')); ?>
				<div class="alert alert-info">
					<div id="activity-description">
					</div>
				</div>
				<?= $this->Bootstrap->input('description', array('class'=>'form-control')); ?>
				<?= $this->Bootstrap->input('acquired', array('class'=>'form-control form-control-inline')); ?>
				<?= $this->Bootstrap->input('event_id', array(
					'label' => 'Event', 
					'empty' => '-', 
					'options' => $events, 
					'class' => 'form-control form-control-inline')); ?>
				<?= $this->Bootstrap->input('player_id_pair', array(
					'label' => 'Paired With', 
					'empty' => '-', 
					'options' => $players, 
					'class' => 'form-control form-control-inline')); ?>
				<div class="alert alert-warning">
					<strong>Warning:</strong> activities peformed more than week day ago cannot be reported :(
				</div>
				<button type="submit" class="btn btn-success">Report</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		var activities = <?= json_encode($activitiesById); ?>;
		$('#LogActivityId').change(function(){
			var activityId = $(this).val();
			if (activityId) {
				$('#activity-description').html(activities[activityId].Activity.description);
			} else {
				$('#activity-description').html('-');
			}
		}).change();
	});
</script>
	