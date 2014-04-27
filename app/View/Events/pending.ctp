<h3>Pending Event Tasks</h3>
<br/>
<? if (empty($eventTaskLogs)): ?>
	<p>No data! :(</p>
<? else: ?>
	<table class="table table-bordered table-striped table-condensed">
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Event</th>
			<th>Logged</th>
			<th>Actions</th>
		</tr>
		<? foreach ($eventTaskLogs as $log) : ?>
			<tr>
				<td><? echo h($log['EventTask']['name']); ?></td>
				<td><? echo h($log['EventTask']['description']); ?></td>
				<td><a href="<? echo $this->Html->url('/events/details/' . $log['Event']['id'] ); ?>"><? echo h($log['Event']['name']); ?></a></td>
				<td><? echo $log['EventTaskLog']['creation']?></td>
				<td>
					<? echo $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/events/deleteTask/' . $log['EventTaskLog']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to delete this event task log?')) ?>
				</td>
			</tr>
		<? endforeach; ?>
	</table>

<? endif; ?>