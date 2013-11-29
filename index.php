<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta name="description" content="">
<title>Ejemplo de menu dinamico </title>
<link href="css.css" rel="stylesheet">
</head>

<?
function Menu($sitio) {
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if(ereg($sitio,$url)) {echo 'id="activo"'; }
}
 ?>
 
<header>

<nav>
<div class=menue <?php Menu('http://ejemplo/index.php')		?> 			<a href="">HOME</a></div>
<div class=menue <?php Menu('http://ejemplo/impresiones.php')		?>  	<a href="">IMPRESIONES</a></div>
<div class=menue <?php Menu('http://ejemplo/contacto.php')		?>    	<a href="">Contacto</a></div>
</nav>

</header>

<body>
</html>
