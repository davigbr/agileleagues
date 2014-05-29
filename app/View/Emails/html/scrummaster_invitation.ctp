<h2>Welcome to Agile Leagues</h2>

<p><?= h($scrumMasterName);?> invites you to join the team <strong><?= h($teamName) ?></strong>!</p>

<h4><a href="<?= Router::url('/players/join/' . $hash, true)?>">Click here to JOIN!</a></h4>

<p>What's Agile Leagues? </p>
<p>It's the easiest and most funny way to practice Scrum and Extreme Programming.</p>
<a href="<?= Router::url('/', true)?>"> Learn more » </a>