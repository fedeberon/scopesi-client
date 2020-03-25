

<!-- File Upload Files -->
<link href="css/fileuploader.css" rel="stylesheet" type="text/css">
<script src="js/fileuploader.js" type="text/javascript"></script>

<!-- Script Inversiones Form -->
<script src="js/inversion.js" type="text/javascript"></script>
<script src="js/autoFiltro.js" type="text/javascript"></script>
<script>
	$('#icon-inversion').addClass('active');

	function openNav() {
		document.getElementById("mySidebar").style.width = "350px";
		document.getElementById("main").style.marginLeft = "350px";
	}
 
	function closeNav() {
		document.getElementById("mySidebar").style.width = "0";
		document.getElementById("main").style.marginLeft= "0";
	}

	$(document).ready(function() {
		$("#btnFiltroInversion").click(function(){
			
			if($(this).html() === "Filtrar Datos"){
				openNav();
				$(this).html("Cerrar");
			} 

			else {
				closeNav();
				$(this).html("Filtrar Datos");
			}

		});
	});
</script>

<style>
.sidebar {
	height: 100%;
	width: 0;
	position: fixed;
	z-index: 1;
	top: 75;
	left: 0;
	background-color: rgb(233,233,233, 0.90);
	overflow-x: hidden;
	transition: 0.5s;
	padding-top: 60px;
	box-shadow: -1px 0px 5px 1px #6c757d;
}

#overlay{
	margin-top: -50px;
}

.btn-filtro{
	color: #fff !important;
	background-color: #6c757d !important;
	padding: 0px 15px;
	margin: 15px 5px;
	border-radius: 4px;
	font-size:15px;	
	
}

.btnAcc{
	float:none;
	font-size:15px;
	padding: 2px 15px;
	margin: 15px 5px;
	border-radius: 4px;
}

#tableOver{
	margin-bottom:
}

#main{
	transition: margin-left .5s;
}
	
</style>


<div class="row">

	<div class="sidebar"  id="mySidebar">
		<?php	
		require("inversionPop.php");
		?>
 		
		 <div>
			<a id="btnCargarFiltro" class="btn-filtro" class="width:50%; float:left">CARGAR FILTRO</a>
			
			<a id="btnGrabarFiltro" class="btn-filtro" class="width:50%; float:rigth">GRABAR FILTRO</a>
		</div>
	</div>		

	<div id="main" style="width:100%">
		
		<div id="modCenter">
			<a class="btnAcc btnAccA" id="btnFiltroInversion">Filtrar Datos</a>
			<a class="btnAcc btnAccA" id="btnCreatividades" href="javascript:;">Creatividades</a>
			<a class="btnAcc btnAccA" id="btnExcel" href="javascript:;">Excel</a>
			<?php if($_SESSION['userType'] == 1) {?>
				<a class="btnAcc btnAccA" id="btnExcelAdmin" href="javascript:;">Excel Admin</a>
			<?php }?>
			
			<table id="dt_inversiones" class="display">
				<thead>
					<tr>
						<th>Sector</th>
						<th>Rubro</th>
						<th>Segmento</th>
						<th>Mes</th>
						<th>A&ntilde;o</th>
						<th>EVP</th>
						<th>Anunciante</th>
						<th>Producto</th>
						<th>Periodo</th>
						<th>Medio</th>
						<th>Tipo Dispositivo</th>
						<th>Importe</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="12" class="dataTables_empty">Cargando Datos...</td>
					</tr>
				</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
			</table>





		</div>

	</div>

</div>


<!-- PopUp Inversion Dialog -->
<div id="inversionDialog"></div>
<div id="creatividadesDialog"></div>

<script>
	$_init();
</script>