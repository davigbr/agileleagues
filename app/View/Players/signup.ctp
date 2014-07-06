<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<?= $this->Bootstrap->create('Player'); ?>
				<div class="panel-heading">
					<div class="panel-title"><strong>Sign Up</strong></div>
				</div>
				<div class="panel-body">
					<?= $this->Bootstrap->input('name', array('label' => 'Full Name', 'placeholder' => 'Enter your fir and last name.', 'autocomplete' => 'off', 'type' => 'text', 'class' => 'form-control')); ?>
					<?= $this->Bootstrap->input('email', array('placeholder' => 'What is your email address?', 'autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
					<?= $this->Bootstrap->input('password', array('autocomplete' => 'off', 'type' => 'password', 'class' => 'form-control')); ?>
					<?= $this->Bootstrap->input('player_type_id', array('class' => 'form-control', 'readonly' => 'readonly')); ?>
					<div class="alert alert-warning">
						You are going to sign up as a <strong>Game Master</strong>. If you want to join an existing team, you should ask a Game Master to invite you. 
						Also remember that you can sign up as only one role per email address. 
					</div>
					<div class="alert alert-info">
						The <strong>Game Master</strong> is responsible for managing the gamification program.
						He/she can create domains, activities, badges, manage teams and invite Players.
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-lg btn-success">Create my account</button>
				</div>
			<?= $this->Bootstrap->end(); ?>
		</div>
	</div>
</div>


	