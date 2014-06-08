<h2>Welcome to Agile Leagues</h2>

<p>You have signed up as <?= $name ?> and we are just waiting you to verify your account by clicking on the link below:</p>

<a href="<?= Router::url('/players/join/' . $hash, true)?>"><?= Router::url('/players/join/' . $hash, true)?></a>