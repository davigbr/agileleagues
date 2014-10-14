<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>New Activity</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Activity'); ?>
				<?= $this->Bootstrap->input('domain_id', array('class' => 'form-control')); ?>
				<? require_once 'form.ctp' ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Save</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	