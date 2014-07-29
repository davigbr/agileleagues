<?= $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
<?= $this->Bootstrap->input('icon', array('type' => 'text', 'readonly' => 'readonly', 'class' => 'form-control')); ?>
<br/>
<div class="row">
	<div class="col-sm-8">
		<h4><strong>Activity Requisites</strong></h4>
		<table class="table table-bordered table-striped">
			<tr>
				<th>Times</th>
				<th>Activity</th>
			</tr>
			<?for($i=0;$i<4;$i++):?>
				<tr style="height: 85px">
					<td style="width: 140px">
						<?= $this->Bootstrap->input('ActivityRequisite.count', array(
							'name' => "data[ActivityRequisite][$i][count]",
							'placeholder' => 'Times', 
							'label' => false, 
							'type' => 'number',
							'min' => 1,
							'max' => 99999,
							'value' => @$this->request->data['ActivityRequisite'][$i]['count'],
							'style' => 'width: 100px; height: 50px; text-align: center',
							'after' => '&nbsp; X',
							'class' => 'form-control form-control-inline'						
						)); ?>
					</td>
					<td>
						<?= $this->Bootstrap->input('ActivityRequisite.activity_id', array(
							'name' => "data[ActivityRequisite][$i][activity_id]",
							'label' => false, 
							'empty' => '-',
							'value' => isset($this->request->data['ActivityRequisite'][$i])? $this->request->data['ActivityRequisite'][$i]['activity_id'] : '',
							'class' => 'form-control'
						)); ?>
						<? $j = 0; ?>
						<? 
							$tagsFound = array();
							if (isset($this->request->data['ActivityRequisite'][$i]['Tags'])) {
								foreach ($this->request->data['ActivityRequisite'][$i]['Tags'] as $tag) {
									$tagsFound[] = $tag['id'];
								}
							}
						?>

						<?foreach ($tags as $tag): ?>
							<? $selected = in_array($tag['Tag']['id'], $tagsFound); ?>
							<div style="margin-bottom: 10px; display: inline-block">
								<span 
									title="<?= h($tag['Tag']['description']? $tag['Tag']['description'] : $tag['Tag']['name']) ?>"
									class="unselectable tag label" 
									data-tag-color="<?= h($tag['Tag']['color'])?>"
									data-tag-selected="<?= $selected? 'true' : 'false'?>"
									<? if ($selected): ?>
										style="margin: 0; cursor: pointer; background-color: <?= h($tag['Tag']['color'])?>"
									<? else : ?>
										style="margin: 0; cursor: pointer; color: black"
									<? endif; ?>
									>
									<?= h($tag['Tag']['name']) ?>
								</span>
								<input type="hidden" 
									name="data[ActivityRequisite][<?=$i?>][Tags][Tags][<?= $j?>]" 
									data-id="<?= (int)$tag['Tag']['id']?>"
									value="<?= $selected? (int)$tag['Tag']['id'] : ''?>"
								 />
							</div>
							<? $j++; ?>
						<?endforeach;?>
					</td>
				</tr>
			<?endfor;?>
		</table>
	</div>
	<div class="col-sm-4">
		<h4><strong>Badge Requisites</strong></h4>
		<table class="table table-bordered table-striped">
			<tr>
				<th>Badge</th>
			</tr>
			<?for($i = 0; $i < 6; $i ++):?>
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
</div>
<?= $this->Bootstrap->input('new'); ?>
<? if ($gameMasterCredlyAccountSetup): ?>
	<h4><strong>Credly Integration</strong></h4>
	<?= $this->Bootstrap->input('credly_badge_id', array(
		'label' => 'Credly Badge Id',
		'type' => 'number', 
		'pattern' => '\d*', 
		'placeholder' => 'Plase type the id of your Credly badge',
		'min' => 0, 
		'max' => 20000000, 
		'class' => 'form-control'
	)); ?>
<?endif;?>
<br/>
<button id="form-submit" type="submit" class="btn btn-success">Save</button>

<script type="text/javascript">
	$(function(){
		$('span.tag').click(function(){
			var $tag = $(this);
			var $input = $('input', $tag.parent());
			if ($tag.attr('data-tag-selected') === 'false') {
				$tag.css('background-color', $tag.attr('data-tag-color'));
				$tag.attr('data-tag-selected', 'true');
				$tag.css('color', 'white');
				$input.val($input.attr('data-id'));
			} else {
				$tag.css('background-color', '');
				$tag.css('color', 'black');
				$tag.attr('data-tag-selected', 'false');
				$input.val('');
			}
		});
		$('#form-submit').click(function(){
			$('#form-submit').unbind().click();
			return false;
		});
	});
</script>
