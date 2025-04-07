<?php
// templates/Users/dashboard.php
?>
<h2>Welcome, <?= h($user->username) ?></h2>
<h3>Login History:</h3>
<ul>
<?php foreach ($loginHistory as $history): ?>
    <li><?= h($history->created) ?> from IP: <?= h($history->ip_address) ?></li>
<?php endforeach; ?>
</ul>