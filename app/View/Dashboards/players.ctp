<style type="text/css">
	td, th { text-align: center; }
	table { table-layout: fixed; }
	td {
		vertical-align: top !important;
	}
</style>
<div class="panel panel-primary panel-table">
	<div class="panel-body with-table">
		<table class="table table-responsive">
			<tr>
				<?foreach ($players as $player): ?>
					<th><?= h($player['Player']['name']) . ', ' . h($player['Player']['title'])?></th>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<th><?= h($player['Team']['name']) ?></th>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<th>Level <?= $player['Player']['level']?> <?= h($player['PlayerType']['name'])?></th>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						<img style="width: 80px; height: 80px" 
							class="img-circle" 
							alt="<?= $player['Player']['name']?>" 
							src="<?= $this->Gravatar->get($player['Player']['email']); ?>"/>
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<?
						$badges = isset($player['BadgeLog'])? $player['BadgeLog'] : array();
					?>
					<td>
						<h3>Badges</h3>
						<?if (!empty($badges)): ?>
							<?foreach ($badges as $badge): ?>
								<a style="margin: 2px; border-color: <?= $badge['Badge']['Domain']['color']?>; background-color: <?= $badge['Badge']['Domain']['color']?>" class="btn btn-primary btn-xs" href="#">
									<i class="<?= h($badge['Badge']['icon'])?>"></i>
									<?= h($badge['Badge']['name']); ?>
								</a>
							<?endforeach; ?>
						<?endif;?>
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						Activity Coins:
						<strong><?= number_format($player['PlayerTotalActivityCoins']['coins']) ?></strong>
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						Total XP: <strong><?= number_format($player['Player']['xp']) ?></strong><br/>
						<small > 
							Level Progress:
							<?=(int)$player['Player']['progress']?>%
							(<?= number_format($player['Player']['next_level_xp_completed']) ?> / <?= number_format($player['Player']['next_level_xp']); ?> XP)
						</small> 
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						<a class="btn btn-primary" href="<?= $this->Html->url('/activities/calendar/' . $player['Player']['id']); ?>">
							<i class="entypo entypo-calendar"></i> Activities
						</a>
					</td>
				<?endforeach;?>
			</tr>
		</table>
	</div>
</div>
