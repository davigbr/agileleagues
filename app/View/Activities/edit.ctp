<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Edit Activity</strong></div>
			</div>
			<div class="panel-body">
				<? echo $this->Bootstrap->create('Activity'); ?>
				<? echo $this->Bootstrap->hidden('id'); ?>
				<? echo $this->Bootstrap->input('domain_id', array('class' => 'form-control')); ?>
				<? echo $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
				<? echo $this->Bootstrap->input('description', array('class' => 'form-control')); ?>
				<? echo $this->Bootstrap->input('xp', array('data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
				<? echo $this->Bootstrap->input('new'); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Save</button>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	