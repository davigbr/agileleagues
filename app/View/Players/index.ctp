<style type="text/css">
	td {
		height: 65px;
		vertical-align: middle !important;
	}

</style>
<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Players</h1>
        </div>
    </div>
    <div class="panel-body with-table">
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th style="text-align: center"><strong>Avatar</strong></th>
				<th><strong>Name</strong></th>
				<th><strong>Team</strong></th>
				<th><strong>Type</strong></th>
				<th><strong>E-mail</strong></th>
				<th><strong>Total XP</strong></th>
				<th><strong>Activity Coins</strong></th>
				<th><strong>Progress</strong></th>
				<th><strong>Actions</strong></th>
			</tr>
			<? foreach ($players as $player) : ?>
				<tr>
					<td style="text-align: center"><img src="<? echo $this->Gravatar->get($player['Player']['email'], 60) ?>" alt="" class="img-rounded" width="60"></td>
					<td><? echo h($player['Team']['name']); ?></td>
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
							<i class="entypo-calendar"></i> Activities
						</a>
						<?if ($isScrumMaster): ?>
							<a title="<?= __('Change team')?>" class="btn btn-info" href="<? echo $this->Html->url('/players/team/' . $player['Player']['id']); ?>">
								<i class="entypo-users"></i> Team
							</a>
						<?endif;?>
					</td>
				</tr>
			<? endforeach; ?>
		</table>
	</div>
</div>		