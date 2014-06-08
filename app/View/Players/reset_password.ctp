<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Reset Password</strong></div>
			</div>
			<div class="panel-body">
				<p class="alert alert-info">Please, type a password between 6 and 20 characters.</p>

				<?= $this->Bootstrap->create('Player'); ?>
				<?= $this->Bootstrap->hidden('id'); ?>
				<?= $this->Bootstrap->input('password', array('autocomplete' => 'off', 'type' => 'password', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('repeat_password', array('type' => 'password', 'class' => 'form-control')); ?>
				<button type="submit" class="btn btn-lg btn-success">Save</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	