<style type="text/css">
	td {
		height: 65px;
		vertical-align: middle !important;
	}

</style>
<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Invitations</h1>
        </div>
    </div>
	<?if (empty($players)):?>
	    <div class="panel-body">
    		<p>No players found :(</p>
    	</div>
	<?else:?>
	    <div class="panel-body">
			<p><?= __('Pending player invitations.')?></p>
	    </div>
	    <div class="panel-body with-table">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th style="text-align: center"><strong>Avatar</strong></th>
					<th><strong>Name</strong></th>
					<th><strong>Team</strong></th>
					<th><strong>E-mail</strong></th>
					<th><strong>Actions</strong></th>
				</tr>
				<? foreach ($players as $player) : ?>
					<tr>
						<td style="text-align: center"><img src="<? echo $this->Gravatar->get($player['Player']['email'], 60) ?>" alt="" class="img-rounded" width="60"></td>
						<td><? echo h($player['Player']['name']); ?>, <? echo h($player['Player']['title']); ?></td>
						<td><? echo h($player['Team']['name']); ?></td>
						<td><? echo h($player['Player']['email']); ?></td>
						<td>
							<?if ($isGameMaster): ?>
								<a title="<?= __('Change team')?>" class="btn btn-info" href="<? echo $this->Html->url('/players/team/' . $player['Player']['id']); ?>">
									<i class="entypo-users"></i> Team
								</a>
								<a class="btn btn-primary" href="<?= $this->Html->url('/players/resendInvitation/' . $player['Player']['id'])?>">
									<i class="entypo-mail"></i>
									Resend Invitation E-mail
								</a>
							<?endif;?>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<?endif;?>
</div>		

<a href="<?= $this->Html->url('/players/invite')?>" class="btn btn-lg btn-success">Invite New Player</a>