<?= $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
<?= $this->Bootstrap->input('description', array('class' => 'form-control')); ?>
<?= $this->Bootstrap->input('bonus_type', array('class' => 'form-control', 'options' => array(
	'+' => '+',
	'%' => '%'
))); ?>
<?= $this->Bootstrap->input('bonus_value', array('class' => 'form-control', 'data-mask' => 'decimal', 'type' => 'text', 'maxlength' => 5)); ?>
<?= $this->Bootstrap->input('color', array('type' => 'text', 'class' => 'form-control', 'placeholder' => '#ff0088', 'maxlength' => 7, 'pattern' => '\\#[0-9A-Fa-f]*')); ?>
<?= $this->Bootstrap->input('new'); ?>
<br/>
<button type="submit" class="btn btn-lg btn-success">Save</button>
<?= $this->Bootstrap->end(); ?>