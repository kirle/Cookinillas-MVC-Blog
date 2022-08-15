
<?php
//file: view/users/login.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$view->setVariable("title", "Login");
$errors = $view->getVariable("errors");
?>

<!--div for errors-->
<?php if (isset($errors)): ?>
		<div class="alert alert-danger text-center " role="alert">
				<?php foreach ($errors as $error): ?>
					<?= $error ?>
				<?php endforeach; ?>
		</div>
	<?php endif; ?>


<div class="container">
		<div class="text-center ">
			<form class="form-signin " action="index.php?controller=users&amp;action=login" method="POST">
				<img class="mb-4" src="static/images/cheficon.png" alt="" width="72" height="72">
				<h1 class="h3 mb-3 font-weight-normal"><?= i18n("Please sign in")?>:</h1>
				
				<label for="name" class="sr-only w-50"><?= i18n("Alias")?></label>
				<input type="name" id="alias" name="alias" class="form-control w-50 center-block" placeholder="<?= i18n("Alias")?>" required="" autofocus="">
				<label for="password" class="sr-only"><?= i18n("Password")?>:</label>
				<input type="password" id="passwd" name="passwd" class="form-control w-50 center-block" placeholder="<?= i18n("Password")?>" required="">
				<div class="checkbox mb-3">
					<label>
					<input type="checkbox" value="remember-me"> <?= i18n("Remember me")?>:
					</label>
				</div>
				<button class="btn btn-lg btn-primary w-30 text-center" type="submit"><?= i18n("Sign in")?></button>
				<p class="mt-5 mb-3 text-muted">© 2021-2022</p>

				<p><?= i18n("Not user?")?> <a href="index.php?controller=users&amp;action=register"><?= i18n("Register here!")?></a></p>

			</form>
			<button class="btn btn-lg btn-light text-center" onclick="location.href='index.php?controller=recipes&amp;action=index'"> <?= i18n("Back to main page")?></button>

		</div>
</div>








