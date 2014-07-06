	<div class="row">
		<div class="col-md-6 col-sm-8 clearfix">
			<ul class="user-info pull-left pull-none-xsm">
				<?if ($loggedPlayer): ?>
					<li class="profile-info dropdown">
						<a href="<?= $this->Html->url('/players/myaccount'); ?>">
							<img src="<?= $this->Gravatar->get($loggedPlayer['Player']['email'], 50) ?>" alt="" class="img-circle" width="50">
							<?if ($loggedPlayer): ?>
								<?= $loggedPlayer['Player']['name']?>
								<? if ($isPlayer): ?>
								 - <?= $loggedPlayer['Player']['title']?>
								<?endif;?>
							<? endif;?>
						</a>
					</li>
				<?endif;?>
				<?if ($isPlayer): ?>
					<li class="profile-info">
						<div class="progress" style="width: 320px; margin: 5px; margin-left: 10px; margin-top: 5px">
							<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $loggedPlayer['Player']['progress']?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $loggedPlayer['Player']['progress']?>%">
							</div>
						</div>
						<div style="width: 320px;">
							<div class="col-sm-6">	
								<span>Level <?= $loggedPlayer['Player']['level']?> <?= $loggedPlayer['PlayerType']['name']?></span>
							</div>
							<div class="col-sm-6">	
								<small class="pull-right"> 
									<?=(int)$loggedPlayer['Player']['progress']?>%
									(<?= $loggedPlayer['Player']['next_level_xp_completed']?> / <?= $loggedPlayer['Player']['next_level_xp']?> XP)
								</small> 
							</div>
						</div>
					</li>
				<?endif;?>
			</ul>
		</div>
		<div class="col-md-6 col-sm-4 clearfix hidden-xs">
			<div class="pull-right btn-group" style="margin-top: 5px">
				<? if($isGameMaster): ?>
					<a class="btn btn-md btn-info" href="<?= $this->Html->url('/pages/welcome')?>">Welcome!</a>
				<?endif;?>
				<a class="btn btn-md btn-success" title="<?=__('Agile Gamification')?>" target="_blank" href="http://www.agilegamification.org/">
					<i class="entypo-book-open"></i> 
					<span class="hidden-sm hidden-md"><?=__('Agile Gamification')?></span>
				</a>
				<?if ($loggedPlayer): ?>
					<div class="btn-group">
						<a href="#" class="btn btn-primary hidden-sm btn-md dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-comments"></i> &nbsp;Contact&nbsp; <span class="caret"></span>
						</a>
						<ul class="dropdown-menu dropdown-primary" role="menu">
							<li>
								<a href="<?= $this->Html->url('/contact/bug')?>" title="<?=__('Report a Bug')?>">
									<i class="fa fa-bug"></i>&nbsp; <?=__('Report a Bug')?>
								</a>
							</li>
							<li>
								<a href="<?= $this->Html->url('/contact/feature')?>" title="<?=__('Request a Feature')?>">
									<i class="fa fa-list"></i>&nbsp; <?=__('Request a Feature')?> 
								</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="mailto:contact@agileleagues.com">
									<i class="fa fa-envelope"></i>&nbsp; contact@agileleagues.com
								</a>
							</li>
						</ul>
					</div>
					<a class="btn btn-md btn-primary" href="<?= $this->Html->url('/players/logout'); ?>">Logout <i class="entypo-logout right"></i></a>
				<?else:?>
					<a class="btn btn-md btn-primary" href="<?= $this->Html->url('/players/login'); ?>">Sign In <i class="entypo-login right"></i></a>
				<?endif;?>
			</div>
		</div>
	</div>
	<hr />
