<header class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="navbar-brand">
			<?= $this->element('logo')?>
		</div>
		<ul class="navbar-nav">
			<li class="root-level">
				<a href="#">
					<i class="entypo-gauge"></i>
					<span>Dashboard</span>
				</a>
			</li>
			<li class="root-level">
				<a href="#">
					<i class="entypo-gauge"></i>
					<span>Dashboard</span>
				</a>
			</li>
			<li class="root-level">
				<a href="#">
					<i class="entypo-gauge"></i>
					<span>Dashboard</span>
				</a>
			</li>
			<li class="root-level">
				<a href="#">
					<i class="entypo-gauge"></i>
					<span>Dashboard</span>
				</a>
			</li>
		</ul>
		<ul class="nav navbar-right pull-right">
			<li>
				<a href="<?= $this->Html->url('/dashboards/activities')?>">Play</a>
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