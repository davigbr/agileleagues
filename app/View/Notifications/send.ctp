<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Send Notification</strong></div>
			</div>
			<div class="panel-body">
				<? echo $this->Bootstrap->create('Notification'); ?>
				<div class="alert alert-info">
					<div id="activity-description">
						Do not select a player if you want to broadcast this notification.
					</div>
				</div>
				<? echo $this->Bootstrap->input('player_id', array('class'=>'form-control', 'empty'=>'-')); ?>
				<? echo $this->Bootstrap->input('title', array('class'=>'form-control')); ?>
				<? echo $this->Bootstrap->input('text', array('class'=>'form-control')); ?>
				<? echo $this->Bootstrap->input('type', array(
					'type' => 'select',
					'options' => array(
						'info' => 'Information - Blue Light',
						'success' => 'Success - Green Light',
						'warning' => 'Warning - Orange Light',
						'error' => 'Error - Red Light',
					),
					'class'=>'form-control'
				)); ?>
				<button type="submit" class="btn btn-success">Send</button>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>
