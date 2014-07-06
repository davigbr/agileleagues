<style type="text/css">
    .table td {
        height: 47px;
    }    
</style>
<?
    $tables = array(
        //'Ever' => $activityLeaderboardsEver,
        'This Week' => $activityLeaderboardsThisWeek,
        'This Month' => $activityLeaderboardsThisMonth,
        'Last Week' => $activityLeaderboardsLastWeek,
        'Last Month' => $activityLeaderboardsLastMonth,
    );
?>

<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Activity Leaderboards</h1>
        </div>
    </div>
</div>
<div class="row">
    <?foreach ($tables as $title => $rows):?>
        <div class="col-lg-3 col-sm-6">
             <div class="panel panel-primary panel-table">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h3><?= $title?></h3>
                        <span>Activities accepted ranking.</span>    
                    </div>
                </div>
                <table class="table table-responsive">
                    <tr>
                        <th title="Position">P</th>
                        <th><strong>Player</strong></th>
                        <th title="Activities Accepted" style="text-align: right"><strong>A.C.</strong></th>
                    </tr>
                    <?$i=0?>
                    <?if (empty($rows)):?>
                        <tr><td colspan="2">No data yet.</td></tr>
                    <?endif;?>
                    <?foreach ($rows as $row): ?>
                        <tr class="<?= $i == 0?'warning':''?>">
                            <td><?= number_format($i + 1) ?>º</td>
                            <td>
                                <img 
                                    class="img-square"
                                    title="<?= $row['Player']['name']?>" 
                                    alt="<?= $row['Player']['name']?>" 
                                    src="<?= $this->Gravatar->get($row['Player']['email'], 30); ?>"/>
                                &nbsp;
                                <?= $row['Leaderboards']['player_name']?> 
                                <?if ($i == 0): ?>
                                    <i class="entypo-trophy"></i>
                                <?endif;?>
                            </td>
                            <td style="text-align: right"><?= number_format($row['Leaderboards']['count'])?></td>
                        </tr>
                        <?$i++?>
                    <?endforeach;?>
                </table>
            </div>
        </div>
    <?endforeach;?>
</div>