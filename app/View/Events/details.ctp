<div class="row">
    <div class="col-sm-12">
        <div class="well">
            <h1>
                <? echo h($event['EventType']['name']) ?>: <? echo h($event['Event']['name']) ?>
                <?if ($eventCompleted): ?>
                    <a class="pull-right btn btn-success btn-lg disabled" title="You have already completed this event." href="#"><i class="glyphicon glyphicon-ok"></i> Completed</a>
                <?elseif ($event['Event']['progress'] == 100): ?>
                    <a class="pull-right btn btn-success btn-lg" href="<? echo $this->Html->url('/events/complete/' . $event['Event']['id']); ?>"<i class="glyphicon glyphicon-ok"></i> Complete Event</a>
                <?endif;?>
            </h1>
            <p><? echo h($event['Event']['description']) ?></p>
            <br/>
            <div class="progress">
                <div class="progress-bar progress-bar-success" style="width: <? echo $event['Event']['progress']?>%">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
         <div class="panel panel-primary panel-table">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Tasks</h3>
                    <span>Tasks that need to be executed to complete this <? echo h($event['EventType']['name']); ?>.</span>    
                </div>
            </div>
            <div class="panel-body with-table">
                <?if (empty($event['EventTask'])): ?>
                    <table class="table table-responsive"><tr><td><p>No tasks required!</p></td></tr></table>
                <?else:?>
                    <table class="table table-responsive">
                        <tr>
                            <th>Completed?</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>XP</th>
                        </tr>
                        <?foreach($event['EventTask'] as $task): ?>
                            <tr>    
                                <td>
                                    <?if ($task['completed']): ?>
                                        <i style="color: green" class="glyphicon glyphicon-ok"></i>
                                    <?else:?>
                                        <i style="color: red" class="glyphicon glyphicon-remove"></i>
                                    <?endif;?>
                                </td>
                                <td><? echo h($task['name']); ?></td>
                                <td><? echo h($task['description']); ?></td>
                                <td><? echo h($task['xp']); ?></td>
                            </tr>
                        <?endforeach;?>
                    </table>
                <?endif;?>
            </div>
        </div>
    </div>
	<div class="col-md-6">
    	 <div class="panel panel-primary panel-table">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Activities</h3>
                    <span>Activities that need to be executed to complete this <? echo h($event['EventType']['name']); ?>.</span>    
                </div>
            </div>
            <div class="panel-body with-table">
            	<?if (empty($event['EventActivity'])): ?>
                    <table class="table table-responsive"><tr><td><p>No activities required!</p></td></tr></table>
            	<?else:?>
	            	<table class="table table-responsive">
	            		<tr>
                            <th style="text-align: center" title="Domain">D</th>
                            <th>Name</th>
                            <th>Times Required / Completed</th>
                            <th>Progress</th>
	            		</tr>
            			<?foreach($event['EventActivity'] as $activity): ?>
                  
                            <tr>
                                <td style="text-align: center; color: white; width: 35px; background-color: <? echo h($activity['Activity']['Domain']['color']); ?>">
                                    <i class="<? echo h($activity['Activity']['Domain']['icon']); ?>"></i>
                                </td>                             
                                <td><? echo h($activity['Activity']['name']); ?></td>                             
                                <td>
                                    <? echo number_format($activity['obtained']) ?> /
                                    <? echo number_format($activity['count']); ?>
                                    (<? echo number_format($activity['progress']) ?>%)

                                </td>                             
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: <? echo $activity['progress']?>%">
                                        </div>
                                    </div>
                                </td>                             
            				</tr>
            			<?endforeach;?>
	            	</table>
            	<?endif;?>
            </div>
        </div>
    </div>
</div>
<br/>
<a class="btn btn-primary btn-lg" href="<? echo $this->Html->url('/events'); ?>">Back to Events</a>
