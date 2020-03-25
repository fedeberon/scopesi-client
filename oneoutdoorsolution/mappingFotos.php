<link rel="stylesheet" href="js/galleria/themes/classic/galleria.classic.css">
<style>
    #galleria{ width: 700px; height: 400px; background: #000 }
</style>

<script type="text/javascript" src="js/galleria/galleria-1.5.7.min.js"></script>
<script type="text/javascript" src="js/galleria/themes/classic/galleria.classic.min.js"></script>
<script type="text/javascript" src="js/fotosMapping.js"></script>

<div id="galleria"></div>

<input type="hidden" id="idUbicacion" value="<?=$_REQUEST['idUbicacion']?>" />
<a style="margin: 10px 30px 0 0;" class="btnAccFilter btnAccB" id="fullscreen" href="#">Fullscreen</a>

<script type="text/javascript">
	$(document).ready(function() {
		$_initFotosMapping();
	});
</script>
