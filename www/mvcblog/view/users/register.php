<?php
//file: view/users/register.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$errors = $view->getVariable("errors");
$user = $view->getVariable("user");
$view->setVariable("title", "Register");
?>

<div class="container">
		<div class="text-center ">
			<form class="form-signin " action="index.php?controller=users&amp;action=register" method="POST">
				<img class="mb-4" src="static/images/cheficon.png" alt="" width="72" height="72">
				<h1 class="h3 mb-3 font-weight-normal"><?= i18n("Register")?>:</h1>
				
				<!--email--> 
				<label for="email" class="sr-only w-50"><?= i18n("Alias")?></label>
				<input type="name" id="email" name="email" class="form-control w-50 center-block" placeholder="<?= i18n("Email")?>" required="" value ="<?= $user->getEmail() ?>" autofocus="">
				<?php
				if (isset($errors["email"])) { ?>
					<span class="help-block"><?php echo i18n($errors["email"]) ?></span>
				<?php } ?>

				<!--alias-->
				<label for="alias" class="sr-only w-50"><?= i18n("Alias")?></label>
				<input type="name" id="alias" name="alias" value="<?= $user->getAlias() ?>" class="form-control w-50 center-block" placeholder="<?= i18n("Alias")?>" required="" autofocus="">
				<?php
				if (isset($errors["alias"])) { ?>
					<span class="help-block"><?php echo i18n($errors["alias"]) ?></span>
				<?php } ?>

				<label for="password" class="sr-only"><?= i18n("Password")?>:</label>
				<input type="password" id="passwd" name="passwd" class="form-control w-50 center-block" placeholder="<?= i18n("Password")?>" required="">
				<?php
				if (isset($errors["passwd"])) { ?>
					<span class="help-block"><?php echo i18n($errors["passwd"]) ?></span>
				<?php } ?>

				<div class="checkbox mb-3">
					<label>
					<input type="checkbox" value="remember-me"> <?= i18n("Remember me")?>:
					</label>
				</div>
				<button class="btn btn-lg btn-primary w-30 text-center mb-3" type="submit"><?= i18n("Register")?></button>
				<!-- back button -->
				

			</form>
			&nbsp;&nbsp;
			<a class="" href="index.php?controller=users&amp;action=login"><?= i18n("¿Already register? Log in") ?></a>
			<p class="mt-3 mb-3 text-muted">© 2021-2022</p>

		</div>
</div>