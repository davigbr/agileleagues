<ul id="main-menu" class="">
	<!-- add class "multiple-expanded" to allow multiple submenus to open -->
	<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
	<!-- Search Bar -->
	<!-- <li id="search">
		<form method="get" action="">
			<input type="text" name="q" class="search-input" placeholder="Search something..."/>
			<button type="submit">
				<i class="entypo-search"></i>
			</button>
		</form>
	</li> -->
	<!-- <li data-controller="timeline">
		<a href="<? echo $this->Html->url('/timeline'); ?>">
			<i class="entypo-clock"></i>
			<span>Timeline</span>
		</a>
	</li> -->
	<li data-controller="dashboards">
		<a href="#">
			<i class="entypo-chart-pie"></i>
			<span>Dashboards</span>
		</a>
		<ul>
			<li data-action="players">
				<a href="<? echo $this->Html->url('/dashboards/players'); ?>"><i class="entypo-user"></i>
					<span>Players</span>
				</a>
			</li>
			<li data-action="leaderboards">
				<a href="<? echo $this->Html->url('/dashboards/leaderboards'); ?>"><i class="entypo-star"></i>
					<span>Leaderboards</span>
				</a>
			</li>
			<?if ($loggedPlayer): ?>
				<li data-action="activities">
					<a href="<? echo $this->Html->url('/dashboards/activities'); ?>">
						<i class="entypo-flag"></i>
						<span>Activities</span>
					</a>
				</li>
				<!-- <li data-action="badges"><a href="<? echo $this->Html->url('/dashboards/badges'); ?>"><i class="entypo-trophy"></i><span>Badges</span></a></li>-->
			<?endif;?>
		</ul>
	</li>

	<?if ($loggedPlayer): ?>
	<li data-controller="activities">
		<a href="#">
			<i class="entypo-flag"></i>
			<span>Activities</span>
		</a>
		<ul>
			<li data-action="index">	
				<a href="<? echo $this->Html->url('/activities/'); ?>">
					<i class="entypo-list"></i>
					<span>Activities List</span>
				</a>
			</li>
			<li data-action="report"><a href="<? echo $this->Html->url('/activities/report'); ?>"><i class="entypo-doc"></i><span>Report Activity</span></a></li>
			<?if ($isScrumMaster): ?>
				<li data-action="notReviewed">
					<a href="<? echo $this->Html->url('/activities/notReviewed'); ?>">
						<i class="entypo-help"></i>
						<span>Activities Not Reviewed</span>
						<?if($activitiesNotReviewedCount > 0): ?>
							<span class="badge badge-danger"><? echo $activitiesNotReviewedCount?></span>
						<?endif;?>
					</a>
				</li>
			<?elseif ($isDeveloper): ?>
				<li data-action="calendar">
					<a href="<? echo $this->Html->url('/activities/calendar'); ?>">
						<i class="entypo-calendar"></i><span>Calendar</span>
					</a>
				</li>
				<li data-action="myreviewed"><a href="<? echo $this->Html->url('/activities/myreviewed'); ?>"><i class="entypo-check"></i><span>My Reviewed Activities</span></a></li>
				<li data-action="mypending">
					<a href="<? echo $this->Html->url('/activities/mypending'); ?>">
						<i class="entypo-clipboard"></i>
						<span>My Pending Activities </span>
						<?if($myPendingActivitiesCount > 0): ?>
							<span class="badge badge-danger"><? echo $myPendingActivitiesCount?></span>
						<?endif;?>
					</a>
				</li>
			<?endif;?>
		</ul>
	</li>
	<?endif; ?>
	<?if ($loggedPlayer): ?>
		<li data-controller="badges">
			<a href="#">
				<i class="entypo-trophy"></i>
				<span>Badges</span>
			</a>
			<ul>
				<li data-action="index">
					<a href="<? echo $this->Html->url('/badges/'); ?>">
						<span><i class="entypo-list"></i>Badges List</span>
					</a>
				</li>
			</ul>
		</li>
		<li data-controller="domains">
			<a href="#">
				<i class="entypo-code"></i>
				<span>Domains</span>
			</a>
			<ul>
				<li data-action="index"><a href="<? echo $this->Html->url('/domains/'); ?>"><span><i class="entypo-list"></i>Domains List</span></a></li>
				<?foreach ($allDomains as $domain): ?>
					<li data-action="badges">
						<a href="<? echo $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>">
							<span><i class="<? echo $domain['Domain']['icon']?>"></i><? echo $domain['Domain']['name']?> Badges</span>
						</a>
					</li>
				<?endforeach;?>
			</ul>
		</li>
		<li data-controller="players">
			<a href="#">
				<i class="entypo-user"></i>
				<span>Players</span>
			</a>
			<ul>
				<li data-action="index">
					<a href="<? echo $this->Html->url('/players/'); ?>">
						<span><i class="entypo-list"></i>Players List</span>
					</a>
				</li>
			</ul>
		</li>
	<?endif;?>
	<li data-controller="events">
		<a href="#">
			<i class="entypo-calendar"></i>
			<span>Events</span>
			<span class="badge badge-danger">NEW</span>
		</a>
		<ul>
			<li data-action="index">
				<a href="<? echo $this->Html->url('/events/index'); ?>"><i class="entypo-list"></i>
					<span>List all events</span>
					<span class="badge badge-danger">NEW</span>
				</a>
			</li>
			<li data-action="report">
				<a href="<? echo $this->Html->url('/events/report'); ?>">
					<i class="entypo-doc"></i><span>Report Event Task</span>
				</a>
			</li>
			<?if ($isScrumMaster): ?>
				<li data-action="create">
					<a href="<? echo $this->Html->url('/events/create'); ?>"><i class="entypo-plus"></i>
						<span>Create</span>
						<span class="badge badge-danger">NEW</span>
					</a>
				</li>
				<li data-action="notReviewed">
					<a href="<? echo $this->Html->url('/events/notReviewed'); ?>">
						<i class="entypo-help"></i>
						<span>Event Tasks Not Reviewed</span>
						<?if($eventTasksNotReviewedCount > 0): ?>
							<span class="badge badge-danger"><? echo $eventTasksNotReviewedCount?></span>
						<?endif;?>
					</a>
				</li>
			<?endif;?>
		</ul>
	</li>
</ul>

<script type="text/javascript">
	$(function(){
		var controller = '<? echo $this->request->controller;?>';
		var action = '<? echo $this->request->action;?>';
		$('[data-controller=' + controller + ']').addClass('active').addClass('opened');
		$('li[data-controller=' + controller + '] li[data-action=' + action + ']').addClass('active');
		$('[data-controller] ul').addClass('visible');
	});
</script>