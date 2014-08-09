<ul id="main-menu" class="">
	<li data-controller="dashboards">
		<a href="#">
			<i class="entypo-chart-pie"></i>
			<span>Dashboards</span>
		</a>
		<ul>
			<li data-action="stats">
				<a href="<?= $this->Html->url('/dashboards/stats'); ?>"><i class="entypo-chart-bar"></i>
					<span>Stats</span>
				</a>
			</li>
			<li data-action="players">
				<a href="<?= $this->Html->url('/dashboards/players'); ?>"><i class="entypo-user"></i>
					<span>Players</span>
				</a>
			</li>
			<li data-action="leaderboards">
				<a href="<?= $this->Html->url('/dashboards/leaderboards'); ?>"><i class="entypo-star"></i>
					<span>Leaderboards</span>
				</a>
			</li>
			<?if ($loggedPlayer && $isPlayer): ?>
				<li data-action="activities">
					<a href="<?= $this->Html->url('/dashboards/activities'); ?>">
						<i class="entypo-flag"></i>
						<span>Activities</span>
					</a>
				</li>
				<!-- <li data-action="badges"><a href="<?= $this->Html->url('/dashboards/badges'); ?>"><i class="entypo-trophy"></i><span>Badges</span></a></li>-->
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
				<?if ($isGameMaster): ?>
					<li data-action="add">
						<a href="<?= $this->Html->url('/activities/add'); ?>"><i class="entypo-plus"></i>
							<span>Create</span>
						</a>
					</li>
				<?endif;?>
				<li data-action="index">	
					<a href="<?= $this->Html->url('/activities/'); ?>">
						<i class="entypo-list"></i>
						<span>Activities List</span>
					</a>
				</li>
				<?if (!$isGameMaster): ?>
					<li data-action="report"><a href="<?= $this->Html->url('/activities/report'); ?>"><i class="entypo-doc"></i><span>Report Activity</span></a></li>
					<li data-action="team">
						<a href="<?= $this->Html->url('/activities/team'); ?>">
							<i class="entypo-users"></i>
							<span>Team Pending Activities</span>
							<?if($teamPendingActivities > 0): ?>
								<span class="badge badge-danger"><?= $teamPendingActivities?></span>
							<?else:?>
								<span class="badge badge-danger">NEW</span>
							<?endif;?>
						</a>
					</li>
					<li data-action="calendar">
						<a href="<?= $this->Html->url('/activities/calendar'); ?>">
							<i class="entypo-calendar"></i><span>Calendar</span>
						</a>
					</li>
					<li data-action="myActivities">
						<a href="<?= $this->Html->url('/activities/myActivities'); ?>">
							<i class="entypo-flag"></i>
							<span>My Activities</span>
							<span class="badge badge-danger">NEW</span>
						</a>
					</li>
					<li data-action="summary">
						<a href="<?= $this->Html->url('/activities/summary'); ?>">
							<i class="entypo-star"></i>
							<span>Activities Summary</span>
						</a>
					</li>
					<li data-action="myPending">
						<a href="<?= $this->Html->url('/activities/myPending'); ?>">
							<i class="entypo-clipboard"></i>
							<span>My Pending Activities </span>
							<?if($myPendingActivitiesCount > 0): ?>
								<span class="badge badge-danger"><?= $myPendingActivitiesCount?></span>
							<?endif;?>
						</a>
					</li>
				<?endif;?>
			</ul>
		</li>

		<li data-controller="tags">
			<a href="#">
				<i class="entypo-tag"></i>
				<span>Tags</span>
			</a>
			<ul>
				<?if ($isGameMaster): ?>
					<li data-action="add">
						<a href="<?= $this->Html->url('/tags/add'); ?>"><i class="entypo-plus"></i>
							<span>Create</span>
						</a>
					</li>
				<?endif;?>
				<li data-action="index">	
					<a href="<?= $this->Html->url('/tags/'); ?>">
						<i class="entypo-list"></i>
						<span>Tags List</span>
						<span class="badge badge-danger">NEW</span>
					</a>
				</li>
			</ul>
		</li>
		<li data-controller="badges">
			<a href="#">
				<i class="entypo-trophy"></i>
				<span>Badges</span>
			</a>
			<ul>
				<?if ($isGameMaster): ?>
					<li data-action="add">
						<a href="<?= $this->Html->url('/badges/add'); ?>"><i class="entypo-plus"></i>
							<span>Create</span>
						</a>
					</li>
				<?endif;?>
				<li data-action="index">
					<a href="<?= $this->Html->url('/badges/'); ?>">
						<span><i class="entypo-list"></i>Badges List</span>
					</a>
				</li>
				<?if ($isGameMaster): ?>
					<li data-action="claimed">
						<a href="<?= $this->Html->url('/badges/claimed'); ?>">
							<span><i class="entypo-thumbs-up"></i>Claimed Badges</span>
						</a>
					</li>
				<?endif;?>
			</ul>
		</li>
		<li data-controller="domains">
			<a href="#">
				<i class="entypo-code"></i>
				<span>Domains</span>
			</a>
			<ul>
				<?if ($isGameMaster): ?>
					<li data-action="add">
						<a href="<?= $this->Html->url('/domains/add'); ?>"><i class="entypo-plus"></i>
							<span>Create</span>
						</a>
					</li>
				<?endif;?>
				<li data-action="index"><a href="<?= $this->Html->url('/domains/'); ?>"><span><i class="entypo-list"></i>Domains List</span></a></li>
				<?foreach ($allDomains as $domain): ?>
					<li data-action="badges">
						<a href="<?= $this->Html->url('/domains/badges/' . $domain['Domain']['id']); ?>">
							<span><i class="<?= h($domain['Domain']['icon'])?>"></i><?= h($domain['Domain']['name'])?> Badges</span>
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
				<?if ($isGameMaster): ?>
					<li data-action="invite">
						<a href="<?= $this->Html->url('/players/invite'); ?>"><i class="entypo-plus"></i>
							<span>Invite</span>
						</a>
					</li>
					<li data-action="invited">
						<a href="<?= $this->Html->url('/players/invited'); ?>">
							<span><i class="entypo-mail"></i>Invitations</span>
						</a>
					</li>
				<?endif;?>
				<li data-action="index">
					<a href="<?= $this->Html->url('/players/'); ?>">
						<span><i class="entypo-list"></i>Players List</span>
					</a>
				</li>
			</ul>
		</li>
		<!-- <li data-controller="events">
			<a href="#">
				<i class="entypo-calendar"></i>
				<span>Events</span>
			</a>
			<ul>
				<li data-action="index">
					<a href="<?= $this->Html->url('/events/index'); ?>"><i class="entypo-list"></i>
						<span>List all events</span>
					</a>
				</li>
				<li data-action="report">
					<a href="<?= $this->Html->url('/events/report'); ?>">
						<i class="entypo-doc"></i><span>Report Event Task</span>
					</a>
				</li>
				<?if ($isGameMaster): ?>
					<li data-action="create">
						<a href="<?= $this->Html->url('/events/create'); ?>"><i class="entypo-plus"></i>
							<span>Create</span>
						</a>
					</li>
					<li data-action="notReviewed">
						<a href="<?= $this->Html->url('/events/notReviewed'); ?>">
							<i class="entypo-help"></i>
							<span>Event Tasks Not Reviewed</span>
							<?if($eventTasksNotReviewedCount > 0): ?>
								<span class="badge badge-danger"><?= $eventTasksNotReviewedCount?></span>
							<?endif;?>
						</a>
					</li>
				<?endif;?>
			</ul>
		</li> -->
		<li data-controller="notifications">
			<a href="#">
				<i class="entypo-mail"></i>
				<span>Notifications</span>
			</a>
			<ul>
				<li data-action="index">
					<a href="<?= $this->Html->url('/notifications'); ?>"><i class="entypo-list"></i>
						<span>Last Notifications</span>
					</a>
				</li>
				<li data-action="send">
					<a href="<?= $this->Html->url('/notifications/send'); ?>"><i class="entypo-pencil"></i>
						<span>Send</span>
					</a>
				</li>
			</ul>
		</li>
		<?if ($isGameMaster): ?>
			<li data-controller="teams">
				<a href="#">
					<i class="entypo-users"></i>
					<span>Teams</span>
				</a>
				<ul>
					<li data-action="add">
						<a href="<?= $this->Html->url('/teams/add'); ?>"><i class="entypo-plus"></i>
							<span>Create</span>
						</a>
					</li>
					<li data-action="index">
						<a href="<?= $this->Html->url('/teams'); ?>"><i class="entypo-list"></i>
							<span>List all teams</span>
						</a>
					</li>
				</ul>
			</li>
		<?endif;?>
	<?endif;?>
</ul>

<script type="text/javascript">
	$(function(){
		var controller = '<?= $this->request->controller;?>';
		var action = '<?= $this->request->action;?>';
		$('[data-controller=' + controller + ']').addClass('active').addClass('opened');
		$('li[data-controller=' + controller + '] li[data-action=' + action + ']').addClass('active');
		$('[data-controller] ul').addClass('visible');
	});
</script>