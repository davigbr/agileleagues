<div class="well">
    <h1>Activities Dashboard</h1>
</div>
<div class="row">
    <?foreach ($domains as $domainId => $domain): ?>
        <div class="col-lg-3 col-sm-6">
            <div class="tile-progress tile-red" style="height: 260px; background-color: <? echo $domain['Domain']['color']?>">
                <div class="tile-header">
                    <a href="<? echo $this->Html->url('/dashboards/activities/' . $domainId); ?>">
                        <h3><? echo $domain['Domain']['name'] ?></h3>
                        <span><? echo $domain['Domain']['description']?></span>
                    </a>
                </div>
                <?
                $count = isset($activitiesCount[$domainId])? $activitiesCount[$domainId] : 0;
                $different = isset($differentActivitiesCompleted[$domainId])? $differentActivitiesCompleted[$domainId] : 0;

                $percentual = @((int)($different / $count*1000))/10;
                ?>
                <div class="tile-progressbar">
                    <span data-fill="<? echo $percentual?>%" style="width: <? echo $percentual?>%;"></span>
                </div>
                <div class="tile-footer">
                    <a href="<? echo $this->Html->url('/dashboards/activities/' . $domainId); ?>">
                        <h4><span class="pct-counter"><? echo $percentual?></span>% completed</h4>
                        <h1 style="color: white"><? echo $different?> / <? echo $count ?></h1>
                    </a>
                    <span>How many different activities from this domain you have completed so far.</span>
                </div>
            </div>
        </div>
    <?endforeach;?>
</div>
<hr/>
<div class="row">
    <div class="col-lg-3">
        <div style="height: 170px" class="tile-stats tile-white">
            <div class="icon"><i class="entypo-flag"></i></div>
            <div class="num"><? echo $activitiesCompletedCount?></div>
            <h3>Activities You Logged</h3>
            <p>How many activities you have logged so far. Only reviewed activities count.</p>
        </div>
        <div style="height: 170px" class="tile-stats tile-white">
            <div class="icon"><i class="entypo-flag"></i></div>
            <div class="num"><? echo number_format($averageActivitiesLogged, 2) ?></div>
            <h3>Average Logged</h3>
            <p>Average number of activities logged by each player. Only reviewed activities count.</p>
        </div>
        <div style="height: 170px" class="tile-stats tile-white">
            <div class="icon"><i class="entypo-flag"></i></div>
            <div class="num"><? echo $activitiesLogged?></div>
            <h3>All Activities Logged</h3>
            <p>How many activities have been logged so far. Only reviewed activities count.</p>
        </div>
        <div style="height: 170px" class="tile-stats tile-white">
            <div class="icon"><i class="entypo-flag"></i></div>
            <div class="num"><? echo $totalActivitiesCount?></div>
            <h3>Different Activities</h3>
            <p>Total amount of different activities from all domains. </p>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="panel panel-primary panel-table">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Activities <strong>Never</strong> Reported</h3>
                </div>
            </div>
            <table class="table table-responsive">
                <?foreach ($neverReportedActivities as $activity): ?>
                    <tr>
                        <td><? echo h($activity['Activity']['name']); ?></td>
                        <td style="text-align: right">
                            <a class="btn btn-xs btn-info" href="<? echo $this->Html->url('/activities/report/' . $activity['Activity']['id']); ?>">Be the first!</a>
                        </td>
                    </tr>
                <?endforeach;?>
            </table>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="panel panel-primary panel-table">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Activities <strong>Least</strong> Reported</h3>
                </div>
            </div>
            <table class="table table-responsive">
                <?foreach ($leastReportedActivities as $activity): ?>
                    <tr>
                        <td><? echo h($activity['Activity']['name']); ?></td>
                        <td style="text-align: right">
                            <a href="#" onclick="return false" class="btn btn-xs btn-default"><? echo number_format($activity['Activity']['reported']); ?></a>
                        </td>
                    </tr>
                <?endforeach;?>
            </table>
        </div>
    </div>
   <div class="col-lg-3">
        <div class="panel panel-primary panel-table">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Activities <strong>Most</strong> Reported</h3>
                </div>
            </div>
            <table class="table table-responsive">
                <?foreach ($mostReportedActivities as $activity): ?>
                    <tr>
                        <td><? echo h($activity['Activity']['name']); ?></td>
                        <td style="text-align: right">
                            <a href="#" onclick="return false" class="btn btn-xs btn-default"><? echo number_format($activity['Activity']['reported']); ?></a>
                        </td>
                    </tr>
                <?endforeach;?>
            </table>
        </div>
    </div></div>

<!-- <div class="row">
    <div class="col-md-12">
        <div class="tile-progress tile-green">
            <div class="tile-header" style="padding-bottom: 20px">
                <h3>Overall Progresss</h3>
            </div>
            <?
                $percentual = ((int)($totalDifferentActivitiesCompleted / $totalActivitiesCount*1000))/10;
            ?>
            <div class="tile-progressbar">
                <span data-fill="<? echo $percentual?>%" style="width: <? echo $percentual?>%;"></span>
            </div>
            <div class="tile-footer">
                <h4><span class="pct-counter"><? echo $percentual?></span>% completed</h4>
                <h1 style="color: white"><? echo $totalDifferentActivitiesCompleted?> / <? echo $totalActivitiesCount?></h1>
            </div>
        </div>
    </div>
</div> -->