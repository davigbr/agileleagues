<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Edit Badge</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Badge'); ?>
				<div class="alert alert-info">
					Please only make slight changes to badges. Sometimes it is better to inactivate and create another one.
				</div>
				<div class="alert alert-warning">
					It may take a while to edit a bagde because all player progress must be recalculated.
				</div>
				<?= $this->Bootstrap->hidden('id'); ?>
				<? require_once 'form.ctp'; ?>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>

<?= $this->element('icons'); ?>