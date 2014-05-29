<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Notifications</h1>
            <br/>
    		<a style="color: white" class="btn btn-large btn-success" href="<?= $this->Html->url('/notifications/send')?>">Send</a>
        </div>
    </div>
    <div class="panel-body with-table">
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>Sender</th>
				<th>Target</th>
				<th>Title</th>
				<th>Text</th>
				<th>Read</th>
				<th>Created</th>
				<th>Type</th>
			</tr>
			<?if (empty($notifications)): ?>
				<tr>
					<td colspan="6">
						<p style="text-align: center">Zero notifications :(</p>
					</td>
				</tr>
			<?else:?>
				<? foreach ($notifications as $notification) : ?>
					<tr>
						<td><a href="<?= $this->Html->url('/players/')?>"><?= h($notification['PlayerSender']['name']) ?></a></td>
						<td><a href="<?= $this->Html->url('/players/')?>"><?= h($notification['Player']['name']) ?></a></td>
						<td><?= h($notification['Notification']['title']) ?></td>
						<td><?= h($notification['Notification']['text']) ?></td>
						<td><i class="glyphicon <?= $notification['Notification']['read']? 'glyphicon-ok': 'glyphicon-remove' ?>"></i></td>
						<td><?= $this->Format->date($notification['Notification']['created']) ?></td>
						<td>
							<?
							    $type = $notification['Notification']['type'];
							    $badge = $type;
							    if ($type === 'error') {
							    	$badge = 'danger';
							    }
							?>
							<span class="badge badge-<?= $badge?>">
								<?= h($notification['Notification']['type']) ?>
							</span>
						</td>
					</tr>
				<? endforeach; ?>
			<?endif;?>
		</table>
    </div>
</div>
