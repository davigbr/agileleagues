<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>My Account</strong></div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-3" style="text-align: center">
						<img src="<? echo $this->Gravatar->get($loggedPlayer['Player']['email'], 200) ?>" alt="" class="img-thumbnail"/>
						<br/>
						<br/>
						<p>
							<strong><? echo $loggedPlayer['Player']['name']?>, <? echo $loggedPlayer['Player']['title']?></strong>
						</p>
						<p>
							<small><a target="_blank" href="http://gravatar.com">Change your picture at gravatar.com</a></small>
						</p>
					</div>
					<div class="col-sm-9">
						<? echo $this->Bootstrap->create('Player'); ?>
						<? echo $this->Bootstrap->hidden('id'); ?>
						<? echo $this->Bootstrap->input('name', array('autocomplete' => 'off', 'type' => 'text', 'class' => 'form-control')); ?>
						<? echo $this->Bootstrap->input('email', array('autocomplete' => 'off', 'type' => 'email', 'class' => 'form-control')); ?>
						<? echo $this->Bootstrap->input('password', array('autocomplete' => 'off', 'type' => 'password', 'class' => 'form-control')); ?>
						<? echo $this->Bootstrap->input('repeat_password', array('type' => 'password', 'class' => 'form-control')); ?>
						<button type="submit" class="btn btn-success">Save</button>
					</div>
				</div>
				<? echo $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>


	