<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1><?= h($badge['Badge']['name']); ?></h1>
        </div>
    </div>
</div>

<div class="row">
	<div class="col-md-6">
    	 <div class="panel panel-primary panel-table">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Activity Requisites</h3>
                    <span>Activity coins necessary for claiming this badge.</span>    
                </div>
            </div>
            <div class="panel-body">
            	<?if (empty($requiredActivitiesProgress)): ?>
            		<p>No activities required!</p>
            	<?else:?>
	            	<table class="table table-responsive">
	            		<tr>
	            			<th>Activity</th>
	            			<th>Activities Completed / Required</th>
	            			<th>Progress</th>
	            		</tr>
            			<?foreach($requiredActivitiesProgress as $activity): ?>
            				<tr>
        						<td><a href="<?= $this->Html->url('/activities'); ?>"><?= h($activity['Activity']['name']); ?></a></td>
        						<td>
                                    <?= $activity['BadgeActivityProgress']['activities_completed']?> /
        						    <?= $activity['BadgeActivityProgress']['activities_required']?>
                                    (<?= $activity['BadgeActivityProgress']['progress']?>%)
                                </td>
        						<td style="text-align: center">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: <?= $activity['BadgeActivityProgress']['progress']?>%">
                                        </div>
                                    </div>
                                </td>
            				</tr>
            			<?endforeach;?>
	            	</table>
            	<?endif;?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
    	 <div class="panel panel-primary panel-table">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Badge Requisites</h3>
                    <span>Other badges required for claiming this one.</span>    
                </div>
            </div>
            <div class="panel-body">
            	<?if (empty($badge['BadgeRequisite'])): ?>
            		<p>No badges required!</p>
            	<?else:?>
	            	<table class="table table-responsive">
                        <tr>
                            <th>Claimed</th>
                            <th>Badge</th>
                        </tr>
                        <?foreach($badge['BadgeRequisite'] as $badgeRequisite): ?>
                            <? $b = $badgesClaimed[$badgeRequisite['badge_id_requisite']]?>
                            <tr>
                                <td>
                                    <?if($b['BadgeClaimed']['claimed']):?>
                                        <i style="color: green" class="glyphicon glyphicon-ok"></i>&nbsp;&nbsp;
                                    <?else:?>
                                        <i style="color: red" class="glyphicon glyphicon-remove"></i>&nbsp;&nbsp;
                                    <?endif;?>
                                </td>
                                <td><a href="<?= $this->Html->url('/badges'); ?>"><?= h($b['Badge']['name']); ?></a></td>
                            </tr>
                        <?endforeach;?>
	            	</table>
            	<?endif;?>
            </div>
        </div>
    </div>
</div>
<?if ($claimed || $canClaim): ?>
    <div class="well">
        <?if ($claimed): ?>
            <p>Congratulations, you already have claimed this badge!</p>
        <?elseif ($canClaim): ?>
            <a href="<?= $this->Html->url('/badges/claim/' . $badge['Badge']['id']); ?>" class="btn btn-lg btn-green">Claim this badge now!</a>
        <?endif;?>
    </div>
<?endif;?>
