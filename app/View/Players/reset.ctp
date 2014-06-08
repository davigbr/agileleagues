<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Reset Password / Resend Verification Message</strong></div>
			</div>
			<div class="panel-body">
				<p class="alert alert-info">Please, type your e-mail address</p>

				<?= $this->Bootstrap->create('Player'); ?>
				<?= $this->Bootstrap->input('email', array('autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
				<button type="submit" class="btn btn-lg btn-success">Reset / Resend</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	