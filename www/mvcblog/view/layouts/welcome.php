<?php
// file: view/layouts/welcome.php

$view = ViewManager::getInstance();

?><!DOCTYPE html>
<html>
<head>

	<!--bootstrap--> 
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<title><?= $view->getVariable("title", "no title") ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="css/login.css" type="text/css">
	<?= $view->getFragment("css") ?>
	<?= $view->getFragment("javascript") ?>
	
</head>
<body class="mask d-flex align-items-center h-100">
<div class="container-fluid login_container">
				
				<header >
				<h1 class="h1 text-center"><?= i18n("Welcome to Cookinillas") ?>!</h1>
				</header>		

				<main id="article" >
				<div id="flash">
					<div id="hidden">					
						<?= $message = $view->popFlash(); ?>
					</div>
					<!--div for errors and confirmations-->
					
					<?php if ($message == "userAddedOk"): ?>
						<div class="alert alert-success" role="alert">
							<?= i18n("User register	 succesfully"); ?>
						</div>
					<?php endif; ?>
	
				</div>
				<?= $view->getFragment(ViewManager::DEFAULT_FRAGMENT) ?>
			</main>

				<footer>
				<?php
				include(__DIR__."/language_select_element.php");
				?>
				</footer>
</div>
	
</body>
</html>
