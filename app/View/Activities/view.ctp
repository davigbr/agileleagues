<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>View Activity</strong></div>
			</div>
			<div class="panel-body">
				<p><strong>Name:</strong> <?= h($activity['Activity']['name']) ?></p>
				<p><strong>Description:</strong> <?= h($activity['Activity']['description']) ?></p>
				<p><strong>Details:</strong> <?= h($activity['Activity']['details']) ?></p>
				<p><strong>Restrictions:</strong> <?= h($activity['Activity']['restrictions']) ?></p>
				<p><strong>XP:</strong> <?= number_format($activity['Activity']['xp']) ?></p>
				<p><strong>Acceptance Votes:</strong> <?= number_format($activity['Activity']['acceptance_votes']) ?></p>
				<p><strong>Rejection Votes:</strong> <?= number_format($activity['Activity']['rejection_votes']) ?></p>
				<p><strong>Daily Limit:</strong> <?= number_format($activity['Activity']['daily_limit']) ?></p>
			</div>
		</div>
	</div>
</div>


	