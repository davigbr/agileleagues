<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Event</strong></div>
			</div>
			<div class="panel-body">
				<? echo $this->Bootstrap->create('Event'); ?>
				<? echo $this->Bootstrap->hidden('id'); ?>
				<? echo $this->Bootstrap->input('event_type_id', array('class' => 'form-control')); ?>
				<? echo $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
				<? echo $this->Bootstrap->input('description', array('class' => 'form-control')); ?>
				<? echo
					$this->Bootstrap->input('xp', array(
						'type' => 'text',
						'data-mask' => 'decimal', 
						'maxlength' => 4,
						'class'=>'form-control'
					)); 
				?>
				<? echo $this->Bootstrap->input('start', array('class'=>'form-control form-control-inline')); ?>
				<? echo $this->Bootstrap->input('end', array('class'=>'form-control form-control-inline')); ?>
				<br/>
				<div class="row">
					<div class="col-sm-6">
						<h4><strong>Tasks</strong></h4>
						<table class="table table-bordered table-striped">
							<tr>
								<th>Name</th>
								<th>Description</th>
								<th>XP</th>
							</tr>
							<?for($i=0;$i<8;$i++):?>
								<tr>
									<td>
										<? echo $this->Bootstrap->input('EventTask.name', array( 
											'name' => "data[EventTask][$i][name]",
											'value' => isset($this->request->data['EventTask'][$i])? $this->request->data['EventTask'][$i]['name']:'',
											'label' => false, 
											'size' => 10,
											'placeholder' => 'Name',
											'class' => 'form-control')); ?>
									</td>
									<td>
										<? echo $this->Bootstrap->input('EventTask.description', array( 
											'type' => 'text',
											'name' => "data[EventTask][$i][description]",
											'value' => isset($this->request->data['EventTask'][$i])? $this->request->data['EventTask'][$i]['description']:'',
											'label' => false, 
											'placeholder' => 'Description',
											'class' => 'form-control')); ?>
									</td>
									<td>
										<? echo $this->Bootstrap->input('EventTask.xp', array(
											'name' => "data[EventTask][$i][xp]",
											'placeholder' => 'XP', 
											'label' => false, 
											'size' => 5,
											'type' => 'text', 
											'value' => @$this->request->data['EventTask'][$i]['xp'],
											'maxlength' => 4,
											'data-mask' => 'decimal',
											'class' => 'form-control'
										)); ?>
									</td>	
								</tr>
							<?endfor;?>
						</table>
					</div>
					<div class="col-sm-6">
						<h4><strong>Activities</strong></h4>

						<table class="table table-bordered table-striped">
							<tr>
								<th>Activity</th>
								<th>Times</th>
							</tr>
							<?for($i=0;$i<8;$i++):?>
								<td>
									<? echo $this->Bootstrap->input('EventActivity.activity_id', array(
										'name' => "data[EventActivity][$i][activity_id]",
										'label' => false, 
										'empty' => '-',
										'value' => isset($this->request->data['EventActivity'][$i])? $this->request->data['EventActivity'][$i]['activity_id'] : '',
										'class' => 'form-control'
									)); ?>
								</td>
								<td>
									<? echo $this->Bootstrap->input('EventActivity.count', array(
										'name' => "data[EventActivity][$i][count]",
										'placeholder' => 'Times', 
										'label' => false, 
										'type' => 'text', 
										'value' => @$this->request->data['EventActivity'][$i]['count'],
										'maxlength' => 4,
										'size' => 5,
										'data-mask' => 'decimal',
										'class' => 'form-control'
									)); ?>
								</td>
							</tr>
							<?endfor;?>
						</table>
					</div>
				</div>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Save</button>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	