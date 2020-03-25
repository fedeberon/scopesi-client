<?php 

require_once('../../Datatables.php');
$datatables = new Datatables();  // for mysqli  =>  $datatables = new Datatables('mysqli'); 

$datatables
->select('users.idUser, users.userCompleteName')
->from('users')
->join('producto', 'users.idUser = producto.idUser', 'inner')
->select('SUM(puntos) sumaPuntos')
->groupby('users.idUser');
 
echo $datatables->generate();
?>