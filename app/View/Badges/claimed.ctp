<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Claimed Badges</h1>
        </div>
    </div>
    <?if (empty($logs)): ?>
	    <div class="panel-body">
		    <p>No claimed badges found :( </p>
	    </div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-responsive table-striped table-bordered table-condensed">
				<tr>
					<th style="text-align: center" title="Domain">D</th>
					<th>Name</th>
					<th>Who Claimed</th>
					<th>When</th>
					<th>Credly Id</th>
					<th>Credly Name</th>
					<th>Credly Image</th>
					<th>Credly Status</th>
					<th>Actions</th>
				</tr>
				<? foreach ($logs as $log) : ?>
					<tr>
						<td title="<?= h($log['Badge']['Domain']['name']); ?>" style="width: 35px; text-align: center; background-color: <?= $log['Badge']['Domain']['color']?>; color: white">
							<i class="<?= h($log['Badge']['icon']); ?>"></i>
						</td>
						<td>
							<a href="<?= $this->Html->url('/badges/view/' . $log['Badge']['id']); ?>"><?= $log['Badge']['name']?></a>
						</td>
						<td>
							<?= h($log['Player']['name']) ?>
						</td>
						<td>
							<?= $this->Format->date($log['BadgeLog']['creation'])?>
						</td>
						<td>
							<?= h($log['Badge']['credly_badge_id'])?>
						</td>
						<td>
							<?= h($log['Badge']['credly_badge_name'])?>
						</td>
						<td>
							<a target="_blank" href="<?= h($log['Badge']['credly_badge_image_url'])?>">
								<img height="50" src="<?= h($log['Badge']['credly_badge_image_url'])?>"/>
							</a>
						</td>
						<td>
							<?= $log['BadgeLog']['credly_given']? 'Given' : 'Not Given'?>
						</td>
						<td>
							<div class="btn-group">
								<?if ($log['Badge']['credly_badge_id']): ?>
									<div class="btn-group">
										<? $playerCredlyAccountSetup = (bool)$log['Player']['credly_id']; ?>
										<?if ($playerCredlyAccountSetup): ?>
											<a title="Give Credit" href="<?= $this->Html->url('/badges/credlyGive/' . $log['BadgeLog']['id'])?>" class="btn btn-success btn-sm">
												Give Credit
											</a>
										<?else:?>
											<a title="This player has not setup his/her Credly account yet" href="#" class="disabled btn btn-success btn-sm">
												Give Credit
											</a>
										<?endif;?>
										<a title="Update Credly Information" href="<?= $this->Html->url('/badges/credlyUpdate/' . $log['Badge']['id']); ?>" class="btn btn-primary btn-sm">
											<i class="glyphicon glyphicon-refresh"></i>
										</a>
									</div>
								<?endif;?>
							</div>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<?endif;?>
</div>		