
<?php
//file: view/recipes/view.php
require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$recipe = $view->getVariable("recipe");
$favs = $view->getVariable("favs");
$isInFavs = $view->getVariable("isInFavs");
$ingredients = $view->getVariable("recipeIngredients");

$currentuser = $view->getVariable("currentusername");
$newcomment = $view->getVariable("comment");
$errors = $view->getVariable("errors");

$view->setVariable("title", "View recipe");

?>
<!--css file-->
<link rel="stylesheet" href="css/view.css">

<div class="container" id="page-header">
<button class="btn btn-lg btn-info text-center" onclick="location.href='index.php?controller=recipes&amp;action=index'"> <?= i18n("Back to main page")?></button>

</div>

<div class="container main-page-container">
	<div class="row container" id="top-page">
		<!--left side -->
		<div class="col-md-6">
		<div class="row">
				<div class="col-md-12">
					<h1><?= i18n("Recipe").": ".htmlentities($recipe->getTitle()) ?></h1>

				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<!--Autor-->

					<em><?= sprintf(i18n("by %s"),$recipe->getAuthor()->getAlias()) ?></em>
					

				</div>
			</div>
			<!--Tiempo de preparación-->
			<div class="row">
				<div class="col-md-12">
					<p><?= i18n("Cooking time").": ".htmlentities($recipe->getCookingTime()) ?> mins</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<!-- Ingredientes -->
						<h2> <?= i18n("Ingredients")?> (<?php echo count($ingredients); ?>):</h2>
						<ul>
							<!--imprime la longitud del array-->
							

							<?php foreach ($ingredients as $ingredient): ?>
								<li>
									<?= htmlentities($ingredient[1]).":".htmlentities($ingredient[2]) ?>
								</li>
							<?php endforeach; ?>
						</ul>


				</div>
			</div>
			<!--content-->
			<div class="row">
					<div class="col-md-12">
					<h2><?php echo i18n("Description")?>:</h2><br>
					<p>
						<?= htmlentities($recipe->getContent()) ?>
					</p>
					</div>
			</div>
			<div class="row" id="view_favs">
					
					<div class="col-md-12 icon-container">
						
						<!--Show favs count-->
						<?php if(isset($currentuser)){ ?>
							<div id="fav-count-container">
										<?php 
										if (isset($isInFavs) && $isInFavs != false){?>
											<a class="glyphicon glyphicon-star	 fas fa-camera fa-2x center-block"  href="index.php?controller=recipes&amp;action=deleteFromFavourites&amp;id=<?= $recipe->getId() ?>"></a>				

										<?php } else { ?>
											<a class="glyphicon glyphicon-star-empty	 fas fa-camera fa-2x center-block"  href="index.php?controller=recipes&amp;action=addToFavourites&amp;id=<?= $recipe->getId() ?>"></a>				
										<?php } 
										?>
										<h3 id="favCount"> <?php echo $favs ?> </h3>
										
									</div>
									<?php 
									}else{ ?>
									<div id="fav-count-container">
										<a class="glyphicon glyphicon-star-empty fas fa-camera fa-2x center-block" onclick="alert('<?php echo i18n('Must login to do this') ?>' )"  ></a>				

										<h3 id="favCount"> <?php echo $recipe->getFavs(); ?> </h3>
										
									</div>
								<?php } ?> 
					</div>
				</div>
			

		</div>
		<!-- right side -->
		<div class="col-md-6" id="top-right">
				<div class="col-md-12" id="imageContainer">
					<!--Image-->
					<?php if ($recipe->getImageUrl() != null) { ?>
					<img class="img-fluid w-100"src="<?= htmlentities($recipe->getImageUrl()) ?>" alt="<?=
					htmlentities($recipe->getTitle()) ?>" width="200" height="200">
					<?php } ?>
				</div>		
		</div>

	</div>
	
	<div class="row" id="button-page">
		
		<div class="col-md-12">
			

			
			<div class="row">
				<div class="col-md-12">
				
					<!-- Comments -->
					<h2><?= i18n("Comments") ?></h2>

					<?php foreach($recipe->getComments() as $comment): ?>
						<hr>
						<p><?= sprintf(i18n("%s commented..."),$comment->getAuthor()->getAlias()) ?> </p>
						<p><?= $comment->getContent(); ?></p>
					<?php endforeach; ?>

					<?php if (isset($currentuser) ): ?>
						<h3><?= i18n("Write a comment") ?></h3>

						<form method="POST" action="index.php?controller=comments&amp;action=add">
							<?= i18n("Comment")?>:<br>
							<?= isset($errors["content"])?i18n($errors["content"]):"" ?><br>
							<textarea type="text" name="content"><?=
							htmlentities($newcomment->getContent());
							?></textarea>
							<input type="hidden" name="id" value="<?= $recipe->getId() ?>" ><br>
							<input type="submit" name="submit" value="<?=i18n("do comment") ?>">
						</form>

					<?php endif ?>


				</div>
			</div>
		</div>
	</div>
</div>







