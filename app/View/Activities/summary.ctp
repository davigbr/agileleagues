<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Activities Summary</h1>
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
					<th>Completed</th>
					<th>Name</th>
					<th>Description</th>
				</tr>
				<? foreach ($logs as $log) : ?>
					<tr>
						<td style="text-align: center; width: 35px; background-color: <?= $log['PlayerActivitySummary']['domain_color']?>; color: white">
							<i class="<?= $log['Domain']['icon']?>"></i>
						</td>
						<td style="text-align: right">
							<?= number_format($log['PlayerActivitySummary']['count']);?>x
						</td>
						<td><?= $log['PlayerActivitySummary']['activity_name']?></td>
						<td><?= $log['PlayerActivitySummary']['activity_description']?></td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<? endif; ?>
</div>