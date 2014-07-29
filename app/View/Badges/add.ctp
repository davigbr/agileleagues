<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Create Badge - <?= h($domain['Domain']['name'])?></strong></div>
			</div>
			<div class="panel-body">
				<? echo $this->Bootstrap->create('Badge'); ?>
				<? require_once 'form.ctp'; ?>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>

<?= $this->element('icons'); ?>