<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Create Badge</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Badge'); ?>
				<?= $this->Bootstrap->input('domain_id', array('class' => 'form-control')); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Next</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	