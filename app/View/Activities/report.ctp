<div class="panel panel-primary" data-collapsed="0">
	<div class="panel-heading">
		<div class="panel-title"><strong>Report Activity</strong></div>
	</div>
	<?= $this->Bootstrap->create('Log'); ?>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<?= $this->Bootstrap->input('activity_id', array(
					'class' => 'form-control form-control-inline', 
					'empty' => '-',
					'options' => $activities,
					'after' => ' <a onclick="return false" id="activity-xp" style="cursor: default" class="hide btn btn-success">-</a>'
				)); ?>
				<div class="hide alert alert-info">
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
					'after' => ' <a onclick="return false" id="activity-pair" title="' . __('You will earn %s%% additional XP for pair activities.', floor(100 * PAIR_XP_MULTIPLIER - 100)) . '" style="cursor: default" class="hide btn btn-info">+' . floor(100 * PAIR_XP_MULTIPLIER - 100) . '% XP</a>',
					'class' => 'form-control form-control-inline')); ?>
				<h3>Tags</h3>
				<? if(empty($tags)): ?>
					<p>No tags available :(</p>
				<? else: ?>
					<p>Please select the tags that match the activity you are reporting. </p>
					<table id="tags" class="table table-bordered table-striped table-condensed">
						<?$i = 0; ?>
						<?foreach ($tags as $tag): ?>
							<tr style="cursor: pointer">
								<td style="width: 24px"><input name="data[Tags][Tags][<?=$i?>]" value="<?= (int)$tag['Tag']['id']?>" type="checkbox"  /></td>
								<td><?= $this->element('tag', array('tag' => $tag)); ?> <?= h($tag['Tag']['description'])?></td>
							</tr>
							<?$i++;?>
						<?endforeach;?>
					</table>
				<? endif; ?>
				<div class="alert alert-warning">
					<strong>Warning:</strong> activities peformed more than week day ago cannot be reported :(
				</div>
			</div>
		</div>
		<button type="submit" class="btn btn-success">Report</button>
	</div>
	<?= $this->Bootstrap->end(); ?>
</div>
<script type="text/javascript">
	$(function(){
		var activities = <?= json_encode($activitiesById); ?>;

		$('#LogActivityId').change(function(){
			var activityId = $(this).val();
			if (activityId) {
				$('#activity-description').html(activities[activityId].Activity.description);
				$('#activity-description').parent().removeClass('hide');
				$('#activity-xp').html('+' + activities[activityId].Activity.xp + ' XP');
				$('#activity-xp').removeClass('hide');
			} else {
				$('#activity-description').parent().addClass('hide');
				$('#activity-xp').addClass('hide');
			}
		}).change();
		
		$('#LogPlayerIdPair').change(function() {
			if ($(this).val()) {
				$('#activity-pair').removeClass('hide');
			} else {
				$('#activity-pair').addClass('hide');
			}
		}).change();

		$('#tags input').click(function(event){
			event.stopPropagation();
		});

		$('#tags tr').click(function(){
			$('input', $(this)).click();
		});
	});
</script>
	