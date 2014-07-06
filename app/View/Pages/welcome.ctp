<style type="text/css">
	body, p, blockquote small { font-size: 14px; }
	#responsabilities li a { text-decoration: underline; }
	p {
		text-align: justify;
	}
</style>

<h1><?=__('Welcome to Agile Leagues!')?></h1>

<p>You have signed up as a <strong>Game Master</strong>.</p>

<p>You are responsible for:</p>
<ul id="responsabilities">
	<li>Creating <a href="#teams">Teams</a> and inviting <a href="#players">Players</a>;</li>
	<li>Registering <a href="#activities">Activities</a> and grouping them into <a href="#domains">Domains</a>;</li>
	<li>Defining <a href="#tags">Tags</a> that can be linked to the activities;</li>
	<li>Monitoring the progress of the game through the <a href="#dashboards">Dashboards</a>.</li>
</ul>
<div class="row">
	<div class="col-md-4">
		<h2 id="teams"><a target="_blank" href="<?= $this->Html->url('/teams')?>">Teams</a></h2>
		<p>
			Teams are group of players that work together. 
			Agile Leagues encourages collaboration between team members and competition (optional) across different teams.
			Teams can be functional or cross-functional.
		</p>
	</div>
	<div class="col-md-4">
		<h2 id="players"><a target="_blank" href="<?= $this->Html->url('/players')?>">Players</a></h2>
		<p>
			They are the players of the gamification program and they will report activities, earn badges, complete missions, and more. 
		</p>
	</div>
	<div class="col-md-4">
		<h2 id="activities"><a target="_blank" href="<?= $this->Html->url('/activities')?>">Activities</a></h2>
		<p>
			Define some cool activities that your players are going to perform. 
			They will be rewarded with Experience Points after completing them. 
		</p>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<h2 id="domains"><a target="_blank" href="<?= $this->Html->url('/domains')?>">Domains</a></h2>
		<p>
			Domains are areas of knowledge or groups of technical skills that you want to boost. 
			Use them as a way of grouping activities into categories.
		</p>
	</div>
	<div class="col-md-4">
		<h2 id="tags"><a target="_blank" href="<?= $this->Html->url('/tags')?>">Tags</a></h2>
		<p>Tags are activity modifiers. You have to create tags so that the players can choose them while reporting.</p>
	</div>
	<div class="col-md-4">
		<h2 id="dashboards"><a target="_blank" href="<?= $this->Html->url('/dashboards')?>">Dashboards</a></h2>
		<p>
			The Dashboards provide different views of your Gamification Program for you and your players.
			For example, within the Dashboards you can find some Leaderboards.
		</p>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h1>Peer Activity Review</h1>
		<p>
			In order to prevent cheating and ensuring that the activities reported are being executed as agreed, Agile Leagues has a built-in activity review mechanism.
			Because we value self-organization and believe that it is best for knowledge workers, the players themselves are responsible for reviewing the activities. 
			As a <strong>Game Master</strong>, you only need to define the activities (including their description) and the votes needed for acceptance or rejection.
		</p>

		If you want to learn more about Peer Activity Review, take a look at:
		<br/>
		<br/>
		<a target="_blank" href="http://www.agilegamification.org/peer-activity-review/">www.agilegamification.org/peer-activity-review/</a>
	</div>
</div>
<!-- <h1>Example: Software Development Team</h1> -->

<p>If you have doubts, please do not hesitate to contact us: <a href="mailto:contact@agileleagues.com">contact@agileleagues.com</a>.</p>
