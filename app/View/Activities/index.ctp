<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Activities</h1>
        </div>
    </div>
    <div class="panel-body with-table">
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th style="text-align: center" title="Domain">D</th>
				<th>Name</th>
				<th>XP</th>
				<th>Description</th>
				<th>Last Week Logs</th>
				<?if ($isScrumMaster): ?>
					<th>
						<a href="<? echo $this->Html->url('/activities/add'); ?>" class="btn btn-large btn-success"><i class="glyphicon glyphicon-plus"></i> New </a>
					</th>
				<?endif;?>
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
						<td title="<? echo h($activity['Domain']['name']); ?>" style="width: 35px; text-align: center; background-color: <? echo $activity['Domain']['color']?>; color: white">
							<i class="<? echo h($activity['Domain']['icon']); ?>"></i>
						</td>
						<td>
							<?if ($activity['Activity']['new']): ?>
								<span class="badge badge-danger">NEW</span>
							<?endif;?>
							<? echo h($activity['Activity']['name']) ?>
						</td>
						<td><? echo h($activity['Activity']['xp']) ?></td>
						<td><? echo h($activity['Activity']['description']) ?></td>
						<td>
							<span class="last-week-logs">
								<? echo $activity['LastWeekLog']['logs']?>
							</span>
						</td>
						<?if ($isScrumMaster): ?>
							<td>
								<a href="<? echo $this->Html->url('/activities/edit/' . $activity['Activity']['id']); ?>" class="btn btn-primary btn-sm">
									<i class="glyphicon glyphicon-edit"></i>
								</a>
								<? echo $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/activities/inactivate/' . $activity['Activity']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to inactivate this activity?')) ?>
							</td>
						<?endif;?>
					</tr>
				<? endforeach; ?>
			<?endif;?>
		</table>
    </div>
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