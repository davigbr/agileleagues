<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
        	<h3>Activities Acquired in <?= $this->Format->date($date)?> by <?= h($player['Player']['name'])?></h3>
    	</div>
	</div>
	<? if (empty($logs)): ?>
		<div class="panel-body">
			<p>No data! :(</p>
		</div>
	<? else: ?>
		<div class="panel-body with-table">
			<table class="table table-bordered table-striped table-condensed">
				<tr>
					<th style="text-align: center" title="Domain">D</th>
					<th>Name</th>
					<th>Logged</th>
					<th>Acquired</th>
					<th>Reviewed</th>
					<th>Description</th>
				</tr>
				<? foreach ($logs as $log) : ?>
					<tr>
						<td style="text-align: center; width: 35px; background-color: <?= h($log['Domain']['color'])?>; color: white">
							<i class="<?= h($log['Domain']['icon'])?>"></i>
						</td>
						<td><?= h($log['Activity']['name'])?></td>
						<td><?= h($log['Log']['created'])?></td>
						<td><?= h($log['Log']['acquired'])?></td>
						<td><?= h($log['Log']['reviewed'])?></td>
						<td><?= h($log['Log']['description'])?></td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<? endif; ?>
</div>