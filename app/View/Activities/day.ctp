<h3>Activities Acquired in <? echo $date?> by <? echo $player['Player']['name']?></h3>
<br/>
<? if (empty($logs)): ?>
	<p>No data! :(</p>
<? else: ?>
	<table class="table table-bordered table-striped table-condensed">
		<tr>
			<th style="text-align: center" title="Domain">D</th>
			<th>Name</th>
			<th>Logged</th>
			<th>Reviewed</th>
			<th>Description</th>
		</tr>
		<? foreach ($logs as $log) : ?>
			<tr>
				<td style="text-align: center; width: 35px; background-color: <? echo $log['Domain']['color']?>; color: white">
					<i class="<? echo $log['Domain']['icon']?>"></i>
				</td>
				<td><? echo $log['Activity']['name']?></td>
				<td><? echo $log['Log']['creation']?></td>
				<td><? echo $log['Log']['reviewed']?></td>
				<td><? echo $log['Log']['description']?></td>
		<? endforeach; ?>
	</table>

<? endif; ?>