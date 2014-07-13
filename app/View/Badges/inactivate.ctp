<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Inactivate Badge</strong></div>
			</div>
			<div class="panel-body">
				<div class="alert alert-danger">
					<p>Are you really sure you want to inactivate the <strong><?=h($badge['Badge']['name'])?></strong> badge?</p>
					<p>Players who have claimed this badge will not be able to see it anymore.</p>
					<br/>
					<p><strong>Important:</strong> This operation CANNOT be undone.</p>
					<br/>
					<a href="<?= $this->Html->url('/badges/inactivate/' . $badge['Badge']['id'] . '/true')?>" class="btn btn-danger">Inactivate</a>
					<a href="<?= $this->Html->url('/badges/')?>" class="btn btn-default">Cancel, I have changed my mind</a>
				</div>
			</div>
		</div>
	</div>
</div>