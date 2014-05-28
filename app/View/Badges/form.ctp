<?= $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
<?= $this->Bootstrap->input('icon', array('type' => 'text', 'class' => 'form-control')); ?>
<br/>
<div class="row">
	<div class="col-sm-6">
		<h4><strong>Badge Requisites</strong></h4>
		<table class="table table-bordered table-striped">
			<tr>
				<th>Badge</th>
			</tr>
			<?for($i=0;$i<4;$i++):?>
			<tr>
				<td>
					<?= $this->Bootstrap->input('BadgeRequisite.badge_id_requisite', array( 
						'name' => "data[BadgeRequisite][$i][badge_id_requisite]",
						'options' => $badges,
						'empty' => '-',
						'value' => isset($this->request->data['BadgeRequisite'][$i])? $this->request->data['BadgeRequisite'][$i]['badge_id_requisite']:'',
						'label' => false, 
						'class' => 'form-control')); ?>
				</td>
			</tr>
			<?endfor;?>
		</table>
	</div>
	<div class="col-sm-6">
		<h4><strong>Activity Requisites</strong></h4>

		<table class="table table-bordered table-striped">
			<tr>
				<th>Activity</th>
				<th>Times</th>
			</tr>
			<?for($i=0;$i<4;$i++):?>
				<td>
					<?= $this->Bootstrap->input('ActivityRequisite.activity_id', array(
						'name' => "data[ActivityRequisite][$i][activity_id]",
						'label' => false, 
						'empty' => '-',
						'value' => isset($this->request->data['ActivityRequisite'][$i])? $this->request->data['ActivityRequisite'][$i]['activity_id'] : '',
						'class' => 'form-control'
					)); ?>
				</td>
				<td>
					<?= $this->Bootstrap->input('ActivityRequisite.count', array(
						'name' => "data[ActivityRequisite][$i][count]",
						'placeholder' => 'Times', 
						'label' => false, 
						'type' => 'text', 
						'value' => @$this->request->data['ActivityRequisite'][$i]['count'],
						'maxlength' => 5,
						'data-mask' => 'decimal',
						'class' => 'form-control'
					)); ?>
				</td>
			</tr>
			<?endfor;?>
		</table>
	</div>
</div>
<?= $this->Bootstrap->input('new'); ?>
<button type="submit" class="btn btn-success">Save</button>