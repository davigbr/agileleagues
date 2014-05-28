<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>New Domain</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Domain'); ?>
				<? require_once 'form.ctp'; ?>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>

<? require_once 'icons.ctp'; ?>
