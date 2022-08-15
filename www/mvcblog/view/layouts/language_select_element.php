<?php
// file: view/layouts/language_select_element.php
?>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.3.1/css/flag-icon.min.css" rel="stylesheet"/>

<!--include language_selector.css --> 
<link rel="stylesheet" href="css/language_selector.css" type="text/css">


<!--bootstrap language icons -->

<div id="language-selector" class="d-flex flex-rows justify-content-center text-center">
	<div onclick="location.href='index.php?controller=language&amp;action=change&amp;lang=en';" id="flag" class="flag-icon-background flag-icon-us" > &nbsp;</div>
	<div onclick="location.href='index.php?controller=language&amp;action=change&amp;lang=es';" id="flag" class="flag-icon-background flag-icon-es" > &nbsp;</div>
</div>


<a  class="flag-icon-background flag-icon-us" href="index.php?controller=language&amp;action=change&amp;lang=en"> </a>