<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>My Activity Coins</h1>
            <br/>
            <p></p>
        </div>
    </div>
    <?if (empty($logs)): ?>
	    <div class="panel-body">
		    <p>No data :( </p>
	    </div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-bordered table-striped table-condensed">
				<tr>
					<th style="text-align: center" title="Domain">D</th>
					<th>Coins</th>
					<th>Spent</th>
					<th>Remaining</th>
					<th>Name</th>
					<th>Description</th>
				</tr>
				<? foreach ($logs as $log) : ?>
					<tr>
						<td style="text-align: center; width: 35px; background-color: <? echo $log['PlayerActivityCoins']['domain_color']?>; color: white">
							<i class="<? echo $log['Domain']['icon']?>"></i>
						</td>
						<td style="text-align: right">
							<? echo (int)$log['PlayerActivityCoins']['coins'];?>
						</td>
						<td style="text-align: right">
							<? echo (int)$log['PlayerActivityCoins']['spent'];?>
						</td>
						<td style="text-align: right">
							<? echo (int)$log['PlayerActivityCoins']['remaining'];?>
						</td>
						<td><? echo $log['PlayerActivityCoins']['activity_name']?></td>
						<td><? echo $log['PlayerActivityCoins']['activity_description']?></td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<? endif; ?>
</div>