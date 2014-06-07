<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Report a Bug</strong></div>
			</div>
			<div class="panel-body">
				<p>Please fill the form below with the maximum ammount of detail you can. We will reply you within 48 hours.</p>
				<?= $this->Bootstrap->create('Bug'); ?>
				<?= $this->Bootstrap->input('where', array('class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('description', array('type' => 'textarea', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('comments', array('type' => 'textarea', 'class' => 'form-control')); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Report</button>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	