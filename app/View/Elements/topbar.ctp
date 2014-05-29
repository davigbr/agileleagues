	<div class="row">
		<div class="col-md-6 col-sm-8 clearfix">
			<ul class="user-info pull-left pull-none-xsm">
				<li class="profile-info dropdown">
					<a href="<?= $this->Html->url('/players/myaccount'); ?>">
						<img src="<?= $this->Gravatar->get($loggedPlayer['Player']['email'], 50) ?>" alt="" class="img-circle" width="50">
						<?if ($loggedPlayer): ?>
							<?= $loggedPlayer['Player']['name']?>, <?= $loggedPlayer['Player']['title']?>
						<? endif;?>
					</a>
				</li>
				<li class="profile-info">
					<div class="progress" style="width: 320px; margin: 5px; margin-left: 10px; margin-top: 5px">
						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $loggedPlayer['Player']['progress']?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $loggedPlayer['Player']['progress']?>%">
						</div>
					</div>
					<div style="width: 320px;">
						<?if ($loggedPlayer): ?>
							<div class="col-sm-6">	
								<span>Level <?= $loggedPlayer['Player']['level']?> <?= $loggedPlayer['PlayerType']['name']?></span>
							</div>
							<div class="col-sm-6">	
								<small class="pull-right"> 
									<?=(int)$loggedPlayer['Player']['progress']?>%
									(<?= $loggedPlayer['Player']['next_level_xp_completed']?> / <?= $loggedPlayer['Player']['next_level_xp']?> XP)
								</small> 
							</div>
						<?else:?>
							<div class="col-sm-6">	
								<span>Level 1</span>
							</div>
							<div class="col-sm-6">	
								<small class="pull-right"> 0% (0 / 0 XP) </small> 
							</div>
						<?endif;?>
					</div>
				</li>
			</ul>
		</div>
		<div class="col-md-6 col-sm-4 clearfix hidden-xs">
			<div class="pull-right btn-group" style="margin-top: 5px">
				<?if ($loggedPlayer): ?>
					<a class="btn btn-sm btn-primary" href="<?= $this->Html->url('/players/logout'); ?>">Logout <i class="entypo-logout right"></i></a>
				<?else:?>
					<a class="btn btn-sm btn-primary" href="<?= $this->Html->url('/players/login'); ?>">Login <i class="entypo-login right"></i></a>
				<?endif;?>
			</div>
		</div>
	</div>
	<hr />
