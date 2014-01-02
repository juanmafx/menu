<?
function Menu($sitio) {
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if(ereg($sitio,$url)) {echo 'class="active"'; }
}
 ?>

<header>
<nav>
<div class=menue <?php  Menu('http://ejemploaquituweb/index.php')     ?>  ><a href="http://ejemploaquituweb/index.php" >Home</a></div>
<div class=menue <?php  Menu('http://ejemploaquituweb/nosotros.php')  ?>  ><a href="http://ejemploaquituweb/nosotros.php">Nosotros</a>   </div>
<div class=menue <?php Menu('http://ejemploaquituweb/contacto.php')   ?>  ><a href="http://ejemploaquituweb/contacto.php">Contacto</a>   </div</nav>
</nav>
</header>
