<h3>Event Tasks Not Reviewed</h3>
<br/>
<? if (empty($eventTaskLogs)): ?>
	<p>No pending event tasks.</p>
<? else : ?>
	<table class="table table-bordered table-condensed table-striped">
		<tr>
			<th>Player</th>
			<th>Reported</th>
			<th>Event Task</th>
			<th>Description</th>
			<th>XP</th>
			<th>Actions</th>
		</tr>
		<? foreach ($eventTaskLogs as $log) : ?>
			<tr>
				<td><? echo h($log['Player']['name']); ?></td>
				<td><? echo h($log['EventTaskLog']['created']); ?></td>
				<td><? echo h($log['EventTask']['name']); ?></td>
				<td><? echo h($log['EventTask']['description']); ?></td>
				<td><? echo number_format($log['EventTask']['xp']); ?></td>
				<td>
					<?if($isScrumMaster): ?>
						<div class="btn-group">
							<a class="btn btn-success btn-sm" href="<? echo $this->Html->url('/events/review/' . $log['EventTaskLog']['id']); ?>">
								<i class="glyphicon glyphicon-ok"></i>
							</a>
						</div>				
					<?endif;?>	
				</td>
			</tr>
		<? endforeach; ?>
	</table>
<? endif; ?>