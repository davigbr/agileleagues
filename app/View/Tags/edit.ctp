<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Edit Tag</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Tag'); ?>
				<?= $this->Bootstrap->hidden('id'); ?>
				<? require_once 'form.ctp' ?>
			</div>
		</div>
	</div>
</div>


	