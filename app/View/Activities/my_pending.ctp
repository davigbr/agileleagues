<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>My Pending Activities</h1>
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
					<th>Description</th>
					<th>Actions</th>
				</tr>
				<? foreach ($logs as $log) : ?>
					<tr>
						<td style="text-align: center; width: 35px; background-color: <?= $log['Domain']['color']?>; color: white">
							<i class="<?= $log['Domain']['icon']?>"></i>
						</td>
						<td><?= $log['Activity']['name']?></td>
						<td><?= $this->element('tag', array('tags' => $log['Tags'])); ?></td>
						<td><?= $log['Log']['created']?></td>
						<td><?= $log['Log']['acquired']?></td>
						<td><?= h($log['Log']['description']); ?></td>
						<td>
							<?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/logs/delete/' . $log['Log']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to delete this activity log?')) ?>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<? endif; ?>
</div>