<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Domains</h1>
        </div>
    </div>
    <div class="panel-body">
		<p>Domains are areas of knowledge or groups of technical skills that you want to boost. </p>
		<?if ($isGameMaster): ?>
			<p>Use them as a way of grouping activities into categories.</p>
		    <a class="btn btn-md btn-success" href="<?= $this->Html->url('/domains/add')?>"><i class="glyphicon glyphicon-plus"></i> Create New Domain</a>
		<? endif; ?>
    </div>
    <?if (empty($domains)): ?>
	    <div class="panel-body">
		    <p>No domains found :( </p>
	    </div>
    <?endif;?>
</div>
<div class="row">
	<? foreach ($domains as $domain) : ?>
		<div class="col-lg-3 col-sm-6">
			<div class="tile-title" style="min-height: 220px; background-color: <?= $domain['Domain']['color']?>">
				<div class="icon">
					<a href="<?= $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>"><i class="<?= h($domain['Domain']['icon']); ?>"></i></a>
				</div>
				<div class="title">
					<a href="<?= $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>"><h3><?= h($domain['Domain']['name']); ?></h3></a>
					<p><?= h($domain['Domain']['description']); ?></p>
					<?if ($isGameMaster): ?>
						<div class="btn-group">
							<a href="<?= $this->Html->url('/badges/add/' . $domain['Domain']['id']); ?>" title="Create a new Badge for this Domain" class="btn btn-success btn-md">
								<i class="glyphicon glyphicon-plus"></i>
							</a>
							<a href="<?= $this->Html->url('/domains/edit/' . $domain['Domain']['id']); ?>" title="Edit Domain" class="btn btn-primary btn-md">
								<i class="glyphicon glyphicon-edit"></i> 
							</a>
							<a href="<?= $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>" title="View Domain Badges" class="btn btn-primary btn-md">
								<i class="entypo-trophy"></i> 
							</a>
							<?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/domains/inactivate/' . $domain['Domain']['id'], $options = array('escape' => false, 'class'=> 'btn btn-danger btn-md'), __('Are you sure you want to inactivate this domain?')) ?>
						</div>
						<p></p>
					<?endif;?>
				</div>
			</div>
		</div>
<? endforeach; ?>
</div>
