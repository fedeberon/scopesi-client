<?php
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<span id="titulacion">Audiencia</span>
	<form id="audienciaPopForm" name="audienciaPopForm" method="post">
		<div id="tabsMain">
			<ul>
					<li><a href="#tabs-Filtros">Filtros</a></li>
					<li><a href="#tabs-Result">Resultado</a></li>
					<!-- <li id="limpiar">Limpiar</li> -->
				</ul>

				<div id="tabs-Filtros">
				<div class="centerFilterAudiencia">
					<table class="tblAudiencia">
						<tbody>
							<tr>
								<!-- Filtro de Edad -->
								<td>
									<table id="dt_filtroEdad" class="display">
										<thead>
											<tr>
												<th></th>
												<th>Edad</th>
											</tr>
											</thead>
											<tbody>
												<tr>
													<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
												</tr>
										</tbody>
									</table>
								</td>

								<!-- Filtro de Sexo -->
								<td>
									<table id="dt_filtroSexo" class="display">
										<thead>
											<tr>
												<th></th>
												<th>Sexo</th>
											</tr>
											</thead>
											<tbody>
												<tr>
													<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
												</tr>
										</tbody>
									</table>
								</td>

								<!-- Filtro de NSE -->
								<td>
									<table id="dt_filtroNSE" class="display">
										<thead>
											<tr>
												<th></th>
												<th>NSE</th>
											</tr>
											</thead>
											<tbody>
												<tr>
													<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
												</tr>
										</tbody>
									</table>
								</td>

								<!-- Filtro de Periodo -->
								<td>
									<table id="dt_filtroPeriodo" class="display">
										<thead>
											<tr>
												<th></th>
												<th>Periodo</th>
											</tr>
											</thead>
											<tbody>
												<tr>
													<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
												</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="tablebotonesEval">
						<tbody>
							<tr>
								<!-- <td>Planes Generados</td> -->
								<!-- <td>
									<select id="cmbDescAudiencia" name="cmbAudiencia" class="logInput"></select>
								</td> -->
								<td class="ml-1 text-center valign">
									<!--
										<a id="btnExcelAudiencia" title="Exportar a Excel"></a>
										<a id="btnEvaluarAudiencia" title="Evaluar Plan"></a>
										<a id="btnGuardarAudiencia" title="Guardar Plan"></a>
										<a id="btnEliminarAudiencia" title="Eliminar Plan"></a>
										<a id="btnRecuperarAudiencia" title="Recuperar Plan"></a>
									-->
									<!-- <a id="btnRecuperarAudiencia" class="botonesEval" title="Recuperar Plan"><i class="fas fa-folder-open"></i></a>
									<a id="btnEliminarAudiencia" class="botonesEval" title="Eliminar Plan"><i class="fas fa-trash-alt"></i></a>
									<a id="btnGuardarAudiencia" class="botonesEval" title="Guardar Plan"><i class="fas fa-save"></i></a>
									<a id="btnEvaluarAudiencia" class="botonesEval" title="Evaluar Plan"><i class="fas fa-chart-pie"></i></a> -->
									<button type="button" class="defaultfocus btn btn-primary btn-sm" id="btnEvaluarAudiencia"><i class="fas fa-running"></i> Ejecutar</button>
									<!-- <a id="btnExcelAudiencia" class="botonesEval" title="Exportar a Excel"><i class="fas fa-file-excel"></i></a> -->
								</td>
							</tr>
						</tbody>
					</table>
					<br/>

					<div id="grillaDetalleComun" style="display: none;">

						<table id="dt_filtroComun" class="display">
							<thead>
								<tr>
									<th>EVP</th>
									<th>Provincia</th>
									<th>Elemento</th>
									<th>Cantidad</th>
								</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="4" class="dataTables_empty">Cargando Datos...</td>
									</tr>
							</tbody>
						</table>

					</div>

					<div id="grillaDetalleBuses" style="display: none;">
						<table id="dt_filtroBuses" class="display">
							<thead>
								<tr>
									<th>EVP</th>
									<th>Direcci&oacute;n</th>
									<th>Elemento</th>
									<th>Cantidad</th>
								</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="4" class="dataTables_empty">Cargando Datos...</td>
									</tr>
							</tbody>
						</table>
					</div>

				</div>
			</div>

			<div id="tabs-Result">

				<div id="tabs">
					<ul>
							<li><a id="btnAudGeneral" href="#tabs-1">General</a></li>
							<li><a href="#tabs-2">Detallada</a></li>
							<li><a href="#tabs-3">Empresas</a></li>
							<li><a href="#tabs-4">Elementos</a></li>
							<li><a href="#tabs-5">Circuito</a></li>
							<li><a id="btnExcelAudiencia" href="#tabs-1"><i class="fas fa-file-excel"></i> Exportar a Excel</a></li>
							<!-- <li><a id="btnExcelAudiencia" class="botonesEval" title="Exportar a Excel"><i class="fas fa-file-excel"></i></a></li> -->
						</ul>
						<div id="tabs-1">
							<div class="container-fluid">
								<div class="row">
									<div class="col-sm">
										<table id="dt_audienciaGeneral" class="display">
											<thead>
												<tr>
													<th>Descripci&oacute;n</th>
													<th>Valor</th>
												</tr>
											</thead>
										</table>
									</div>
									<div class="col-sm">
										<canvas id="canvas2"></canvas>
									</div>
									<div class="col-sm">
										<canvas id="canvas3"></canvas>
									</div>
								</div>
								<div class="row">
									<div class="col-sm">
										<div id="minimapaudiencias"></div>
									</div>
									<div class="col-sm">
										<canvas id="canvas5"></canvas>
									</div>
									<div class="col-sm">
										<canvas id="canvas6"></canvas>
									</div>
								</div>
							</div>
						</div>
						<div id="tabs-2">
							<table id="dt_audienciaDetallada" class="display">
							<thead>
								<tr>
									<!-- <th>Descripci&oacute;n</th> -->
									<th>Empresa</th>
									<th>Direcci&oacute;n</th>
									<th>Localidad</th>
									<th>Provincia</th>
									<th>Elemento</th>
									<th>Cobertura Neta</th>
									<th>Frecuencia</th>
									<th>Cobertura %</th>
									<th>Impactos Totales</th>
									<th>PBR</th>
									<th>CPR</th>
									<th>CPM</th>
									<th>Target</th>
								</tr>
								</thead>
						</table>
						</div>
						<div id="tabs-3">
							<table id="dt_audienciaEmpresa" class="display">
							<thead>
								<tr>
									<!-- <th>Descripci&oacute;n</th> -->
									<th>Empresa</th>
									<th>Cant. Ubicaciones</th>
									<th>Inversi&oacute;n</th>
									<th>Impactos</th>
									<th>PBR</th>
									<th>CPR</th>
									<th>CPM</th>
									<th>Target</th>
								</tr>
								</thead>
						</table>
						</div>
						<div id="tabs-4">
							<table id="dt_audienciaElemento" class="display">
							<thead>
								<tr>
									<!-- <th>Descripci&oacute;n</th> -->
									<th>ID</th>
									<th>Elemento</th>
									<th>Cant. Elementos</th>
									<th>Inversi&oacute;n</th>
									<th>Impactos</th>
									<th>PBR</th>
									<th>CPR</th>
									<th>CPM</th>
									<th>Target</th>
								</tr>
								</thead>
						</table>
						</div>
						<div id="tabs-5">
							<table id="dt_audienciaCircuito" class="display">
							<thead>
								<tr>
									<th>Empresa</th>
									<th>Elemento</th>
									<th>Localidad</th>
									<th>Cant. Ubicaciones</th>
									<th>Inversi&oacute;n</th>
									<th>Impactos</th>
									<th>PBR</th>
									<th>CPR</th>
									<th>CPM</th>
									<th>Target</th>
								</tr>
								</thead>
							</table>
						</div>
				</div>
			</div>
		</div>

		<a id="btnSalirAudiencia" class="btnAcc btnAccB">Cerrar</a>

	</form>
</div>

<!-- PopUp Save Plan Audiencia Dialog -->
<div id="audienciaPlanDialog"></div>

<!-- PopUp Delete Audiencia Dialog -->
<div id="deleteAudienciaDialog" style="display: none;">
	<span id="titulacion">Realmente desea eliminar el Plan Seleccionado?</span>
	<a class="btnAcc btnAccB" id="btnConfirmDeleteAudiencia">S&iacute;</a>
	<a class="btnAcc btnAccB" id="btnExitDeleteAudiencia">No</a>
</div>
