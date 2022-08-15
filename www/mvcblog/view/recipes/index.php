<?php
//file: view/recipes/index.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$recipes = $view->getVariable("recipes");
$currentuser = $view->getVariable("currentusername");
$errors = $view->getVariable("errors");
$favs = $view->getVariable("favs");
$view->setVariable("title", "Recipes");

?>

<!--Title and create action --> 

	<div class="row inner-main-header container-fluid main-page-header" id="header" >
		<div class="col-md-3" id="headerContainerLeft">

		<form action="index.php?controller=recipes&amp;action=search" method="POST">
			<div class="form-check" id="select-forms">
				<input class="form-check-input" type="radio" name="search_action" value="searchByName" checked>
				<label class="form-check-label" for="flexRadioDefault1">
					<?= i18n("Search by name") ?>
				</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="search_action" value="searchByIngredient" >
				<label class="form-check-label" for="flexRadioDefault2">
				<?= i18n("Search by ingredient") ?>
				</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="search_action" value="searchByAuthor" >
				<label class="form-check-label" for="flexRadioDefault3">
				<?= i18n("Search by author") ?>
				</label>
			</div>
		
		
			<!--search recipe part -->
			<input type="text" id="searchLabel" name="ingredient" class="form-control text-center w-50" placeholder="<?=i18n("Search recipe")?>" />
			
			
		</form>	

		</div>

		<!-- title -->
		<div class="col-md-6" id="headerContainerCenter">		
			<h1><?=i18n("Recipes")?></h1> 
		</div>

		<!--create recipe button-->
		<div class="col-md-3" id="headerContainerRight"><?php if (isset($currentuser)): ?>
			<h1><a class="btn btn-primary" href="index.php?controller=recipes&amp;action=add"><?= i18n("Create recipe") ?></a></h1> 
			
		<?php endif; ?></div>

	</div>	
		


<!--Main body-->

<div class="inner-main-body p-2 forum-content show" id="mainBody">

	<!--div for errors-->
	<?php if (isset($errors)): ?>
		<div class="alert alert-danger" role="alert">
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?= $error ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<!-- cards-container -->
	<div class="container-fluid" id="cards-container">


	<?php foreach ($recipes as $recipe): ?>
		<div class="card" id="card"  >
            <div class="row no-gutters" id="recipe-card" >
                <div id="card-content" onclick="location.href='index.php?controller=recipes&amp;action=view&amp;id=<?= $recipe->getId() ?>'">
					<!--image-->
					<div class="col-sm-3 img-fluid" id="imageContainer">
						<?php 
							if ($recipe->getImageUrl() != null) { ?>
								<img class="card-img" width="50%" height="50%"
								src="<?= htmlentities($recipe->getImageUrl()) ?>" alt="<?=
								htmlentities($recipe->getTitle()) ?>">
							<?php 
							} else { ?> 
								<img class="card-img " width="50%" height="40%"
								src="static/images/default.png" alt="<?=
								htmlentities($recipe->getTitle()) ?>">
							<?php
							} ?>
					</div>
					<!--info-->
					<div class="col-sm-8">
						<div class="card-body">
							<!--title-->
							<h5 class="card-title">
								<a href="index.php?controller=recipes&amp;action=view&amp;id=<?= $recipe->getId() ?>"><?= htmlentities($recipe->getTitle()) ?></a>
							</h5>
							<!--description-->
							<p class="card-text"><?= htmlentities($recipe->getContent()) ?></p>

							<!--date and author-->
							<p class="card-text"><small class="text-muted">
								<?= i18n("Created on")?> <?= htmlentities($recipe->getDate()) ?> <?= i18n("by") ?> <?= htmlentities($recipe->getAuthor()->getAlias()) ?>
							<p class="card-text">
						
						
				
						</div>
					</div>
				</div> <!-- card container left -->
				
				<!--Ingredients-->
				<div class="col-sm-1">
					<div class="container-fluid">
						<div class="d-flex flex-row justify-content-center " id="card-actions">
								<!-- acciones -->
								<a class="glyphicon glyphicon-eye-open fas fa-camera fa-2x center-block" href="index.php?controller=recipes&amp;action=view&amp;id=<?= $recipe->getId() ?>" 
								></a>

								
								<?php
								//show actions ONLY for the author of the recipe (if logged)
								if (isset($currentuser) ){ ?>

									<div id="fav-count-container">
										<?php 
										if (in_array($recipe->getId(), $favs )){?>
											<a class="glyphicon glyphicon-star	 fas fa-camera fa-2x center-block"  href="index.php?controller=recipes&amp;action=deleteFromFavourites&amp;id=<?= $recipe->getId() ?>"></a>				

										<?php } else { ?>
											<a class="glyphicon glyphicon-star-empty	 fas fa-camera fa-2x center-block"  href="index.php?controller=recipes&amp;action=addToFavourites&amp;id=<?= $recipe->getId() ?>"></a>				
										<?php } 
										?>
										<h3 id="favCount"> <?php echo $recipe->getFavs(); ?> </h3>
										
									</div>
								
								
								<?php if($currentuser == $recipe->getAuthor()->getAlias()){ ?>
									<?php
									// 'Edit Button'
									?>
									<a class="glyphicon glyphicon-edit fas fa-camera fa-2x center-block" href="index.php?controller=recipes&amp;action=edit&amp;id=<?= $recipe->getId() ?>"> </a>


								<?php
								// 'Delete Button'
								?>
								<form method="POST" action="index.php?controller=recipes&amp;action=delete"
									id="delete_recipe_<?= $recipe->getId(); ?>"
									style="display: inline"
									>

									<input type="hidden" name="id" value="<?= $recipe->getId() ?>">

									<a class="glyphicon glyphicon-remove fas fa-camera fa-2x center-block" href="#" 
									onclick="
									if (confirm('<?= i18n("are you sure?")?>')) {
										document.getElementById('delete_recipe_<?= $recipe->getId() ?>').submit()
									}"
									></a>
								</form>
								<?php } ?>
									
									
									
								<?php }else{ ?>
									<div id="fav-count-container">
										<a class="glyphicon glyphicon-star-empty fas fa-camera fa-2x center-block" onclick="alert('<?php echo i18n('Must login to do this') ?>' )"  ></a>				

										<h3 id="favCount"> <?php echo $recipe->getFavs(); ?> </h3>
										
									</div>
								<?php } ?>
												
					

								

						</div>
					</div>
				
								
            	</div>
		
        	</div>

	</div> <!--/card -->
		
	

			
	<?php endforeach; ?>

	</div>
</div>



