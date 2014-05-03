<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Change Team - <?= $this->request->data['Player']['name']?></strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Player'); ?>
				<?= $this->Bootstrap->hidden('id'); ?>
				<?= $this->Bootstrap->input('team_id', array(
					'empty' => '-',
					'class' => 'form-control'
				)); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Save</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>