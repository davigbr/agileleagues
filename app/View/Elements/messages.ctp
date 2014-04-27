<? $success = $this->Session->flash(); ?>
<? if ($success) : ?>
	<div class="alert alert-success"><? echo $success?></div>
<? endif; ?>
<? $error = $this->Session->flash('error'); ?>
<? if ($error) : ?>
	<div class="alert alert-danger"><? echo $error?></div>
<? endif; ?>
