<span 
	data-toggle="tooltip" 
	data-placement="top" 
	title="<?= h($tag['Tag']['description']? $tag['Tag']['description'] : $tag['Tag']['name']) ?>"
	class="label" 
	style="background-color: <?= h($tag['Tag']['color'])?>">
	<?= h($tag['Tag']['name']) ?>
</span>
