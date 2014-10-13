<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>XP History</h1>
        </div>
    </div>
    <div class="panel-body">
    	<p><?= __('The last 1000 XP log records you earned will be shown here.')?></p>
    </div>
    <?if (empty($logs)): ?>
	    <div class="panel-body">
		    <p>No XP found :( </p>
	    </div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-striped table-bordered table-condensed">
                <tr>
                    <th>Date</th>
                    <th>XP</th>
                    <th>Activity Reported</th>
                    <th>Activity Reviewed</th>
                </tr>
                <? foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $this->Format->dateTime($log['XpLog']['created']) ?></td>
                        <td><?= number_format($log['XpLog']['xp'])?>XP</td>
                        <td><?= h(isset($log['LogReported']['Activity'])? $log['LogReported']['Activity']['name'] : '') ?></td>
                        <td><?= h(isset($log['LogReviewed']['Activity'])? $log['LogReviewed']['Activity']['name'] : '') ?></td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
    <? endif;?>
</div>    
