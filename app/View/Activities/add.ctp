<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>New Activity</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Activity'); ?>
				<?= $this->Bootstrap->input('domain_id', array('class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('description', array('class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('details', array('class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('restrictions', array('class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('xp', array('placeholder' => 'Ammount of experience points the players will earn when this activity is accepted.', 'data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
				<?= $this->Bootstrap->input('acceptance_votes', array('placeholder' => 'Number of votes needed from the players for this activity to be considered accepted.', 'data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
				<?= $this->Bootstrap->input('rejection_votes', array('placeholder' => 'Number of votes needed from the players for this activity to be considered rejected.', 'data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
				<?= $this->Bootstrap->input('new'); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Save</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	