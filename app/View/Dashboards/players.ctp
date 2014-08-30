<style type="text/css">
	td, th { text-align: center; }
	table { table-layout: fixed; }
	.big-text {
		font-size: 31px;
	}
</style>
<div class="panel panel-primary panel-table">
	<div class="panel-body with-table" style="text-align: center">
		<table class="table table-responsive">
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						<span class="big-text"><?= h($player['Player']['name'])?></span>
						<br/>
						<small><?= h($player['Player']['title'])?></small>
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<th>Level <?= $player['Player']['level']?></th>
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
					<td>
						Activities Logged:
						<strong><?= number_format($player['Player']['activities']) ?></strong>
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<td>
						<strong><?= number_format($player['Player']['xp']) ?></strong>
						<br/>
						<small>Experience Points</small>
						<br/>
						<br/>
						<small>
							Level Progress:
							<?=(int)$player['Player']['progress']?>%
							(<?= number_format($player['Player']['next_level_xp_completed']) ?> / <?= number_format($player['Player']['next_level_xp']); ?> XP)
						</small> 
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<?
						$badges = isset($player['BadgeLog'])? $player['BadgeLog'] : array();
					?>
					<td>
						<span class="big-text"><?= number_format(count($badges)); ?></span>
						<br/>
						<strong>Badge<?= count($badges) == 1 ? '' : 's'?></strong>
					</td>
				<?endforeach;?>
			</tr>
			<tr>
				<?foreach ($players as $player): ?>
					<?
						$badges = isset($player['BadgeLog'])? $player['BadgeLog'] : array();
					?>
					<td style="vertical-align: top !important">
						<div style="display: inline-block; text-align: left">
							<?if (!empty($badges)): ?>
								<?foreach ($badges as $badge): ?>
									<a title="<?= h($badge['Badge']['name']); ?>" style="min-width: 45px; margin: 2px; border-color: <?= $badge['Badge']['Domain']['color']?>; background-color: <?= $badge['Badge']['Domain']['color']?>" class="btn btn-primary btn-md" href="#">
										<i class="<?= h($badge['Badge']['icon'])?>"></i> 
									</a> 
									<span><?= h($badge['Badge']['name']); ?></span>
									<br/>
								<?endforeach; ?>
							<?endif;?>
						</div>
					</td>
				<?endforeach;?>
			</tr>
			<!-- <tr>
				<?foreach ($players as $player): ?>
					<td>
						<a class="btn btn-primary" href="<?= $this->Html->url('/activities/calendar/' . $player['Player']['id']); ?>">
							<i class="entypo entypo-calendar"></i> Activities
						</a>
					</td>
				<?endforeach;?>
			</tr> -->
		</table>
		<br/>
		<input type="checkbox" id="auto-update">
        <label for="auto-update">Auto Update</label>
		<br/>
		<br/>
	</div>
</div>
<script type="text/javascript">
	if (window.location.hash == '#autoupdate') {
		$('#auto-update').click();
	}

	$(function(){
		$('#auto-update').click(function(){
			if ($(this).is(':checked')) {
				window.location.hash = '#autoupdate';
			} else {
				window.location.hash = '';
			}
		}); 
		var timer = 60*1000;
		var timerFunc = function() {
			if ($('#auto-update').is(':checked')) {
				window.location.reload();
			}
			setTimeout(timerFunc, timer);
		};
		setTimeout(timerFunc, timer);
	});
</script>