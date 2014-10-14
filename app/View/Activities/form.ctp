<?= $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
<?= $this->Bootstrap->input('description', array('class' => 'form-control')); ?>
<?= $this->Bootstrap->input('details', array('class' => 'form-control')); ?>
<?= $this->Bootstrap->input('restrictions', array('class' => 'form-control')); ?>
<?= $this->Bootstrap->input('xp', array('maxLength' => 4, 'placeholder' => 'Ammount of experience points the players will earn when this activity is accepted.', 'data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
<?= $this->Bootstrap->input('acceptance_votes', array('maxLength' => 3, 'placeholder' => 'Number of votes needed from the players for this activity to be considered accepted.', 'data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
<?= $this->Bootstrap->input('rejection_votes', array('maxLength' => 3, 'placeholder' => 'Number of votes needed from the players for this activity to be considered rejected.', 'data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
<?= $this->Bootstrap->input('daily_limit', array('maxLength' => 3, 'placeholder' => 'Maximum number of times that this activity can be reported per day per player. Zero means no limit.', 'data-mask' => 'decimal', 'type'=>'text', 'class'=>'form-control')); ?>
<?= $this->Bootstrap->input('new'); ?>
