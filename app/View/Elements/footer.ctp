<style type="text/css">
	.addthis_toolbox {
		display: inline-block;
	}
	.addthis_label {
		display: inline-block;
		height: 32px;
		font-height: 32px;
		vertical-align: middle;
		margin-bottom: 7px;
		margin-right: 5px;
		font-weight: bold;
	}
</style>
<footer class="main">
	&copy; 2013 Davi Gabriel da Silva - <strong><a href="<?= Router::url('/')?>">Agile Leagues</a></strong> - Agile Software Development Gamification Tool</a><br/>
	<br/>
	<a target="_blank" href="http://www.agilegamification.org/"><i class="fa fa-globe"></i> Agile Gamification: Gamification applied to Agile Software Development</a>
	<br/>
	<a target="_blank" href="https://github.com/davigbr/agileleagues"><i class="fa fa-github"></i> GitHub: Agile Leagues is open-source and licensed under Apache 2.0</a>
	<br/>
	<a target="_blank" href="https://trello.com/b/0Y6rJLQB"><i class="fa fa-trello"></i> Trello: vote on features</a>
	<br/><br/>
	<div class="visible-lg visible-md">
		<a target="_blank" href="http://www.agilegamification.org/gamification-of-scrum/" class="btn btn-md btn-primary">Gamification of Scrum</a>
		<a target="_blank" href="http://www.agilegamification.org/gamification-of-xp/" class="btn btn-md btn-primary">Gamification of Extreme Programming</a>
		<a target="_blank" href="https://github.com/davigbr/agileleagues" class="btn btn-md btn-blue"><?= Configure::read('version') ?></a>
	</div>
	<br/>
	<em>Our goal: Help self-organized creative teams drive behaviors, amplify feedback, and increase progress visibility.</em>
	<br/><br/>	
	<!-- AddThis Button BEGIN -->
	<div class="addthis_label">Share</div>
	<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript">
		var addthis_config = {'data_track_addressbar' : false};
		var addthis_share = {'url' : 'http://www.agileleagues.com/'}
	</script>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51a77d7207c7bac6"></script>
	<!-- AddThis Button END -->
	<div class="addthis_label">&nbsp;&nbsp;&nbsp;Follow Us</div>
	<!-- AddThis Follow BEGIN -->
	<div class="addthis_toolbox addthis_32x32_style addthis_default_style">
		<a class="addthis_button_facebook_follow" addthis:userid="AgileLeagues"></a>
		<a class="addthis_button_twitter_follow" addthis:userid="davigbr"></a>
		<a class="addthis_button_linkedin_follow" addthis:userid="davigbr"></a>
		<a class="addthis_button_youtube_follow" addthis:userid="davigbr123"></a>
	</div>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51a77d7207c7bac6"></script>
	<!-- AddThis Follow END -->
</footer>

<?php echo $this->element('sql_dump'); ?>