<h1><?= h($type) ?></h1>
<?if ($type === 'Bug'): ?>
	<p>Where: <?= h($data['where'])?></p>
<?endif;?>
<p>Description: <?= h($data['description'])?></p>
<p>Comments: <?= h($data['comments'])?></p>

<h2>From: <?= $user['name']?></h2>
<p>E-mail: <?= $user['email']?></p>