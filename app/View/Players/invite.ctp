<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Invite Player</strong></div>
			</div>
			<div class="panel-body">
				<?if (empty($teams)) : ?>
					<div class="alert alert-danger">
						<p>Please create a <a href="<?= $this->Html->url('/teams/add')?>">Team</a> before inviting players.</p>
					</div>
				<?endif;?>
				<p><?= __('Here you can invite new players to join one of your teams. ')?></p>
				<p><?= __('You can reassign players to other teams in the Player List section.')?></p>
				<br/>
				<?= $this->Bootstrap->create('Player'); ?>
				<?= $this->Bootstrap->input('name', array('autocomplete' => 'off', 'type' => 'text', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('email', array('autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('team_id', array('class' => 'form-control')); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Invite!</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	