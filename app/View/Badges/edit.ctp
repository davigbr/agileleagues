<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Edit Badge</strong></div>
			</div>
			<div class="panel-body">
				<? echo $this->Bootstrap->create('Badge'); ?>
				<? echo $this->Bootstrap->hidden('id'); ?>
				<? require_once 'form.ctp'; ?>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>

<?= $this->element('icons'); ?>