<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1><?= h($player['Player']['name']) ?>, <?= h($player['Player']['title']) ?> lvl <?= number_format($player['Player']['level']) ?></h1>
        </div>
    </div>
    <div class="panel-body">
    	<div class="row">
    		<div class="col-lg-12">
				<h2>Badges</h2>
    			<? if (empty($player['BadgeLog'])): ?>
    				<p>No badges :(</p>
	    		<? else: ?>
					<? foreach ($player['BadgeLog'] as $badge): ?>
						<button class="btn" style="color: white; background-color: <?= h($badge['Badge']['Domain']['color'])?>">
							<i class="<?= h($badge['Badge']['icon']) ?>"></i>
							<?= h($badge['Badge']['name']) ?>
						</button>
		    		<? endforeach ;?>
	    		<? endif; ?>
    		</div>
    	</div>
    	<div class="row">
    		<div class="col-lg-4">
				<h2>Domains</h2>
    			<? if (empty($domains)): ?>
    				<p>No data.</p>
	    		<? else :?>
					<table class="table table-striped table-condensed">
						<thead>
							<tr>
								<th style="text-align: center">D</th>
								<th>Name</th>
								<th>Reports</th>
							</tr>
						</thead>
						<tbody>
							<? foreach ($domains as $domain): ?>
								<tr>
									<td style="color: white; text-align: center; background-color: <?= h($domain['Domain']['color']) ?>"><i class="<?= h($domain['Domain']['icon'])?>"></i></td>
									<td><?= h($domain['Domain']['name'])?></td>
									<td><?= number_format($domain['Domain']['reports'])?></td>
								</tr>
							<? endforeach; ?>
						</tbody>
					</table>
				<? endif; ?>
    		</div>	
    		<div class="col-lg-4">
    			<h2>Activities</h2>
    			<? if (empty($activities)): ?>
    				<p>No data.</p>
	    		<? else :?>
					<table class="table table-striped table-condensed">
						<thead>
							<tr>
								<th style="text-align: center">D</th>
								<th>Name</th>
								<th>Reports</th>
							</tr>
						</thead>
						<tbody>
							<? foreach ($activities as $activity): ?>
								<tr>
									<td style="color: white; text-align: center; background-color: <?= h($activity['Domain']['color']) ?>"><i class="<?= h($activity['Domain']['icon'])?>"></i></td>
									<td><?= h($activity['Activity']['name'])?></td>
									<td><?= number_format($activity['Activity']['reports'])?></td>
								</tr>
							<? endforeach; ?>
						</tbody>
					</table>
				<? endif; ?>
			</div>
    		<div class="col-lg-4">
				<h2>Tags</h2>    			
    			<? if (empty($tags)): ?>
    				<p>No data.</p>
    			<? else :?>
					<table class="table table-striped table-condensed">
						<thead>
							<tr>
								<th>Name</th>
								<th>Reports</th>
							</tr>
						</thead>
						<tbody>
							<? foreach ($tags as $tag): ?>
								<tr>
									<td><?= $this->element('tag', array('tag' => $tag)); ?></td>
									<td><?= number_format($tag['Tag']['reports'])?></td>
								</tr>
							<? endforeach; ?>
						</tbody>
					</table>
				<? endif; ?>
    		</div>
    	</div>
    </div>
</div>

