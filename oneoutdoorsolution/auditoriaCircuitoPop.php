<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="auditoriaCampannaCircuitoPopForm" name="auditoriaCampannaCircuitoPopForm" method="post">
		<p>
			Filtrar Mapa por:
			<select id="cmbFiltro">
				<option value="CIR">Circuito</option>
				<option value="EMP">Empresa de V&iacute;a P&uacute;blica</option>
				<option value="ELE">Elemento</option>
				<option value="SEM">Sem&aacute;foro</option>
			</select>
		</p>
		<a id="btnSalirCampCircuito" class="btnAcc3 btnAccB">Cerrar</a>
		<a id="btnMapaDetalle" class="btnAcc3 btnAccB">Ver Mapa</a>
		<table id="dt_auditoriasCampannasCircuito" class="display">
			<thead>
				<tr>
					<th>Nombre Campa&ntilde;a</th>
					<th>Fecha Inicio</th>
					<th>Fecha Fin</th>
					<th>Nombre EVP</th>
					<th>Elemento</th>
					<th>Circuito</th>
					<th>Provincia</th>
					<th>Localidad</th>
					<th>Cant. Pautada</th>
					<th>Detalle</th>
					<th class="chkMapping">Mapping</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="11" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initCampannasCircuitoPop();
	});
</script>