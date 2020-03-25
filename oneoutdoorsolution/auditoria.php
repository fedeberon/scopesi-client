<!-- Script Inversiones Form -->
<!--<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>-->
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyANGywbfxItEbdle738SiU-AVJGIjadVYM" type="text/javascript"></script>
<script src="js/jquery/jQuery-gMap-master/jquery.gmap.js" type="text/javascript"></script>
<script src="js/jquery/jquery.blockUI-2.53.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>

<script src="js/auditoria.js?<?=time()?>" type="text/javascript"></script>
<script src="js/autoFiltro.js" type="text/javascript"></script>

<script>
	$('#icon-auditoria').addClass('active');
</script>

<div id="modCenter">
	<a class="btnAcc btnAccA" id="btnInformes" href="javascript:;">Informes</a>
	<a class="btnAcc btnAccA" id="btnCampannas" href="javascript:;">Campa&ntilde;as</a>
	<img align="left" src="images/auditorias.png">
	<span id="titulacion">Auditorias</span>
	<div id="campannas">
		<a class="btnAcc btnAccA" id="btnMapa" href="javascript:;">Ver Mapa</a>
		<table id="dt_auditoriasCampannas" class="display">
			<thead>
				<tr>
					<th>Id</th>
					<th>Nombre Campa&ntilde;a</th>
					<th>Producto</th>
					<th>Agencia</th>
					<th>Detalle</th>
					<th>Mapping</th>
					<th>Download</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="7" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
	</div>

	<div id="informes" style="display: none;">
		<a class="btnAcc btnAccA" id="btnExcelInforme">Excel</a>
		<table id="dt_auditoriaInformes" class="display">
			<thead>
				<tr>
					<th>Dispositivo</th>
					<th>Empresa</th>
					<th>Provincia</th>
					<th>Localidad</th>
					<th>Desde</th>
					<th>Hasta</th>
					<th>Control</th>
					<th>Frecuencia</th>
					<th>Exhibido OK</th>
					<th>Sin Afiche</th>
					<th>Con Desperfecto</th>
					<th>Total Exhibido</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="12" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- PopUp Auditoria Dialog -->
<div id="auditoriaCampannaDialog"></div>
<div id="auditoriaInformeDialog"></div>
<div id="auditoriaMapaDialog"></div>
<div id="auditoriaFotosDialog"></div>

<!-- PopUp Auditoria Circuitos Dialog -->
<div id="auditoriaCircuitoDialog"></div>
<div id="auditoriaCircuitoDetalleDialog"></div>
<div id="auditoriaAudienciaDialog"></div>

<script>
	$_init();
</script>
