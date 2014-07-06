<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Teams</h1>
        </div>
    </div>
    <div class="panel-body with-table">
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>Name</th>
				<th>Game Master</th>
				<th>Players</th>
				<th>
					<a href="<? echo $this->Html->url('/teams/add'); ?>" class="btn btn-large btn-success"><i class="glyphicon glyphicon-plus"></i> Create </a>
				</th>
			</tr>
			<?if (empty($teams)): ?>
				<tr>
					<td colspan="4">
						<p style="text-align: center">No teams :(</p>
					</td>
				</tr>
			<?else:?>
				<? foreach ($teams as $team) : ?>
					<tr>
						<td><?= h($team['Team']['name'])?></td>
						<td><?= h($team['GameMaster']['name'])?></td>
							<?foreach ($team['Players'] as $dev): ?>
								<?= h($dev['name']) ?>; 
							<?endforeach;?>
						</td>
						<td>
							<a href="<? echo $this->Html->url('/teams/edit/' . $team['Team']['id']); ?>" class="btn btn-primary btn-sm">
								<i class="glyphicon glyphicon-edit"></i>
							</a>
							<? echo $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/teams/delete/' . $team['Team']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to delete this team?')) ?>
						</td>
					</tr>
				<? endforeach; ?>
			<?endif;?>
		</table>
    </div>
</div>
