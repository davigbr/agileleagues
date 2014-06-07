<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Request a Feature</strong></div>
			</div>
			<div class="panel-body">
				<p>Do you want to request a feature? Please fill the form below. We will reply you within 48 hours.</p>
				<?= $this->Bootstrap->create('Feature'); ?>
				<?= $this->Bootstrap->input('description', array('type' => 'textarea', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('comments', array('type' => 'textarea', 'class' => 'form-control')); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Request</button>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	