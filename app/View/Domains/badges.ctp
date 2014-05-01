<h3><? echo $domain['Domain']['name']?> Badges</h3>
<br/>
<div class="row">
    <?foreach ($badges as $badgeId => $badge): ?>
        <?
            $activitiesProgress = $badgeActivitiesProgress[$badgeId];
        ?>
        <div class="col-lg-3 col-md-6">
            <div class="tile-title" style="height: 330px; <? echo $badge['claimed']? 'background-color: ' . $badge['Domain']['color']:''?>">           
                <div class="icon">
                    <a href="<? echo $this->Html->url('/badges/view/' . $badgeId); ?>">
                        <?if($badge['claimed']):?>
                            <i class="<? echo $badge['Badge']['icon']? $badge['Badge']['icon'] : 'fa fa-question'?>"></i>
                        <?else:?>
                            <i class="glyphicon glyphicon-lock"></i>
                        <?endif;?>
                    </a>
                </div>
                <div class="title">
                    <a href="<? echo $this->Html->url('/badges/view/' . $badgeId); ?>">
                        <h3>
                            <? echo h($badge['Badge']['name']); ?>&nbsp;
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
                                    <span title="<? echo $activityProgress['Activity']['name']?>">
                                        <span title="Required"><? echo $activityProgress['BadgeActivityProgress']['coins_required']?>x </span>
                                        <? echo h($activityProgress['Activity']['name']); ?>
                                        <?if (!$badge['claimed']): ?>
                                            <span title="You have">
                                                (<? echo $activityProgress['BadgeActivityProgress']['coins_obtained']?>)
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