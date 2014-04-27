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
					<th><? echo $player['Player']['name'] . ', ' . $player['Player']['title']?></th>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<th>Level <? echo $player['Player']['level']?></th>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						<img style="width: 80px; height: 80px" 
							class="img-circle" 
							alt="<? echo $player['Player']['name']?>" 
							src="<? echo $this->Gravatar->get($player['Player']['email']); ?>"/>
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
								<a style="margin: 2px; border-color: <? echo $badge['Badge']['Domain']['color']?>; background-color: <? echo $badge['Badge']['Domain']['color']?>" class="btn btn-primary btn-xs" href="#">
									<i class="<? echo $badge['Badge']['icon']?>"></i>
									<? echo $badge['Badge']['name']; ?>
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
						<strong><? echo number_format($player['PlayerTotalActivityCoins']['coins']) ?></strong>
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						Total XP: <strong><? echo number_format($player['Player']['xp']) ?></strong><br/>
						<small > 
							Level Progress:
							<?=(int)$player['Player']['progress']?>%
							(<? echo number_format($player['Player']['next_level_xp_completed']) ?> / <? echo number_format($player['Player']['next_level_xp']); ?> XP)
						</small> 
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						<a class="btn btn-primary" href="<? echo $this->Html->url('/activities/calendar/' . $player['Player']['id']); ?>">
							<i class="entypo entypo-calendar"></i> Activities
						</a>
					</td>
				<?endforeach;?>
			</tr>
		</table>
	</div>
</div>
