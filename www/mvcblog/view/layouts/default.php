<?php
//file: view/layouts/default.php
require_once(__DIR__."/../../core/ViewManager.php");

$view = ViewManager::getInstance();
$currentuser = $view->getVariable("currentusername");

?><!DOCTYPE html>
<html>
<head>
	<title><?= $view->getVariable("title", "no title") ?></title>
	<meta charset="utf-8">

	<!--include bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<!--include select2-->
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<!--fonts--> 
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" integrity="sha256-46r060N2LrChLLb5zowXQ72/iKKNiw/lAmygmHExk/o=" crossorigin="anonymous" />


	<link rel="stylesheet" href="css/style.css" type="text/css">
	<!-- enable ji18n() javascript function to translate inside your scripts -->
	<script type="text/javascript" src="index.php?controller=language&amp;action=i18njs">
	</script>

	<script src="js/script.js"></script>

	<?= $view->getFragment("css") ?>
	<?= $view->getFragment("javascript") ?>
</head>
<body>


	<div class="container-fluid main_container">
		<!-- Header -->
		<div class="inner-main-header container-fluid" id="header">
						<div class="text-center"><h1>Cookinillas</h1></div>

						<nav id="nav ">
							<div class="container-fluid text-center" id="nav-container"> 
							<a class="glyphicon glyphicon-apple  text-center" href="index.php?controller=recipes&amp;action=index"><?= i18n("Recipes")?></a>			

							<?php if (isset($currentuser)): ?>
								<!-- <?= sprintf(i18n("Hello %s"), $currentuser) ?> -->
									
								<a class="glyphicon glyphicon-user  text-center" href="index.php?controller=recipes&amp;action=getOwnRecipes"><?= i18n("My recipes")?></a> 
								<a class="glyphicon glyphicon-bookmark  text-center" href="index.php?controller=recipes&amp;action=home"><?= i18n("Home")?></a>
								<a 	class="glyphicon glyphicon-log-out  text-center" href="index.php?controller=users&amp;action=logout"><?php echo i18n("Logout") ?> (<?php echo $currentuser ?>)</a>

								


							<?php else: ?>
								
								<a class="glyphicon glyphicon-log-in"  href="index.php?controller=users&amp;action=login"
								> <?= i18n("Login") ?></a>
							<?php endif ?>
							</div>
						</nav>
		</div>
		<!-- /Header -->

				
		<!-- Body -->
			<!--main area-->
			<main id="article" >
				<div id="flash">
					<div id="hidden">					
						<?= $message = $view->popFlash(); ?>
					</div>
					<!--div for errors and confirmations-->
					<?php if ($message == "deletedOk"): ?>
						<div class="alert alert-success" role="alert">
							<?= i18n("Recipe deleted succesfully"); ?>
						</div>
					<?php endif; ?>
					<?php if ($message == "deletedError"): ?>
						<div class="alert alert-danger" role="alert">
							<?= i18n("Error deleting recipe"); ?>
						</div>
					<?php endif; ?>
					<?php if ($message == "addedOk"): ?>
						<div class="alert alert-success" role="alert">
							<?= i18n("Recipe added succesfully"); ?>
						</div>
					<?php endif; ?>
					<?php if ($message == "addedError"): ?>
						<div class="alert alert-danger" role="alert">
							<?= i18n("Error adding recipe"); ?>
						</div>
					<?php endif; ?>
					<?php if ($message == "updatedOk"): ?>
						<div class="alert alert-success" role="alert">
							<?= i18n("Recipe updated succesfully"); ?>
						</div>
					<?php endif; ?>
					<?php if ($message == "addedFavOk"): ?>
						<div class="alert alert-success" role="alert">
							<?= i18n("Fav updated succesfully"); ?>
						</div>
					<?php endif; ?>
					

					<?php if ($message == "deletedFavOk"): ?>
						<div class="alert alert-success" role="alert">
							<?= i18n("Deleted from favourites"); ?>
						</div>
					<?php endif; ?>

				</div>
				<?= $view->getFragment(ViewManager::DEFAULT_FRAGMENT) ?>
			</main>
		<!-- /Body -->

		<!--Footer-->
		<footer id="footer">
			<?php
			include(__DIR__."/language_select_element.php");
			?>
		</footer>				
	</div>
		
</body>
</html>
