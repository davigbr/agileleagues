<style type="text/css">
	span.form-control {
		display: inline;
		padding: 4px;
	}
</style>
<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>My Activities</h1>
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
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th style="text-align: center" title="Domain">D</th>
					<th>Name</th>
					<th>Tags</th>
					<th>Logged</th>
					<th>Acquired</th>
					<th>Paired With</th>	
					<th>Event</th>
					<th>Status</th>
					<th title="Acceptance Votes">A. Votes</th>
					<th title="Rejection Votes">R. Votes</th>
					<th>Description</th>
				</tr>
				<? foreach ($logs as $log) : ?>
					<tr>
						<td style="text-align: center; width: 35px; background-color: <?= $log['Domain']['color']?>; color: white">
							<i class="<?= $log['Domain']['icon']?>"></i>
						</td>
						<td><?= $log['Activity']['name']?></td>
						<td><?= $this->element('tag', array('tags' => $log['Tags'])); ?></td>
						<td><?= $this->Format->dateTime($log['Log']['created'])?></td>
						<td><?= $this->Format->dateTime($log['Log']['acquired'])?></td>
						<td><?= $log['PairedPlayer']['name']?></td>
						<td><?= h($log['Event']['name']); ?></td>
						<td>
							<? if ($log['Log']['accepted']):?>
								<span class="badge badge-success">Accepted</span>
							<? elseif ($log['Log']['rejected']) : ?>
								<span class="badge badge-danger">Rejected</span>
							<? else : ?>
								<span class="badge">Pending</span>
							<?endif;?>
						</td>
						<td><?= $log['Log']['acceptance_votes']?>/<?= $log['Activity']['acceptance_votes']?></td>
						<td><?= $log['Log']['rejection_votes']?>/<?= $log['Activity']['rejection_votes']?></td>
						<td><?= h($log['Log']['description']); ?></td>
					</tr>
					<? if($log['LogVote']): ?>
						<tr>
							<td></td>
							<td colspan="5">
								<? foreach ($log['LogVote'] as $vote) : ?>
									<?if ($vote['vote'] > 0): ?>
										<p>
											<span class="badge badge-success">+1</span>
											<strong><?= h($vote['Player']['name'])?></strong> accepted your activity
											in <?= $this->Format->dateTime($vote['creation'])?>
											and commented: <em><?= h($vote['comment'])?></em>
										</p>
									<?else:?>
										<p>
											<span class="badge badge-danger">-1</span>
											<strong><?= h($vote['Player']['name'])?></strong> rejected your activity
											in <?= $this->Format->dateTime($vote['creation'])?>
											and commented: <em><?= h($vote['comment'])?></em>
										</p>
									<?endif;?>
								<? endforeach;?>
							</td>
						</tr>
					<? endif; ?>
				<? endforeach; ?>
			</table>
		</div>
		<div class="panel-body">
			<ul class="pagination">
				<?= $this->Paginator->numbers(array('currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'active', 'separator' => false))?>
			</ul>
		</div>

	<? endif; ?>
</div>