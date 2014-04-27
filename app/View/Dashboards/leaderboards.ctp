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

<div class="row">
    <div class="col-sm-12">
        <div class="well">
            <h1>Activity Leaderboards</h1>
        </div>
    </div>
    <?foreach ($tables as $title => $rows):?>
        <div class="col-lg-3 col-sm-6">
             <div class="panel panel-primary panel-table">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h3><? echo $title?></h3>
                        <span>Activity coins acquisition ranking.</span>    
                    </div>
                </div>
                <table class="table table-responsive">
                    <tr>
                        <th><strong>Player</strong></th>
                        <th style="text-align: right"><strong>Activity Coins</strong></th>
                    </tr>
                    <?$i=0?>
                    <?if (empty($rows)):?>
                        <tr><td colspan="2">No data yet.</td></tr>
                    <?endif;?>
                    <?foreach ($rows as $row): ?>
                        <tr class="<? echo $i == 0?'warning':''?>">
                            <td>
                                <img 
                                    class="img-square"
                                    title="<? echo $row['Player']['name']?>" 
                                    alt="<? echo $row['Player']['name']?>" 
                                    src="<? echo $this->Gravatar->get($row['Player']['email'], 30); ?>"/>
                                &nbsp;
                                <? echo $row['Leaderboards']['player_name']?> 
                                <?if ($i == 0): ?>
                                    <i class="entypo-trophy"></i>
                                <?endif;?>
                            </td>
                            <td style="text-align: right"><? echo $row['Leaderboards']['count']?></td>
                        </tr>
                        <?$i++?>
                    <?endforeach;?>
                </table>
            </div>
        </div>
    <?endforeach;?>
</div>