<!--add custom css -->
<link rel="stylesheet" href="css/add.css">

<?php
//file: view/recipes/add.php
require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$recipe = $view->getVariable("recipe");
$errors = $view->getVariable("errors");
$ingredients = $view->getVariable("ingredients");
$selectedIngredients = $view->getVariable("selectedIngredients");

$view->setVariable("title", "Edit recipe");


?>

<!-- new content -->
<div class="container" id="add_view_container">
	<div id="form-container" class="col-md-8 col-md-offset-2">

		<div class="h1" id="title" >
			<h1><?= i18n("Create recipe") ?></h1>

		</div>
		
		<form id="form-add" action="index.php?controller=recipes&amp;action=add" method="POST" enctype="multipart/form-data">
			<!--top row --> 
			<div class="row">
				<!--left side -->
				<div id="top-row-left" class="col-md-6">
					<!--title-->
					<div class="form-group">
						<label for="title"><?= i18n("Title") ?> <span class="require">*</span></label>
						<input type="text" class="form-control" name="title" value="<?= $recipe->getTitle() ?>">
						<!-- if is set else show error -->
						<?php if (isset($errors["title"])) { ?>
							<span class="help-block"><?php echo i18n($errors["title"]) ?></span>
						<?php } ?>
					</div>
				</div>
				<!--right side -->
				<div class="col-md-6">
					<!--Cooking time -->
					<div class="form-outline">
						<label class="form-label" for="typeNumber"><?= i18n("Cooking time") ?> (mins) <span class="require">*</span></label>
						<input type="number" min="1" max="200" name="cooking_time" class="form-control" value="<?= $recipe->getCookingTime() ?>">
					</div>
					<?php
					if (isset($errors["cooking_time"])) { ?>
						<span class="help-block"><?php echo i18n($errors["cooking_time"]) ?></span>
					<?php } ?>
				</div>

			</div>
			<!--/top row --> 

		
			<!--Contents-->
			<div class="form-group">
				<label for="content"><?= i18n("Contents") ?> <span class="require">*</span></label>
				<textarea name="content" class="form-control" rows="4" cols="50"><?=
																					htmlentities($recipe->getContent()) ?></textarea>
				<?php if (isset($errors["content"])) { ?>
					<span class="help-block"><?php echo i18n($errors["content"]) ?></span>
				<?php } ?>
			</div>

			
			<!-- middle row -->
			<div class="row" id="amount-container">
				
				
						<!--Ingrediente a añadir-->
						<div class="form-group">
							<label class="form-label" for="ingredients"><?= i18n("Ingredient") ?> </label>
							<br>
							<select class="js-example-basic-single" name="selected" data-live-search="true" data-style="btn-warning">
								<?php foreach ($ingredients as $ingredient) { ?>
									<option value="<?= $ingredient[0] ?>">
										<?= $ingredient[1] ?>
									</option>
								<?php } ?>
								<!-- <input type="hidden" name="ingredientid" value="<?= $ingredient[0] ?>">  -->
								<!-- hidden input to send the ingredient id -->

							</select>
						</div>
					
					
						<!--Cantidad-->
						<div class="form-group">
							<label class="form-label" for="quantity"><?= i18n("Amount") ?> : </label>
							<input type="number" min="1" max="200" name="amount" class="form-control" value="1">
							
						</div>
						<?php
						if (isset($errors["amount"])) { ?>
							<span class="help-block"> <?php echo i18n($errors["amount"]) ?></span>
						<?php } ?>
				
				
		
			</div>

			<div class="row">
				<button type="submit" class="button glyphicon glyphicon-plus fas fa-camera fa-2x center-block" name="addIngredientToRecipe" value="Click here to add" />
			</div>

			<div class="row">
				<!--Ingredientes añadidos-->
				<div class="form-group center"> 
					<?= i18n("Selected ingredients") ?>:
					<div id="selected-ingredients">

						<?php serialize($selectedIngredients); ?>
						<?php if (isset($selectedIngredients)) { ?>

							<?php foreach ($selectedIngredients as $ingredient) { ?>
								<p>
									<div class="container-fluid" id="selected-ingredients-container">
										<div id="selected-ingredient" class="row">
											<!--left side for name and amount -->
											
												<h5><?= i18n("Name")?>:<?= $ingredient["name"] ?></h5>
												<h5><?= i18n("Num")?>:<?= $ingredient["amount"] ?></h5>
											
										</div> 
										
										<br>
									</div>
								</p>
							<?php } ?>

						<?php } ?>
					</div>
					<?php
					if (isset($errors["ingredients"])) { ?>
						<span class="help-block"><?php echo i18n($errors["ingredients"]) ?></span>
					<?php } ?>
					<button name="removeIngredientFromRecipe" type="submit" class="button button-red glyphicon glyphicon-minus fas fa-camera fa-2x center-block"  
												value="Click here to add" onclick="">
												<input type="hidden" name="ingredient_id_remove" value="
												<?= (isset($ingredient["id"]))?$ingredient["id"]:null ?>">


												</button>
				</div>
			</div>
			<br>
			

			

			<!-- Add a new ingredient not existing -->
			
			<div class="form-group" id="addnew">
				<label class="form-label" for="quantity"><?= i18n("¿Not in list? Add one") ?> : </label>
				<input type="text" class name="newIngredient" class="form-control" placeholder="<?= i18n("Add an ingredient") ?>">

				<input class="form-control" id="form-add" type="submit" name="addIngredient" value="<?php echo i18n("Click here to add") ?> " />
				<?php
				if (isset($errors["newIngredient"])) { ?>
					<span class="help-block"><?php echo i18n($errors["newIngredient"]) ?></span>
				<?php } ?>


			</div>

			<!-- Image -->
			<?= i18n("Image") ?>:
			<input type="file" class="form-control" name="image_url" id="image_url" value="<?= $recipe->getImageUrl() ?>">
			<input type="hidden" name="image_url_old" value="<?= $recipe->getImageUrl() ?>">
			
			<?php echo i18n("Image selected") ?>:<?= $recipe->getImageUrl() ?> <br>
			<?php
			if (isset($errors["image_url"])) { ?>
				<span class="help-block"><?php echo i18n($errors["image_url"]) ?></span>
			<?php } ?>


			

			<!--Extra-->
			<div class="form-group">
				<p><span class="require">*</span> <?php echo i18n("required fields") ?></p>
			</div>

			<div class="form-group" id="actions-nav">
				<!--ok button-->
				<button type="submit" name="submit" class="btn btn-primary">
					<?php echo i18n("Create") ?>
				</button>
				<!-- back button -->
				<a href="index.php" class="btn btn-secondary">
					<?= i18n("Back") ?>
				</a>
				<!-- reset button -->
				<button type="reset" class="btn btn-secondary">
					<?= i18n("Reset") ?>
				</button>

			</div>

			<!--passing array-->
			<?php if (isset($selectedIngredients)) { ?>
				<?php
				foreach ($selectedIngredients as $value) {
					echo '<input type="hidden" name="selectedIngredients[]" value="' . $value["id"] . '">';
					echo '<input type="hidden" name="selectedIngredients[]" value="' . $value["name"] . '">';
					echo '<input type="hidden" name="selectedIngredients[]" value="' . $value["amount"] . '">';
				}
				?>
			<?php } ?>




		</form>
	</div>

</div>
</div>