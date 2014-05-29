<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Invite Player</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Player'); ?>
				<?= $this->Bootstrap->input('name', array('autocomplete' => 'off', 'type' => 'text', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('email', array('autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('player_type_id', array('class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('team_id', array('class' => 'form-control')); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Invite!</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	