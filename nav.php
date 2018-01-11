<?
function Menu($sitio)
{
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if(($sitio)== $url) 
{echo 'class="active"'; }
}
?>
 
<ul class="nav nav-pills pull-right">

<li <?php  Menu('http://ejemplo-aqui-tu-web/index.php')?>> <a href="http://ejemplo-aqui-tu-web" >Home</a>					</li>
<li <?php  Menu('http://ejemplo-aqui-tu-web/catalogo/index.php')?>>		<a href="http://ejemplo-aqui-tu-web/catalogo">Catalogo</a>		</li>
<li <?php  Menu('http://ejemplo-aqui-tu-web/contacto/index.php')?>>		<a href="http://ejemplo-aqui-tu-web/contacto/">Contacto</a>			</li>

</ul>