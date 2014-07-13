<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>My Account</strong></div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-3" style="text-align: center">
						<img src="<?= $this->Gravatar->get($loggedPlayer['Player']['email'], 200) ?>" alt="" class="img-thumbnail"/>
						<br/>
						<br/>
						<p>
							<strong>
							<?= $loggedPlayer['Player']['name']?>
							<? if ($isPlayer): ?>
								- <?= $loggedPlayer['Player']['title']?>
							<? endif;?>
							</strong>
						</p>
						<p>
							<small><a target="_blank" href="http://gravatar.com">Change your picture at gravatar.com</a></small>
						</p>
					</div>
					<div class="col-sm-9">
						<?= $this->Bootstrap->create('Player'); ?>
						<?= $this->Bootstrap->hidden('id'); ?>
						<?= $this->Bootstrap->input('name', array('autocomplete' => 'off', 'type' => 'text', 'class' => 'form-control')); ?>
						<?= $this->Bootstrap->input('email', array('autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
						<?= $this->Bootstrap->input('password', array('label' => 'Change Password', 'autocomplete' => 'off', 'type' => 'password', 'class' => 'form-control')); ?>
						<?= $this->Bootstrap->input('repeat_password', array('type' => 'password', 'class' => 'form-control')); ?>
						<?= $this->Bootstrap->input('timezone', array('type' => 'select', 'options' => array_combine(DateTimeZone::listIdentifiers(), DateTimeZone::listIdentifiers()), 'class' => 'form-control'))?>
						<button type="submit" class="btn btn-success">Save</button>
					</div>
				</div>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	