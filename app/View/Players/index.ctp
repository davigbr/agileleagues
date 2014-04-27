<style type="text/css">
	td {
		height: 65px;
		vertical-align: middle !important;
	}

</style>
<h3>Players</h3>
<br/>
<table class="table table-striped table-bordered">
	<tr>
		<th><strong>Photo</strong></th>
		<th><strong>Name</strong></th>
		<th><strong>Type</strong></th>
		<th><strong>E-mail</strong></th>
		<th><strong>Total XP</strong></th>
		<th><strong>Activity Coins</strong></th>
		<th><strong>Progress</strong></th>
		<th><strong>Information</strong></th>
	</tr>
	<? foreach ($players as $player) : ?>
		<tr>
			<td><img src="<? echo $this->Gravatar->get($player['Player']['email'], 60) ?>" alt="" class="img-rounded" width="60"></td>
			<td><? echo h($player['Player']['name']); ?>, <? echo h($player['Player']['title']); ?></td>
			<td><? echo h($player['PlayerType']['name']); ?></td>
			<td><? echo h($player['Player']['email']); ?></td>
			<td><? echo h($player['Player']['xp']); ?></td>
			<td><? echo h($player['PlayerTotalActivityCoins']['coins']); ?></td>
			<td style="text-align: center">
				<span><? echo (int)$player['Player']['progress']?>%</span>
				
				<div class="progress">
					<div style="width: <? echo (int)$player['Player']['progress']?>%" 
					class="progress-bar progress-bar-info"></div>
				</div>
				<? echo h($player['Player']['level']); ?>
				<i class="glyphicon glyphicon-arrow-right"></i>
				<? echo h(1+$player['Player']['level']); ?>
			</td>
			<td>
				<a class="btn btn-primary" href="<? echo $this->Html->url('/activities/calendar/' . $player['Player']['id']); ?>">
					<i class="entypo entypo-calendar"></i> Activities
				</a>
			</td>
		</tr>
	<? endforeach; ?>
</table>