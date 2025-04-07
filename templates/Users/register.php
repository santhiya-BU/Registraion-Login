<?php
// templates/Users/register.php
?>
<h1>Register</h1>
<?= $this->Form->create($user) ?>
<?= $this->Form->control('username') ?>
<?= $this->Form->control('name') ?>
<?= $this->Form->control('email_id') ?>
<?= $this->Form->control('password') ?>
<?= $this->Form->button('Register') ?>
<?= $this->Form->end() ?>