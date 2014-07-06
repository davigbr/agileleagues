<style type="text/css">
	h3 a { text-decoration: none; color: white;}
	h3 a:hover { text-decoration: none; color: white;}
</style>

<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Dashboards</h1>
        </div>
    </div>
</div>
<div class="row">
	<? if ($isPlayer): ?>
	    <div class="col-sm-2">
			<div class="tile-title tile-primary">
				<div class="icon">
					<a href="<?= $this->Html->url('/dashboards/activities')?>"><i class="entypo-flag"></i></a>
				</div>
				<div class="title">
					<h3><a href="<?= $this->Html->url('/dashboards/activities')?>">Activities</a></h3>
					<p></p>
				</div>
			</div>
		</div>
	<?endif;?>
    <div class="col-sm-2">
		<div class="tile-title tile-primary">
			<div class="icon">
				<a href="<?= $this->Html->url('/dashboards/players')?>"><i class="entypo-user"></i></a>
			</div>
			<div class="title">
				<h3><a href="<?= $this->Html->url('/dashboards/players')?>">Players</a></h3>
				<p></p>
			</div>
		</div>
	</div>
    <div class="col-sm-2">
		<div class="tile-title tile-primary">
			<div class="icon">
				<a href="<?= $this->Html->url('/dashboards/leaderboards')?>"><i class="entypo-star"></i></a>
			</div>
			<div class="title">
				<h3><a href="<?= $this->Html->url('/dashboards/leaderboards')?>">Leaderboards</a></h3>
				<p></p>
			</div>
		</div>
	</div>
 </div>
