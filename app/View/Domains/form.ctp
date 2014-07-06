<?= $this->Bootstrap->input('name', array('placeholder' => 'The name of the domain', 'type' => 'text', 'class' => 'form-control')); ?>
<?= $this->Bootstrap->input('description', array('placeholder' => 'A meaningful description', 'class' => 'form-control')); ?>
<?= $this->Bootstrap->input('abbr', array('label' => 'Abbreviation', 'placeholder' => '3 characters', 'type' => 'text', 'class' => 'form-control', 'maxlength' => 3)); ?>
<?= $this->Bootstrap->input('color', array('type' => 'text', 'class' => 'form-control', 'placeholder' => '#ff0088', 'maxlength' => 7, 'pattern' => '\\#[0-9A-Fa-f]*')); ?>
<?= $this->Bootstrap->input('icon', array('type' => 'text', 'readonly' => 'readonly', 'class' => 'form-control', 'label' => 'Icon (choose one below)')); ?>
<br/>
<button type="submit" class="btn btn-lg btn-success">Save</button>
