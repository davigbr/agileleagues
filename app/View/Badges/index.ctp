<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Badges</h1>
        </div>
    </div>
    <div class="panel-body">
    	<p>If you want to create new Badges, please access the <a href="<?= $this->Html->url('/domains')?>">Domains</a>.</p>
    </div>
    <?if (empty($badges)): ?>
	    <div class="panel-body">
		    <p>No badges found :( </p>
	    	<p><?= __('If you want to create new badges, please access the corresponding Domain.')?></p>
	    </div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-responsive table-striped table-bordered table-condensed">
				<tr>
					<th style="text-align: center" title="Domain">D</th>
					<th>Name</th>
					<th>Required Badges</th>
					<th>Required Activities</th>
					<th>Actions</th>
				</tr>
				<? foreach ($badges as $badge) : ?>
					<tr>
						<td title="<?= h($badge['Domain']['name']); ?>" style="width: 35px; text-align: center; background-color: <?= $badge['Domain']['color']?>; color: white">
							<i class="<?= h($badge['Badge']['icon']); ?>"></i>
						</td>
						<td>
							<?if ($badge['Badge']['new']): ?>
								<span class="badge badge-danger">NEW</span>
							<?endif;?>
							<a href="<?= $this->Html->url('/badges/view/' . $badge['Badge']['id']); ?>"><?= $badge['Badge']['name']?></a>
						</td>
						<td>
							<?foreach($badge['BadgeRequisite'] as $badgeRequisite): ?>
								<? $requiredBadge = $badgesById[$badgeRequisite['badge_id_requisite']];?>
								<?= $requiredBadge['Badge']['name']?>; 
							<?endforeach;?>
						</td>
						<td>	
							<?foreach($badge['ActivityRequisite'] as $activityRequisite): ?>
								<? $activity = $activitiesById[$activityRequisite['activity_id']];?>
								<?= $activityRequisite['count']?>x 
								<?= $activity['Activity']['name']?>; 
							<?endforeach;?>
						</td>
						<td>
							<?if ($isGameMaster): ?>
								<div class="btn-group">
									<div class="btn-group">
										<a title="Edit Badge" href="<?= $this->Html->url('/badges/edit/' . $badge['Badge']['domain_id'] . '/' . $badge['Badge']['id']); ?>" class="btn btn-primary btn-sm">
											<i class="glyphicon glyphicon-edit"></i>
										</a>
										<a title="Inactivate Badge" href="<?= $this->Html->url('/badges/inactivate/' . $badge['Badge']['id']); ?>" class="btn btn-danger btn-sm">
											<i class="glyphicon glyphicon-trash"></i>
										</a>
									</div>
								</div>
							<?endif;?>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<?endif;?>
</div>		