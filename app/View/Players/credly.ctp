<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Credly Account</strong></div>
			</div>
			<div class="panel-body">
				<p>For more information about Credly, please access their website: <a target="_blank" href="https://credly.com/">https://credly.com/</a></p>
				<? if ($isGameMaster): ?>
					<div class="alert alert-warning">
						<p>Please type your Credly email and password below. This is necessary because we need to get your access token in order to give credits (issue badges) using your Credly account.</p>
						<p>We will <strong>NOT</strong> store your password.</p>
					</div>

					<?= $this->Bootstrap->create('Credly'); ?>
					<?= $this->Bootstrap->hidden('id'); ?>
					<?= $this->Bootstrap->input('credly_email', array('autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
					<?= $this->Bootstrap->input('credly_password', array('autocomplete' => 'off', 'type' => 'password', 'class' => 'form-control')); ?>
					
					<p>
						<input type="checkbox" name="data[Credly][accept]" id="accept" /> 
						<label for="accept">
							You authorize Agile Leagues to give credits (issue badges) using your Credly account.
						</label>
					</p>
				<? else: ?>
					<div class="alert alert-warning">
						<p>Please type your Credly email below.</p>
					</div>

					<?= $this->Bootstrap->create('Credly'); ?>
					<?= $this->Bootstrap->hidden('id'); ?>
					<?= $this->Bootstrap->input('credly_email', array('autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
					
				<?endif;?>
				<button type="submit" class="btn btn-success">Setup Account</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	