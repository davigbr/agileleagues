<style type="text/css">
	td {
		height: 50px;
	}
</style>
<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Active Events</h1>
        </div>
    </div>
    <? if (empty($activeEvents)): ?>
    	<div class="panel-body">
    		<p>No future events!</p>
    	</div>
    <?else:?>
	    <div class="panel-body with-table">
			<table class="table table-responsive table-striped table-bordered">
				<tr>
					<th>Name</th>
					<th class="visible-lg">Type</th>
					<th class="visible-lg">Start</th>
					<th class="visible-lg">End</th>
					<th>Joined</th>
					<th>Completed</th>
					<th style="text-align: center">Actions</th>
				</tr>
				<? foreach ($activeEvents as $event) : ?>
					<tr>
						<td>
							<a href="<? echo $this->Html->url('/events/details/' . $event['Event']['id']); ?>">
								<? echo h($event['Event']['name']); ?>
							</a>
						</td>
						<td class="visible-lg">
							<?if ($event['EventType']['id'] == EVENT_TYPE_MISSION): ?>
								<span class="label label-info">
									<strong><? echo h($event['EventType']['name']); ?></strong>
								</span>
							<?else: ?>
								<span class="label label-danger">
									<strong><? echo h($event['EventType']['name']); ?></strong>
								</span>
							<?endif;?>
						</td>
						<td class="visible-lg"><? echo $this->Format->date($event['Event']['start']); ?></td>
						<td class="visible-lg"><? echo $this->Format->date($event['Event']['end']); ?></td>
						<td>
							<? $joined = false; ?>
							<?foreach ($event['EventJoinLog'] as $joinLog): ?>
								<?
									if ($joinLog['player_id'] == $loggedPlayer['Player']['id']) {
										$joined = true;
									}
								?>
								<a href="<? echo $this->Html->url('/players/') ?>">
									<img 
										title="<? echo h($joinLog['Player']['name']); ?>" 
										alt="<? echo h($joinLog['Player']['name']); ?>" 
										src="<? echo $this->Gravatar->get($joinLog['Player']['email'], 32) ?>"/>
								</a>
							<?endforeach;?>
						</td>
						<td>
							<?foreach ($event['EventCompleteLog'] as $completeLog): ?>
								<a href="<? echo $this->Html->url('/players/') ?>">
									<img 
										title="<? echo h($completeLog['Player']['name']); ?>" 
										alt="<? echo h($joinLog['Player']['name']); ?>" 
										src="<? echo $this->Gravatar->get($joinLog['Player']['email'], 32) ?>"/>
								</a>
							<?endforeach;?>		
						</td>
						<td style="text-align: center;">
							<div class="btn-group">
								<?if ($isDeveloper): ?>
									<?if ($joined): ?>
										<a href="#" class="disabled btn btn-success btn-sm">
											<i class="glyphicon glyphicon-ok"></i> Joined
										</a>
									<?elseif ($event['EventType']['id'] == EVENT_TYPE_MISSION && $loggedPlayer['Player']['level'] >= EVENT_LEVEL_REQUIRED_MISSION): ?>
										<a href="<? echo $this->Html->url('/events/join/' . $event['Event']['id']); ?>" class="btn btn-info btn-sm">
											<i class="entypo-users"></i> Join
										</a>
									<?elseif ($event['EventType']['id'] == EVENT_TYPE_CHALLENGE && $loggedPlayer['Player']['level'] >= EVENT_LEVEL_REQUIRED_CHALLENGE): ?>
										<a href="<? echo $this->Html->url('/events/join/' . $event['Event']['id']); ?>" class="btn btn-info btn-sm">
											<i class="entypo-users"></i> Join
										</a>
									<?else:?>
										<a title="You must reach level <? echo $event['EventType']['level_required']?> to join <? echo $event['EventType']['name']?>s." 
											href="#" 
											class="disabled btn btn-danger btn-sm">
											<i class="entypo-lock"></i> Cannot Join
										</a>
									<?endif;?>
								<?endif;?>
								<?if ($isScrumMaster): ?>
									<a title="Edit" href="<? echo $this->Html->url('/events/edit/' . $event['Event']['id']); ?>" class="btn btn-primary btn-sm">
										<i class="glyphicon glyphicon-edit"></i>
									</a>
								<?endif;?>
								<a title="Details" href="<? echo $this->Html->url('/events/details/' . $event['Event']['id']); ?>" class="btn btn-primary btn-sm">
									<i class="fa fa-tasks"></i>
								</a>
							</div>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<?endif;?>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="panel panel-primary panel-table">
		    <div class="panel-heading">
		        <div class="panel-title">
		            <h3>Future Events</h3>
		        </div>
		    </div>
		    <? if (empty($futureEvents)): ?>
		    	<div class="panel-body">
		    		<p>No future events!</p>
		    	</div>
		    <?else:?>
			    <div class="panel-body with-table">
					<table class="table table-responsive table-striped table-bordered">
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Start</th>
							<th>End</th>
							<?if ($isScrumMaster): ?>
								<th style="text-align: center">Actions</th>
							<?endif;?>
						</tr>
						<? foreach ($futureEvents as $event) : ?>
							<tr>
								<td><? echo h($event['Event']['name']); ?></td>
								<td><? echo h($event['EventType']['name']); ?></td>
								<td><? echo $this->Format->date($event['Event']['start']); ?></td>
								<td><? echo $this->Format->date($event['Event']['end']); ?></td>
								<?if ($isScrumMaster): ?>
									<td style="text-align: center;">
										<div class="btn-group">
											<a href="<? echo $this->Html->url('/events/edit/' . $event['Event']['id']); ?>" class="btn btn-primary btn-sm">
												<i class="glyphicon glyphicon-edit"></i>
											</a>
										</div>
									</td>
								<?endif;?>	
							</tr>
						<? endforeach; ?>
					</table>
				</div>
			<?endif;?>
		</div>	
	</div>
	<div class="col-md-6">
		<div class="panel panel-light panel-primary panel-table">
		    <div class="panel-heading">
		        <div class="panel-title">
		            <h3>Past Events</h3>
		        </div>
		    </div>
		    <? if (empty($pastEvents)): ?>
		    	<div class="panel-body">
		    		<p>No past events!</p>
		    	</div>
		    <?else:?>
			    <div class="panel-body with-table">
					<table class="table table-responsive table-striped table-bordered">
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Start</th>
							<th>End</th>
							<th>Completed</th>
						</tr>
						<? foreach ($pastEvents as $event) : ?>
							<tr class="disabled">
								<td><? echo h($event['Event']['name']); ?></td>
								<td><? echo h($event['EventType']['name']); ?></td>
								<td><? echo $this->Format->date($event['Event']['start']); ?></td>
								<td><? echo $this->Format->date($event['Event']['end']); ?></td>
								<td>
									<?foreach ($event['EventCompleteLog'] as $completeLog): ?>
										<a href="<? echo $this->Html->url('/players/') ?>">
											<img 
												title="<? echo h($completeLog['Player']['name']); ?>" 
												alt="<? echo h($joinLog['Player']['name']); ?>" 
												src="<? echo $this->Gravatar->get($joinLog['Player']['email'], 32) ?>"/>
										</a>
									<?endforeach;?>		
								</td>													
							</tr>
						<? endforeach; ?>
					</table>
				</div>
			<?endif;?>
		</div>		
	</div>
</div>
