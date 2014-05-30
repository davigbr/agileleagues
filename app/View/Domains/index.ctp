<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Domains</h1>
        </div>
    </div>
    <?if (empty($domains)): ?>
	    <div class="panel-body">
		    <p>No domains found :( </p>
		    <a class="btn btn-md btn-success" href="<?= $this->Html->url('/domains/add')?>"><i class="glyphicon glyphicon-plus"></i> Create New Domain</a>
	    </div>
    <?endif;?>
</div>
<div class="row">

	<? foreach ($domains as $domain) : ?>
		<div class="col-sm-3">
			<div class="tile-title" style="height: 220px; background-color: <?= $domain['Domain']['color']?>">
				<div class="icon">
					<a href="<?= $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>"><i class="<?= h($domain['Domain']['icon']); ?>"></i></a>
				</div>
				<div class="title">
					<a href="<?= $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>"><h3><?= h($domain['Domain']['name']); ?></h3></a>
					<p><?= h($domain['Domain']['description']); ?></p>
				</div>
			</div>
		</div>
<? endforeach; ?>
</div>
