<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1><?=__('Teams')?></h1>
        </div>
    </div>
    <div class="panel-body">
    	<p><?=__('Teams are group of players that work together. Agile Leagues encourages collaboration between team members and competition across different teams.')?></p>
    </div>
	<?if (empty($teams)): ?>
	    <div class="panel-body">
			<p>No teams :(</p>
		    <a class="btn btn-md btn-success" href="<?= $this->Html->url('/teams/add')?>"><i class="glyphicon glyphicon-plus"></i> Create New Team</a>
    	</div>
	<?else:?>
	    <div class="panel-body with-table">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th><?=__('Name')?></th>
					<th><?=__('Players')?></th>
					<th>
						<a href="<?= $this->Html->url('/teams/add'); ?>" class="btn btn-large btn-success"><i class="glyphicon glyphicon-plus"></i> Create </a>
					</th>
				</tr>
				<? foreach ($teams as $team) : ?>
					<tr>
						<td><?= h($team['Team']['name'])?></td>
						<td>
							<? if (empty($team['Players'])): ?>
								<a class="btn btn-md btn-info" href="<?= $this->Html->url('/players/invite')?>">Invite some players!</a>
							<?endif;?>
							<?foreach ($team['Players'] as $dev): ?>
								<?= h($dev['name']) ?>; 
							<?endforeach;?>
						</td>
						<td>
							<a href="<?= $this->Html->url('/teams/edit/' . $team['Team']['id']); ?>" class="btn btn-primary btn-sm">
								<i class="glyphicon glyphicon-edit"></i>
							</a>
							<?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/teams/delete/' . $team['Team']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to delete this team?')) ?>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
	    </div>
	<?endif;?>
</div>
