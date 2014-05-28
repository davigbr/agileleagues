<?= $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
<?= $this->Bootstrap->input('description', array('class' => 'form-control')); ?>
<?= $this->Bootstrap->input('abbr', array('type' => 'text', 'class' => 'form-control', 'maxlength' => 3)); ?>
<?= $this->Bootstrap->input('color', array('type' => 'text', 'class' => 'form-control', 'placeholder' => '#ff0088', 'maxlength' => 7, 'pattern' => '\\#[0-9A-Fa-f]*')); ?>
<?= $this->Bootstrap->input('icon', array('type' => 'text', 'class' => 'form-control', 'label' => 'Icon (choose one below)')); ?>
<?= $this->Bootstrap->input('player_type_id', array('options' => $playerTypes, 'empty' => '-', 'class' => 'form-control')); ?>
<br/>
<button type="submit" class="btn btn-lg btn-success">Save</button>
