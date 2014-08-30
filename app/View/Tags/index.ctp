<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Tags</h1>
        </div>
    </div>
    <div class="panel-body">
    	<p><?=__('Tags are modifiers that the players can choose while reporting activities. ')?></p>
	    <?if ($isGameMaster): ?>
			<a href="<?= $this->Html->url('/tags/add'); ?>" class="btn btn-md btn-success"><i class="glyphicon glyphicon-plus"></i> Create New Tag </a>
		<?endif;?>
    </div>
    <?if (empty($tags)): ?>
	    <div class="panel-body">
		    <p>No tags found :( </p>
	    </div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th>Name</th>
					<th>Description</th>
					<th>Symbol</th>
					<th>Bonus</th>
					<?if ($isGameMaster): ?>
						<th>Actions</th>
					<?endif;?>
				</tr>
				<?if (empty($tags)): ?>
					<tr>
						<td colspan="6">
							<p style="text-align: center">No tags :(</p>
						</td>
					</tr>
				<?else:?>
					<? foreach ($tags as $tag) : ?>
						<tr>
							<td>
								<?if ($tag['Tag']['new']): ?>
									<span class="badge badge-danger">NEW</span>
								<?endif;?>
								<?= h($tag['Tag']['name']) ?>
							</td>
							<td><?= h($tag['Tag']['description']) ?></td>
							<td>
								<?= $this->element('tag', array('tag' => $tag)); ?>
							</td>
							<td>
								<? if ($tag['Tag']['bonus_type'] === '+'): ?>
									+<?= number_format($tag['Tag']['bonus_value']) ?> XP
								<? else : ?>
									<?= number_format(100 + $tag['Tag']['bonus_value']) ?>% XP
								<? endif; ?>
							</td>
							<?if ($isGameMaster): ?>
								<td>
									<div class="btn-group">
										<a title="<?=__('Edit')?>" href="<?= $this->Html->url('/tags/edit/' . $tag['Tag']['id']); ?>" class="btn btn-primary btn-sm">
											<i class="glyphicon glyphicon-edit"></i>
										</a>
										<?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', '/tags/inactivate/' . $tag['Tag']['id'], $options = array('escape' => false, 'title' => __('Inactivate'), 'class'=> 'btn btn-danger btn-sm'), __('Are you sure you want to inactivate this tag?')) ?>
									</div>
								</td>
							<?endif;?>
						</tr>
					<? endforeach; ?>
				<?endif;?>
			</table>
	    </div>
    <?endif;?>
</div>
<script type="text/javascript">
	$(function(){
		$(".last-week-logs").sparkline('html', {
		    type: 'line',
		    width: '100px',
		    height: '15px',
		    lineColor: '#ff4e50',
		    fillColor: '',
		    lineWidth: 2,
		    spotColor: '#a9282a',
		    minSpotColor: '#a9282a',
		    maxSpotColor: '#a9282a',
		    highlightSpotColor: '#a9282a',
		    highlightLineColor: '#f4c3c4',
		    spotRadius: 2,
		    drawNormalOnTop: true
		 });
	});
</script>