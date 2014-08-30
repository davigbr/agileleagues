<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Activities</h1>
        </div>
    </div>
    <div class="panel-body">
    	<p><?= __('Activities are actions that the players will perform and report.')?></p>
	    <?if ($isGameMaster): ?>
			<a href="<?= $this->Html->url('/activities/add'); ?>" class="btn btn-md btn-success"><i class="glyphicon glyphicon-plus"></i> Create New Activity </a>
		<?endif;?>
    </div>
    <?if (empty($activities)): ?>
	    <div class="panel-body">
		    <p>No activities found :( </p>
	    </div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th style="text-align: center" title="Domain">D</th>
					<th>Name</th>
					<th>XP</th>
					<th title="<?=__('Required Votes for Acceptance')?>">A. Votes</th>
					<th title="<?=__('Required Votes for Rejection')?>">R. Votes</th>
					<th>Last Week Logs</th>
					<th>Actions</th>
				</tr>
				<?if (empty($activities)): ?>
					<tr>
						<td colspan="6">
							<p style="text-align: center">No activities :(</p>
						</td>
					</tr>
				<?else:?>
					<? foreach ($activities as $activity) : ?>
						<tr>
							<td title="<?= h($activity['Domain']['name']); ?>" style="width: 35px; text-align: center; background-color: <?= $activity['Domain']['color']?>; color: white">
								<i class="<?= h($activity['Domain']['icon']); ?>"></i>
							</td>
							<td title="<?= h($activity['Activity']['description']) ?>">
								<?if ($activity['Activity']['new']): ?>
									<span class="badge badge-danger">NEW</span>
								<?endif;?>
								<?= h($activity['Activity']['name']) ?>
							</td>
							<td><?= h($activity['Activity']['xp']) ?></td>
							<td>
								<? for($i = 0; $i < $activity['Activity']['acceptance_votes']; $i++) { 
									?><i style="color: green" class="entypo-up"></i><?
								}?>
							</td>
							<td>
								<? for($i = 0; $i < $activity['Activity']['rejection_votes']; $i++) { 
									?><i style="color: red" class="entypo-down"></i><?
								}?>
							</td>
							<td>
								<span class="last-week-logs">
									<?= $activity['LastWeekLog']['logs']?>
								</span>
							</td>
							<td>
								<div class="btn-group">
									<a href="<?= $this->Html->url('/activities/view/' . $activity['Activity']['id']); ?>" class="btn btn-primary btn-sm">
										<i class="glyphicon glyphicon-search"></i>
									</a>
									<?if ($isGameMaster): ?>
										<a href="<?= $this->Html->url('/activities/edit/' . $activity['Activity']['id']); ?>" class="btn btn-primary btn-sm">
											<i class="glyphicon glyphicon-edit"></i>
										</a>
										<?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/activities/inactivate/' . $activity['Activity']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to inactivate this activity?')) ?>
									<?endif;?>
								</div>
							</td>
						</tr>
					<? endforeach; ?>
				<?endif;?>
			</table>
	    </div>
    <?endif;?>
</div>
<script type="text/javascript">
	$(function(){
		$(".last-week-logs").sparkline('html', {
		    type: 'line',
		    width: '100px',
		    height: '15px',
		    lineColor: '#ff4e50',
		    fillColor: '',
		    lineWidth: 2,
		    spotColor: '#a9282a',
		    minSpotColor: '#a9282a',
		    maxSpotColor: '#a9282a',
		    highlightSpotColor: '#a9282a',
		    highlightLineColor: '#f4c3c4',
		    spotRadius: 2,
		    drawNormalOnTop: true
		 });
	});
</script>