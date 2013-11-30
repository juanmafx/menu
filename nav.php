<?
function Menu($sitio) {
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if(ereg($sitio,$url)) {echo 'id="activo"'; }
}
 ?>
 
<header>

<nav>
<div class=menue <?php  Menu('http://hacerpatito.com/menu/index.php')  ?>  ><a href="http://hacerpatito.com/menu/index.php" >Home</a></div>
<div class=menue <?php  Menu('http://hacerpatito.com/menu/nosotros.php') 	  ?>  ><a href="http://hacerpatito.com/menu/nosotros.php">Nosotros</a>   </div>
<div class=menue <?php Menu('http://hacerpatito.com/menu/contacto.php')        ?>  ><a href="http://hacerpatito.com/menu/contacto.php">Contacto</a>   </div</nav>
</nav>
</header>
