<link rel="stylesheet" href="js/galleria/themes/classic/galleria.classic.css">
<style>
    #galleria{ width: 700px; height: 400px; background: #000 }
</style>

<script type="text/javascript" src="js/galleria/galleria-1.2.8.min.js"></script>
<script type="text/javascript" src="js/galleria/themes/classic/galleria.classic.min.js"></script>
<script type="text/javascript" src="js/creatividades.js"></script>
<form id="creatividadesPopForm" name="creatividadesPopForm" method="post">
	<table class="tableRubros">
		<tr>
			<td>
				<span>Rubro</span>
				<select id="cmbRubroCreatividad" name="cmbRubroCreatividad"></select>
			</td>
			<td>
				<span>Mes/A&ntilde;o</span>
				<input type="text" name="txtMesAnnoCreatividad" id="txtMesAnnoCreatividad" style="width: 80px">
			</td>
			<td>
				<a id="btnFiltroCreatividad" class="btnAccGrid btnAccB">Ver</a>
			</td>
			<td>
				<a class="btnAccGrid btnAccB" id="fullscreen" href="#">Fullscreen</a>
			</td>
			<td>
				<a id="btnZip" class="btnAccGrid btnAccB">Zip</a>
			</td>
		</tr>
	</table>
</form>
<div id="galleria"></div>

<script type="text/javascript">
	$(document).ready(function() {
		$_initCreatividad();
	});
</script>