<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<link rel="stylesheet" href="<? echo Router::url('/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/font-icons/entypo/css/entypo.css'); ?>">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/bootstrap.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/neon-core.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/neon-theme.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/neon-forms.css'); ?>">
	<link rel="stylesheet" href="<? echo Router::url('/assets/css/custom.css'); ?>">

	<script src="<? echo Router::url('/assets/js/jquery-1.11.0.min.js'); ?>"></script>

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
</head>
<body class="page-body login-page login-form-fall">
	<script>
		var baseurl = '<? echo $this->Html->url('/players/login.json'); ?>';
	</script>
	<div class="login-container">
		<div class="login-header login-caret">
			<div class="login-content">
				<span style="color: white; font-size:50px">
					<span>Agile</span>
					<span style="color: #888">Leagues</span>
				</span>
				<p style="color: white" class="description">Dear user, please log in.</p>
				<!-- progress bar indicator -->
				<div class="login-progressbar-indicator">
					<h3>43%</h3>
					<span style="color: white">logging in...</span>
				</div>
			</div>
		</div>
		<div class="login-progressbar">
			<div></div>
		</div>
		<div class="login-form">
			<div class="login-content">
				<div class="form-login-error">
					<h3>Invalid login</h3>
					<p>Please enter a correct e-mail and password.</p>
				</div>
				<form method="post" action="<? echo $this->Html->url('/players/login'); ?>" role="form" id="form_login">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="entypo-user"></i>
							</div>
							<input autocomplete="off" type="text" required="required" class="form-control" name="email" id="email" placeholder="E-mail" />
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="entypo-key"></i>
							</div>
							<input autocomplete="off" type="password" required="required" class="form-control" name="password" id="password" placeholder="Password"/>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-block btn-login">
							Login In
							<i class="entypo-login"></i>
						</button>
					</div>
					<div class="form-group">
						<em>- or -</em>
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-default btn-lg btn-block btn-icon icon-left facebook-button">
							Login with Facebook
							<i class="entypo-facebook"></i>
						</button>
					</div>
					<!-- 
					You can also use other social network buttons
					<div class="form-group">
						<button type="button" class="btn btn-default btn-lg btn-block btn-icon icon-left twitter-button">
							Login with Twitter
							<i class="entypo-twitter"></i>
						</button>
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-default btn-lg btn-block btn-icon icon-left google-button">
							Login with Google+
							<i class="entypo-gplus"></i>
						</button>
					</div> -->				
				</form>
				<div class="login-bottom-links">
					<a href="#" class="link">Forgot your password? Talk to the administrator (:</a>
					<br />
					<a href="#">Privacy Policy</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Bottom Scripts -->
	<script src="<? echo Router::url('/assets/js/gsap/main-gsap.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/bootstrap.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/joinable.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/resizeable.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-api.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/jquery.validate.min.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-login.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-custom.js'); ?>"></script>
	<script src="<? echo Router::url('/assets/js/neon-demo.js'); ?>"></script>

</body>
</html>
