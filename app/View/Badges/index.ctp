<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Badges</h1>
        </div>
    </div>
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
					<td title="<? echo h($badge['Domain']['name']); ?>" style="width: 35px; text-align: center; background-color: <? echo $badge['Domain']['color']?>; color: white">
						<i class="<? echo h($badge['Badge']['icon']); ?>"></i>
					</td>
					<td>
						<?if ($badge['Badge']['new']): ?>
							<span class="badge badge-danger">NEW</span>
						<?endif;?>
						<a href="<? echo $this->Html->url('/badges/view/' . $badge['Badge']['id']); ?>"><? echo $badge['Badge']['name']?></a>
					</td>
					<td>
						<?foreach($badge['BadgeRequisite'] as $badgeRequisite): ?>
							<? $requiredBadge = $badgesById[$badgeRequisite['badge_id_requisite']];?>
							<? echo $requiredBadge['Badge']['name']?>; 
						<?endforeach;?>
					</td>
					<td>	
						<?foreach($badge['ActivityRequisite'] as $activityRequisite): ?>
							<? $activity = $activitiesById[$activityRequisite['activity_id']];?>
							<? echo $activityRequisite['count']?>x 
							<? echo $activity['Activity']['name']?>; 
						<?endforeach;?>
					</td>
					<td>
						<?if ($isScrumMaster): ?>
							<div class="btn-group">
								<a href="<? echo $this->Html->url('/badges/edit/' . $badge['Badge']['domain_id'] . '/' . $badge['Badge']['id']); ?>" class="btn btn-primary btn-sm">
									<i class="glyphicon glyphicon-edit"></i>
								</a>
							</div>
						<?endif;?>
					</td>
				</tr>
			<? endforeach; ?>
		</table>
	</div>
</div>		