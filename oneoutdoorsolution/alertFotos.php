<link rel="stylesheet" href="js/galleria/themes/classic/galleria.classic.css">
<style>
    #galleria{ width: 700px; height: 400px; background: #000 }
</style>

<script type="text/javascript" src="js/galleria/galleria-1.5.7.min.js"></script>
<script type="text/javascript" src="js/galleria/themes/classic/galleria.classic.min.js"></script>
<script type="text/javascript" src="js/fotosAlert.js"></script>

<div id="galleria"></div>

<input type="hidden" id="idDia" value="<?php echo $_REQUEST['dia']; ?>" />
<a style="margin: 10px 30px 0 0;" class="btnAccFilter btnAccB" id="fullscreen" href="#">Fullscreen</a>

<script type="text/javascript">
	$(document).ready(function() {
		$_initFotosAlert();
	});
</script>
