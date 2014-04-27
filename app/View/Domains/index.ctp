<h3>Domains</h3>
<br/>
<div class="row">
	<? foreach ($domains as $domain) : ?>
		<div class="col-sm-3">
			<div class="tile-title" style="background-color: <? echo $domain['Domain']['color']?>">
				<div class="icon">
					<a href="<? echo $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>"><i class="<? echo h($domain['Domain']['icon']); ?>"></i></a>
				</div>
				<div class="title">
					<a href="<? echo $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>"><h3><? echo h($domain['Domain']['name']); ?></h3></a>
					<p><? echo h($domain['Domain']['description']); ?></p>
				</div>
			</div>
		</div>
<? endforeach; ?>
</div>
