<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Inactivate Domain</strong></div>
			</div>
			<div class="panel-body">
				<div class="alert alert-danger">
					<p>Are you really sure you want to inactivate the <strong><?=h($domain['Domain']['name'])?></strong> domain?</p>
					<p>Players will not be able to see this domain anymore.</p>
					<br/>
					<p><strong>Important:</strong> This operation CANNOT be undone.</p>
					<p>All Activities from this domain are going to be <strong>inactivated</strong>.</p>
					<p>All Badges from this domain are going to be <strong>inactivated</strong>.</p>
					<br/>
					<a href="<?= $this->Html->url('/domains/inactivate/' . $domain['Domain']['id'] . '/true')?>" class="btn btn-danger">Inactivate this Domain, all its Badges and Activities</a>
					<a href="<?= $this->Html->url('/domains/')?>" class="btn btn-default">Cancel, I have changed my mind</a>
				</div>
			</div>
		</div>
	</div>
</div>