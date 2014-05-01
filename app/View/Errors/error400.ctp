<div class="main-content">
	<div class="page-error-404">
		<div class="error-symbol">
			<i class="entypo-attention"></i>
		</div>
		<div class="error-text">
			<h2>404</h2>
			<p>Resource not found</p>
		</div>
		<p class="error">
			<?php printf(
				__d('cake', 'The requested address %s was not found on this server.'),
				"<strong>'{$url}'</strong>"
			); ?>
		</p>
		<?php
		if (Configure::read('debug') > 0):
			echo $this->element('exception_stack_trace');
		endif;
		?>
	</div>
</div>
