<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/font-icons/font-awesome/css/font-awesome.min.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/font-icons/entypo/css/entypo.css'); ?>">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/bootstrap.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/neon-core.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/neon-theme.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/neon-forms.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/custom.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/js/vertical-timeline/css/component.css'); ?>">

	<script src="<? echo Router::url('/assets/js/jquery-1.11.0.min.js'); ?>"></script>
	<script src="<? echo Router::url('/js/analytics.js'); ?>"></script>

	<!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<?php
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<?= $this->element('meta') ?>
</head>
<body class="page-body gray">
	<div class="page-container <? echo $collapseSidebar? 'sidebar-collapsed' : '' ?>">
		<div class="sidebar-menu">
			<header class="logo-env">
				<div class="logo">
					<span style="color: white; font-size: 24px">Agile <span style="color: #888">Leagues</span></span>
				</div>
				<div class="sidebar-collapse">
					<a href="#" class="sidebar-collapse-icon with-animation">
						<i class="entypo-menu"></i>
					</a>
				</div>
				<div class="sidebar-mobile-menu visible-xs">
					<a href="#" class="with-animation">
						<i class="entypo-menu"></i>
					</a>
				</div>
			</header>
			<? echo $this->element('main_menu'); ?>		
		</div>	
		<div class="main-content">
			<? echo $this->element('topbar'); ?>
			<? echo $this->element('messages') ?>	
			<? echo $this->fetch('content'); ?>
			<? echo $this->element('footer'); ?>	
		</div>
	</div>

	<? echo $this->element('notifications'); ?>

	<link rel="stylesheet" href="<? echo Router::url('/assets/js/jvectormap/jquery-jvectormap-1.2.2.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/js/rickshaw/rickshaw.min.css'); ?>">

	<!-- Bottom Scripts -->
	<script src="<? echo Router::url('/assets/js/gsap/main-gsap.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/bootstrap.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/joinable.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/resizeable.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-api.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jvectormap/jquery-jvectormap-europe-merc-en.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jquery.sparkline.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/rickshaw/vendor/d3.v3.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/rickshaw/rickshaw.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-chat.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-custom.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-demo.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jquery.inputmask.bundle.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/toastr.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/fullcalendar/fullcalendar.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-calendar.js'); ?>"></script>
</body>
</html>
