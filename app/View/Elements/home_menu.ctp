<header class="navbar navbar-fixed-top">
	<div class="navbar-inner" style="width: 1170px; margin: auto">
		<div class="navbar-brand">
			<?= $this->element('logo')?>
		</div>
		<ul class="navbar-nav">
			<li class="root-level">
				<a href="#">
					<span>Scrum Gamification</span>
				</a>
			</li>
			<li class="root-level">
				<a href="#">
					<span>XP Gamification</span>
				</a>
			</li>
			<li class="root-level">
				<a href="#">
					<span>Contact Us</span>
				</a>
			</li>
		</ul>
		<ul class="nav navbar-right pull-right">
			<li>
				<a href="<?= $this->Html->url('/dashboards/activities')?>">Play <i class="entypo-play right"></i></a>
			</li>
			<li class="sep"></li>
			<li>
				<?if ($loggedPlayer): ?>
					<a href="<? echo $this->Html->url('/players/logout'); ?>">Logout <i class="entypo-logout right"></i></a>
				<?else:?>
					<a href="<? echo $this->Html->url('/players/login'); ?>">Login <i class="entypo-login right"></i></a>
				<?endif;?>
			</li>
		</ul>
	</div>
</header>