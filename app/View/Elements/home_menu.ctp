<header class="navbar navbar-fixed-top">
	<div class="navbar-inner" style="width: 1170px; margin: auto">
		<div class="navbar-brand">
			<?= $this->element('logo')?>
		</div>
		<ul class="navbar-nav">
			<li class="root-level">
				<a target="_blank" href="http://www.agilegamification.org/gamification-of-scrum/">
					<span>Scrum Gamification</span>
				</a>
			</li>
			<!-- <li class="root-level">
				<a target="_blank" href="http://www.agilegamification.org/gamification-of-xp/">
					<span>XP Gamification</span>
				</a>
			</li> -->
			<li class="root-level">
				<a href="mailto:contact@agileleagues.com">
					<i class="fa fa-envelope"></i><span>&nbsp; contact@agileleagues.com</span>
				</a>
			</li>
		</ul>
		<ul class="nav navbar-right pull-right">
			<li>
				<a href="<?= $this->Html->url('/dashboards/activities')?>">Play <i class="entypo-play right"></i></a>
			</li>
			<li class="sep"></li>
			<?if ($loggedPlayer): ?>
				<li>
					<a href="<?= $this->Html->url('/players/logout'); ?>">Logout <i class="entypo-logout right"></i></a>
				</li>
			<?else:?>
				<li>
					<a href="<?= $this->Html->url('/players/login'); ?>">Sign In <i class="entypo-login right"></i></a>
				</li>
			<?endif;?>
		</ul>
	</div>
</header>