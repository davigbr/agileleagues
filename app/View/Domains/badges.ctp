<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h3><?= h($domain['Domain']['name'])?> Badges</h3>
        </div>
    </div>
</div>
<div class="row">
    <?foreach ($badges as $badgeId => $badge): ?>
        <?
            $activitiesProgress = $badgeActivitiesProgress[$badgeId];
        ?>
        <div class="col-lg-3 col-md-6">
            <div class="tile-title" style="height: 330px; <?= $badge['claimed']? 'background-color: ' . $badge['Domain']['color']:''?>">           
                <div class="icon">
                    <a href="<?= $this->Html->url('/badges/view/' . $badgeId); ?>">
                        <?if($badge['claimed']):?>
                            <i class="<?= $badge['Badge']['icon']? h($badge['Badge']['icon']) : 'fa fa-question'?>"></i>
                        <?else:?>
                            <i class="glyphicon glyphicon-lock"></i>
                        <?endif;?>
                    </a>
                </div>
                <div class="title">
                    <a href="<?= $this->Html->url('/badges/view/' . $badgeId); ?>">
                        <h3>
                            <?= h($badge['Badge']['name']); ?>&nbsp;
                        </h3>
                        <?if(!$badge['claimed'] && $badge['progress'] === 100):?>
                            <p>All required activities have been <strong style="color: green">completed</strong>. Claim now!</p>
                        <?else:?>
                            <p>(<?=(int)$badge['progress']?>%)</p>
                        <?endif;?>
                    </a>
                    <div class="tile-progress" style="margin-bottom: 0">
                        <div class="tile-progressbar">
                            <span data-fill="<?=number_format($badge['progress'], 2); ?>%" style="width: <?=number_format($badge['progress'], 2); ?>%;"></span>
                        </div>
                        <div style="height: 40px" >
                            <?
                                $divWidth = 350;
                                $maximumWidth = 40;
                                $width = $maximumWidth; 
                                if (count($badge['BadgeLog']) * $maximumWidth > $divWidth) {
                                    $width = floor($divWidth / count($badge['BadgeLog']));
                                }
                            ?>
                            <?foreach ($badge['BadgeLog'] as $badgeLog): ?>
                                <? 
                                    $player = $players[$badgeLog['player_id']]['Player']['email']; 
                                ?>
                                <img style="margin-left: -1px; margin-right: -1px" src="<?= $this->Gravatar->get($player, $width) ?>" width="<?= $width?>" alt=""/>
                            <?endforeach;?>
                        </div>
                        <div class="tile-footer" style="height: 100px; text-align: center">
                            <div class="row">
                                <?if (empty($activitiesProgress)): ?>
                                    <i style="color: green" class="glyphicon glyphicon-ok"></i>&nbsp;&nbsp;<span>No activities required.</span>
                                <?endif;?>
                                <?foreach($activitiesProgress as $activityProgress): ?>
                                    <?if($badge['claimed'] || $activityProgress['BadgeActivityProgress']['progress'] == 100):?>
                                        <i style="color: green" class="glyphicon glyphicon-ok"></i>&nbsp;&nbsp;
                                    <?else:?>
                                        <i style="color: red" class="glyphicon glyphicon-remove"></i>&nbsp;&nbsp;
                                    <?endif;?>
                                    <span title="<?= $activityProgress['Activity']['name']?>">
                                        <span title="Required"><?= $activityProgress['BadgeActivityProgress']['coins_required']?>x </span>
                                        <?= h($activityProgress['Activity']['name']); ?>
                                        <?if (!$badge['claimed']): ?>
                                            <span title="You have">
                                                (<?= $activityProgress['BadgeActivityProgress']['coins_obtained']?>)
                                            </span>
                                        <?endif;?>
                                    </span>
                                    <br/>
                                <?endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?endforeach;?>
</div>
<?if ($isScrumMaster): ?>
    <div class="btn-group">
        <a href="<? echo $this->Html->url('/domains/edit/' . $domain['Domain']['id']); ?>" class="btn btn-primary btn-lg">
            <i class="glyphicon glyphicon-edit"></i> Edit
        </a>
    </div>
<?endif;?>