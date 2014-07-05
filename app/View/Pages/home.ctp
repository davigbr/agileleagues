<style type="text/css">
	body, p, blockquote small { font-size: 14px; }
	.tile-stats { min-height: 160px;}
	.img-polaroid { border-radius: 10px; }
</style>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-12">
		<h1>Let's Gamify software development right now!</h1>
		<p class="lead">Agile Leagues is a <a target="_blank" href="http://www.agilegamification.org/">gamification</a> tool that helps you boost <em>Agile Software Development</em> practices inside your teams and across your organization.</p>
		<hr/>
		<h1>Agile Gamification</h1>
		<p>How about mixing up Agile Methodologies and Gamification?</p>
		<blockquote>
			<p>...it refers to the use of game mechanics and rewards in an agile software development setting to increase team engagement and drive desired behaviors.</p>
		</blockquote>
		<p>Read more about at <a target="_blank" href="http://www.agilegamification.org/">www.agilegamification.org</a></p>
		<hr/>
		<div class="row">
			<div class="col-sm-12">
				<p>With <strong>Agile Leagues</strong>, you can:</p>
				<ol>
					<li>Boost software development practices, like Pair Programming or Test-Driven Development;</li>
					<li>Improve collaboration and shared goals across your teams;</li>
					<li>Increase the adoption of Scrum and/or Extreme Programming inside your organization;</li>
					<li>Drive behaviors and increase engagement</li>
				</ol>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4">
				<h2>Domains</h2>
				<p>Start by creating domains. They are areas of knowledge or groups of technical skills that you want to boost. </p>
				<?= $this->Html->image('home/testing.png', array('style' => 'max-width: 100%', 'class' => 'img-polaroid'))?>
			</div>
			<div class="col-sm-4">
				<h2>Activities</h2>
				<p>Define some cool activities that your players are going to perform. They will be rewarded after completing them!</p>
				<?= $this->Html->image('home/activities.png', array('style' => 'max-width: 100%', 'class' => 'img-polaroid'))?>
			</div>
			<div class="col-sm-4">
				<h2>Badges</h2>
				<p>Domains and Activities are nice, but you want more. Add badges to the game so that the players now have some targets.</p>
				<?= $this->Html->image('home/badge.png', array('style' => 'max-width: 100%', 'class' => 'img-polaroid'))?>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6">
				<h2>Leaderboards</h2>
				<p>If you want to promote a little bit of competition, you can use Leaderboards. </p>
				<?= $this->Html->image('home/leaderboards.png', array('style' => 'max-width: 100%', 'class' => 'img-polaroid'))?>
			</div>
			<div class="col-sm-6">
				<h2>Dashboards</h2>
				<p>Information about the gamification program will be everywhere thanks to the Dashboards.</p>
				<br/>
				<?= $this->Html->image('home/collaboration.png', array('style' => 'max-width: 100%', 'class' => 'img-polaroid'))?>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 hidden-xs hidden-sm">
		<div class="panel panel-info" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Have an account? <span style="color: black">Sign in</span></strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Player', array('action' => 'signin')); ?>
				<?= $this->Bootstrap->input('email', array('label' => false, 'placeholder' => 'E-mail', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('password', array('label' => false, 'placeholder' => 'Password (6-20 chars)', 'maxlength' => 20, 'class' => 'form-control')); ?>
				<button class="btn btn-md btn-block btn-info">Sign In</button>
				<?= $this->Bootstrap->end();?>
			</div>
		</div>
		<div class="panel panel-success" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>New to Agile Leagues? <span style="color: black">Sign up</span></strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Player', array('action' => 'signup')); ?>
				<?= $this->Bootstrap->input('name', array('autocomplete' => 'off', 'label' => false, 'placeholder' => 'Full Name', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('email', array('autocomplete' => 'off', 'label' => false, 'placeholder' => 'E-mail', 'class' => 'form-control')); ?>
				<?= $this->Bootstrap->input('password', array('autocomplete' => 'off', 'label' => false, 'placeholder' => 'Password (6-20 chars)', 'maxlength' => 20, 'class' => 'form-control')); ?>
				<button class="btn btn-md btn-block btn-success">Sign Up for Agile Leagues</button>
				<?= $this->Bootstrap->end();?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<?= $this->Html->image('home/hazard-360.jpg', array('class' => 'img-polaroid', 'style' => 'width: 100%'))?>
				<br/>
				<br/>
				<div class="alert alert-warning">
					<strong>Beware!</strong> This tool is so powerful that your teammates may become addicted.
					We recommend you to <a href="<?=$this->Html->url('/players/signup')?>">sign up</a> as soon as possible, otherwise they will hate you forever :)
				</div>

				<?= $this->Html->image('home/team-360.jpg', array('class' => 'img-polaroid', 'style' => 'width: 100%'))?>
			</div>
		</div>
	</div>
</div>
<br/>
<br/>
<div class="row">
	<div class="col-sm-3">
		<div class="tile-stats tile-primary">
			<div class="num">1 Billion</div>
			<p>of active digital game </p>
			<h3>players worldwide</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-primary">
			<div class="num" data-start="0" data-end="71" data-postfix="%" data-duration="1500" data-delay="0">71%</div>
			<p>of americans are <strong>not engaged</strong> in their jobs</p>
			<h3>= US$ 350 Billion loss</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-primary">
			<div class="num" data-start"0" data-end="92" data-delay="0" data-postfix="%" data-duration="1500" >92%</div>
			<p>of 2 year old children</p>
			<h3>are already players</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-primary">
			<div class="num" data-start"0" data-end="100" data-delay="0" data-postfix=" Mi" data-duration="1500" >100 Mi</div>
			<p>construction time of Wikipedia in hours</p>
			<h3>= 3 weeks played in Angry Birds</h3>
		</div>
	</div>
</div>


