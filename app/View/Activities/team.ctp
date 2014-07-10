<style type="text/css">
	.accept-activity, .reject-activity {
		cursor: pointer;
	}
	td {
		height: 42px;
	}
</style>

<?= $this->Bootstrap->create('Log'); ?>
<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Team Pending Activities</h1>
            <br/>
            <p>
	            Review your teammate's reported activities by clicking 
	            on the <a href="#" onclick="return false" class="btn btn-default"><i class="entypo-thumbs-up"></i></a> 
	            or <a href="#" onclick="return false" class="btn btn-default"><i class="entypo-thumbs-down"></i></a>
	            and typing a comment. After you're done, hit the 
	            <a href="#" onclick="return false" class="btn btn-default">Review all commented activities!</a>
	            button. 
            </p>
            <p>Only the oldest 50 activities are shown.</p>
            <br/>
            <p class="alert alert-warning">Please <strong>pay attention</strong> because you cannot undo or delete your comments.</p>
        </div>
    </div>
    <?if (empty($logs)): ?>
	    <div class="panel-body">
		    <p>No pending activities found for your team. </p>
	    </div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th style="text-align: center" title="Domain">D</th>
					<th>Name</th>
					<th>Tags</th>
					<th>Player</th>
					<th>Acquired</th>
					<th>Description</th>
					<th title="<?=__('The XP bonus for accepting this activity')?>">Bonus</th>
					<th>%</th>
					<th>Accept</th>
					<th title="<?=__('The XP bonus for rejecting this activity')?>">Bonus</th>
					<th>%</th>
					<th>Reject</th>

				</tr>
				<?if (empty($logs)): ?>
					<tr>
						<td colspan="6">
							<p style="text-align: center">No activities :(</p>
						</td>
					</tr>
				<?else:?>
					<? foreach ($logs as $log) : ?>
						<tr>
							<td title="<?= h($log['Domain']['name']); ?>" style="width: 35px; text-align: center; background-color: <?= $log['Domain']['color']?>; color: white">
								<i class="<?= h($log['Domain']['icon']); ?>"></i>
							</td>
							<td>
								<?if ($log['Activity']['new']): ?>
									<span class="badge badge-danger">NEW</span>
								<?endif;?>
								<span data-toggle="tooltip" title="<?= h($log['Activity']['description'])?>"><?= h($log['Activity']['name']) ?></span>
								(<?= h($log['Log']['xp']) ?> XP) 
							</td>
							<td><?= $this->element('tag', array('tags' => $log['Tags'])); ?></td>
							<td>
								<?= h($log['Player']['name'])?>
								<?= isset($log['PairedPlayer']['name'])? (' + ' .  h($log['PairedPlayer']['name'])) : ''?>
							</td>
							<td><?= $this->Format->date($log['Log']['acquired'])?></td>
							<td><?= h($log['Log']['description'])?></td>
							<? if (isset($log['MyVote'])): ?>
								<td>
									<?if($log['MyVote']['vote'] == 1): ?>
										<? $bonus = floor($log['Log']['xp'] * ACCEPTANCE_XP_MULTIPLIER); ?>
										<span class="badge badge-success">+ <?= $bonus < 1? 1 : $bonus?> XP</span>
									<?endif;?>
								</td>
								<td>
									<?=number_format($log['Log']['acceptance_votes'])?>/<?=number_format($log['Activity']['acceptance_votes'])?>
								</td>
								<td>
									<?if($log['MyVote']['vote'] == 1): ?>
										<?= h($log['MyVote']['comment'])?>
									<?endif;?>
								</td>
								<td>
									<?if($log['MyVote']['vote'] == -1): ?>
										<span class="badge badge-danger">
										+<?= REJECTION_XP_BONUS ?> XP</span>
									<?endif;?>
								</td>
								<td>
									<?=number_format($log['Log']['rejection_votes'])?>/<?=number_format($log['Activity']['rejection_votes'])?>
								</td>
								<td>
									<?if($log['MyVote']['vote'] == -1): ?>
										<?= h($log['MyVote']['comment'])?>
									<?endif;?>
								</td>
							<? else: ?>
								<td>
									<? $bonus = floor($log['Log']['xp'] * ACCEPTANCE_XP_MULTIPLIER); ?>
									<span class="badge">+ <?= $bonus < 1? 1 : $bonus?> XP</span>
								</td>
								<td>
									<?=number_format($log['Log']['acceptance_votes'])?>/<?=number_format($log['Activity']['acceptance_votes'])?>
								</td>
								<td>
									<div style="min-width: 100px; max-width: 400px" class="input-group">
										<div class="accept-activity input-group-addon">
											<i style="color: green" class="entypo-thumbs-up"></i></a>
										</div>
										<input maxlength="250" name="data[Log][<?=$log['Log']['id']?>][acceptance_comment]" type="text" disabled="disabled" class="accept-input form-control" />
									</div>
								</td>
								<td>
									<span class="badge">
										+<?= REJECTION_XP_BONUS ?> XP
									</span>
								</td>
								<td>
									<?=number_format($log['Log']['rejection_votes'])?>/<?=number_format($log['Activity']['rejection_votes'])?>
								</td>
								<td>
									<div style="min-width: 100px; max-width: 400px" class="input-group">
										<div class="reject-activity input-group-addon">
											<i style="color: red" class="entypo-thumbs-down"></i></a>
										</div>
										<input maxlength="250" name="data[Log][<?=$log['Log']['id']?>][rejection_comment]" type="text" disabled="disabled" class="reject-input form-control" />
									</div>
								</td>
							<? endif; ?>
						</tr>
						<? if(!empty($log['LogVote'])): ?>
						<tr>
							<td></td>
							<td colspan="5">
								<? foreach ($log['LogVote'] as $vote) : ?>
									<?if ($vote['vote'] > 0): ?>
										<p>
											<span class="badge badge-success">+1</span>
											<strong><?= h($vote['Player']['name'])?></strong> accepted your activity
											in <?= $this->Format->dateTime($vote['creation'])?>
											and commented: <em><?= h($vote['comment'])?></em>
										</p>
									<?else:?>
										<p>
											<span class="badge badge-danger">-1</span>
											<strong><?= h($vote['Player']['name'])?></strong> rejected your activity
											in <?= $this->Format->dateTime($vote['creation'])?>
											and commented: <em><?= h($vote['comment'])?></em>
										</p>
									<?endif;?>
								<? endforeach;?>
							</td>
						</tr>
					<? endif; ?>
					<? endforeach; ?>
				<?endif;?>
			</table>
	    </div>
	    <div class="panel-body">
			<button class="btn btn-success btn-lg">Review all commented activities!</button>
			<br/>
	    </div>
    <?endif;?>
</div>
<?= $this->Bootstrap->end(); ?>

<script type="text/javascript">
	$(function(){
		var onClick = function() {
			var $inputGroup = $(this).parent();
			var $tr = $inputGroup.parent().parent();
			var $input = $('input', $inputGroup);
			var isAccept = $(this).hasClass('accept-activity');
			console.log(isAccept);

			if ($input.attr('disabled')) {
				$input.removeAttr('disabled');
				$input.attr('placeholder', 'Please type a comment');

				if (isAccept) {
					var $oppositeInput = $('input.reject-input', $tr);
				} else {
					var $oppositeInput = $('input.accept-input', $tr);
				}
				$oppositeInput.removeAttr('placeholder');
				$oppositeInput.attr('disabled', 'disabled');
				$oppositeInput.val('');
			} else {
				$input.attr('disabled', 'disabled');
				$input.removeAttr('placeholder');
				$input.val('');
			}
			$input.focus();
			return false;
		};

		$('.accept-activity').click(onClick);
		$('.reject-activity').click(onClick);

	});
</script>