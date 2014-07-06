<?
	$tags = isset($tags)? $tags : array();
	if (isset($tag)) {
		$tags = array(isset($tag['Tag']) ? $tag['Tag'] : $tag);
	}
?>
<?foreach ($tags as $tag) :
	?><span 
		data-toggle="tooltip" 
		data-placement="top" 
		title="<?= h($tag['description']? $tag['description'] : $tag['name']) ?>"
		class="label tag" 
		style="background-color: <?= h($tag['color'])?>">
		<?= h($tag['name']) ?>
	</span><?
endforeach;?>