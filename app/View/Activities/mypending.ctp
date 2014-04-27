<h3>My Pending Activities</h3>
<br/>
<? if (empty($logs)): ?>
	<p>No data! :(</p>
<? else: ?>
	<table class="table table-bordered table-striped table-condensed">
		<tr>
			<th style="text-align: center" title="Domain">D</th>
			<th>Name</th>
			<th>Logged</th>
			<th>Acquired</th>
			<th>Event</th>
			<th>Description</th>
			<th>Actions</th>
		</tr>
		<? foreach ($logs as $log) : ?>
			<tr>
				<td style="text-align: center; width: 35px; background-color: <? echo $log['Domain']['color']?>; color: white">
					<i class="<? echo $log['Domain']['icon']?>"></i>
				</td>
				<td><? echo $log['Activity']['name']?></td>
				<td><? echo $log['Log']['creation']?></td>
				<td><? echo $log['Log']['acquired']?></td>
				<td><? echo h($log['Event']['name']); ?></td>
				<td><? echo h($log['Log']['description']); ?></td>
				<td>
					<? echo $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/logs/delete/' . $log['Log']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to delete this activity log?')) ?>
				</td>
			</tr>
		<? endforeach; ?>
	</table>

<? endif; ?>