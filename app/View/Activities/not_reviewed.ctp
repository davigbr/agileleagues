<h3>Activities Not Reviewed</h3>
<br/>
<? if (empty($logs)): ?>
	<p>No pending activities.</p>
<? else : ?>
	<table class="table table-bordered table-condensed table-striped">
		<tr>
			<th>Domain</th>
			<th>Player</th>
			<th>Acquired</th>
			<th>Event</th>
			<th>Activity</th>
			<th>Description</th>
			<th>XP</th>
			<th>Actions</th>
		</tr>
		<? foreach ($logs as $log) : ?>
			<tr>
				<td style="width: 35px; text-align: center; background-color: <? echo $log['Activity']['Domain']['color']?>; color: white">
					<? echo h($log['Activity']['Domain']['abbr']) ?>
				</td>
				<td><? echo h($log['Player']['name']); ?></td>
				<td><? echo h($log['Log']['acquired']); ?></td>
				<td><? echo h(isset($log['Event'])? $log['Event']['name'] : ''); ?></td>
				<td><? echo h($log['Activity']['name']); ?></td>
				<td><? echo h($log['Log']['description']); ?></td>
				<td><? echo number_format($log['Activity']['xp']); ?></td>
				<td>
					<?if($isScrumMaster): ?>
						<div class="btn-group">
							<a class="btn btn-success btn-sm" href="<? echo $this->Html->url('/logs/review/' . $log['Log']['id']); ?>">
								<i class="glyphicon glyphicon-ok"></i>
							</a>
						</div>				
					<?endif;?>	
				</td>
			</tr>
		<? endforeach; ?>
	</table>
<? endif; ?>