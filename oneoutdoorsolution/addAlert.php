<!-- Script Inversiones Form -->
<script src="js/jquery/jquery.blockUI-2.53.js" type="text/javascript"></script>

<script src="js/addAlert.js?<?=time()?>" type="text/javascript"></script>
<script src="js/autoFiltro.js" type="text/javascript"></script>
<script>
	$('#icon-addAlert').addClass('active');
</script>

<div id="modCenter">

<img align="left" src="images/auditorias.png">
<span id="titulacion">Alert</span>
    <select id="cmbDias" name="cmbDias" class="loginput">
		<option value="lunes">Lunes</option>
		<option value="martes">Martes</option>
		<option value="miercoles">Miercoles</option>
        <option value="jueves">Jueves</option>
        <option value="viernes">Viernes</option>
    </select>
    <a class="btnAccA btnAcc" id="btnGetFotos"  align="center" href="javascript:;">Galeria</a>
</div>
<!-- PopUp AddAler Dialog -->
<div id="alertFotosDialog"></div>


<script>
	$_init();
</script>
