<html>
	<head>
	<link rel=StyleSheet href="../archivos/style.css" type="text/css" media=screen>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	</head>
<?php 
include "../archivos/conexion1.php"; 
//Creamos un if que no nos deje entrar a esta página salvo que exista ya la sesión, esto es para que desde fuera no se pueda acceder,
//para ver esta página hay que estar logueado si o si.
session_start();
    if (isset($_COOKIE['contrasena'])||isset($_SESSION['contra'])){
    }else{
        header('Location: login.php');
        die() ;
    }

//Creamos la parte para importar 10 contactos desde una archivo txt
//botón para cerrar la conexión
	echo  "<form class='contactos' action='' method='post'><input type='submit' name='cargar' value='Cargar Contactos'>
	<input type='submit' class='cerrar' name='cerrar' value='  '></form>";
	
		
	if(isset($_POST['cargar'])){
		$open = fopen('contactos.txt','r');
		while (!feof($open)){
			$getTextLine = fgets($open);
			$explodeLine = explode(",",$getTextLine);
			list($nombre,$telefono,$correo,$fechaNac) = $explodeLine;
			$qry = "INSERT INTO agenda (nombre, telefono, correo, fechaNac) values('$nombre','$telefono','$correo','$fechaNac')";
			$consulta= $conn->prepare($qry);
			$consulta->execute();
		}
		fclose($open);
		echo "<div class='alerta' id='mensaje'>Contactos cargados</div>";
	};

	//CERRAR CONEXIÓN
	if(isset($_POST['cerrar'])){
		header('Location: login.php');
		setcookie("usuario",$_COOKIE['usuario'],time()-1);
		setcookie("contrasena",$_COOKIE['contrasena'],time()-1);
		session_destroy();
		$conn=null;
	};

//BORRAR DE UNO EN UNO
	if(isset($_POST['quitar'])){
		if(isset($_POST['dato'])){
		$sql="DELETE FROM agenda WHERE codigo=:cod";
			$stmt=$conn->prepare($sql);
			$stmt->bindParam(":cod",$_POST['dato']);
			$stmt->execute();
			
			header('Refresh:0');
			
		}

	};


	//MODIFICAR
	//Mostramos los campos a modificar, se muestran de forma dinámica de forma que el código es independiente de la bbdd
	if(isset($_POST['modificar'])){
		?>
		<html><head><style>#todo{opacity: 0.2;}</style></head></html>
			<?php
		$id = $_POST['dato'];
		$nombres="SELECT * FROM agenda WHERE codigo=:cod;";
		$consulta_nombres=$conn->prepare($nombres);
		$consulta_nombres->bindParam(':cod',$id);
		$consulta_nombres->execute();
		$resultado_nombres=$consulta_nombres->fetchAll();
		echo "<div class='contenedor' id='container'><form action='' method='post' class='elemento'><table>";
		foreach($resultado_nombres as $nombre_columna){
			for($i=0;$i<count($nombre_columna)/2;$i++){
				if($i==0){
					echo "<tr class='sin' ><input type='text' readonly name='dato[]' value='".$nombre_columna[$i]."'></input></tr>";
				}else{
					echo "<tr class='sin'><input type='' name='dato[]' value='".$nombre_columna[$i]."'></input></tr>";
				}
			}
		}
		echo "<tr><div><input type='submit' class='eliminar-varios' name='modificar-ult' value='Actualizar'></input>";
		echo "<input type='submit' class='eliminar-varios' name='cancelar' value='Cancelar'></input></div></tr>";

		echo "</table></form></div>";
	};
	//Parte de actualización

	if(isset($_POST['modificar-ult'])){
		$nombres="SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='agenda' AND `TABLE_NAME`='agenda';";
		$consulta_nombres=$conn->prepare($nombres);
		$consulta_nombres->execute();
		$resultado_nombres=$consulta_nombres->fetchAll();
					foreach($resultado_nombres as $nombre_columna){	
				for($i=0;$i<count($nombre_columna)/2;$i++){
					$nombress[]=$nombre_columna;
				}
			}

			if((is_numeric($_POST['dato'][2])) && (strpos($_POST["dato"][4], "@")!==false) && (strpos($_POST["dato"][4], ".")!==false)){
				$stmt = $conn->query("SELECT COUNT(*) FROM agenda WHERE telefono='".$_POST['dato'][2]."' OR correo='".$_POST['dato'][4]."' AND codigo <>".$_POST['dato'][0]."");
				$res = $stmt->fetchColumn(0);
				if($res){
					
					for($i=1;$i<count($nombress);$i++){
						$nombress[$i][0];
						$sql="UPDATE agenda SET  ".$nombress[$i][0]."=:nom  WHERE codigo=".$_POST['dato'][0].";";
						$stmt=$conn->prepare($sql);
						$stmt->bindParam(":nom",$_POST['dato'][$i]);
						$stmt->execute();
					}
					echo "<div class='alerta' id='mensaje'>Contacto actualizado</div>";
				}else{
					echo "<div class='alerta' id='mensaje'>El teléfono o correo ya se encuentra registrado</div>";

				}	
			}else{
				echo "<div class='alerta' id='mensaje'>El teléfono debe ser numérico y el correo debe contener '@' y '.' </div>";

			}				
	};



	//AGREGAR NUEVO CONTACTO
	//aquí mostramos el div con los campos para agregar
	if(isset($_POST["btnAgregar"])){ 
		?>
		<html><head><style>#todo{opacity: 0.2;}</style></head></html>
			<?php
		$nombres="SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='agenda' AND `TABLE_NAME`='agenda';";
		$consulta_nombres=$conn->prepare($nombres);
		$consulta_nombres->execute();
		$resultado_nombres=$consulta_nombres->fetchAll();
		echo "<div class='contenedor' id='container'><form action='' method='post' class='elemento'><table>";
		array_shift($resultado_nombres);//borramos la primera fila porque es el código y no queremos que aparezca porque es autoincrement
		foreach($resultado_nombres as $nombre_columna){
			for($i=0;$i<count($nombre_columna)/2;$i++){		
				echo "<tr class='sin'>".$nombre_columna[$i]."<input type='text' name='dato[]' value=''></input></tr>";
			}
		}
		echo "<tr><div><input type='submit' class='eliminar-varios' name='agregar-ult' value='Agregar'></input>";
		echo "<input type='submit' class='eliminar-varios' name='cancelar' value='Cancelar'></input></div></tr>";
		echo "</table></form></div>";
}
//aqui se realiza la insercción en sí que como quiero que sea independiente de la bbdd hace esto:
/*
primero inserto una fila vacía, esto genera un código que como es autoincrement, este ultimo codigo que se ha generado
será el más alto de la bbdd si o si, después selecciono el registro con el codigo más alto y guardo este codigo en una variable
para despues decirle que actualice ese registro y guarde los campos que el usuario ha añadido, no se si la lógica es la mejor
pero es lo único que se me ha ocurrido para que sea independiente de la bbdd y, sorprendentemente, funciona

*/
if(isset($_POST['agregar-ult'])){
	if((is_numeric($_POST['dato'][1])) && (strpos($_POST["dato"][3], "@")!==false) && (strpos($_POST["dato"][3], ".")!==false)){
		$stmt = $conn->query("SELECT COUNT(*) FROM agenda WHERE telefono='".$_POST['dato'][1]."' OR correo='".$_POST['dato'][3]."' ");
	$res = $stmt->fetchColumn(0);
	if($res){
		echo "<div class='alerta' id='mensaje'>El teléfono o correo ya se encuentra registrado</div>";
	}else{
		$conn->exec("INSERT INTO `agenda` (`codigo`) VALUES (NULL);");
		$nombres="SELECT MAX(codigo) FROM agenda;";
		$consulta_nombres=$conn->prepare($nombres);
		$consulta_nombres->execute();
		$resultado_nombres=$consulta_nombres->fetchAll();
		foreach($resultado_nombres as $nombre_columnaa){	
			$_SESSION['codiguito']=$nombre_columnaa[0];
			//echo $_SESSION['codiguito'];
		}
		$nombres="SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='agenda' AND `TABLE_NAME`='agenda';";
		$consulta_nombres=$conn->prepare($nombres);
		$consulta_nombres->execute();
		$resultado_nombres=$consulta_nombres->fetchAll();
		foreach($resultado_nombres as $nombre_columna){	
			for($i=0;$i<count($nombre_columna)/2;$i++){
				$nombress[]=$nombre_columna;
			}
		}
		/*eliminamos el primer elemento que es el codigo porque sino desde el formulario nos llega que el primer elemento es el nombre y si actualizamos el codigo
		con el nombre da error, el codigo hay que dejarle fuera */
		array_shift($nombress); 
		for($i=0;$i<count($nombress);$i++){
			$sql="UPDATE agenda SET  ".$nombress[$i][0]."=:nom  WHERE codigo=".$_SESSION['codiguito'].";";
			$stmt=$conn->prepare($sql);
			$stmt->bindParam(":nom",$_POST['dato'][$i]);
			$stmt->execute();
		}
			echo "<div class='alerta' id='mensaje'>Contacto agregado</div>";
	}
	}else{
		echo "<div class='alerta' id='mensaje'>El teléfono debe ser numérico y el correo debe contener '@' y '.' </div>";
	}
	
		
}


if(isset($_POST["cancelar"])){ 
	header("Location:paginador.php");
}

//BORRAR
	if(isset($_POST['borrar'])){
		if (empty($_POST['eliminar'])){
			echo '<h1>No se ha seleccionado ningun registro</h1>';
		}else{
			foreach($_POST['eliminar'] as $id_borrar){
				//creamos la consulta que borra todos los contactos con los id que se han pasado y que nos redirige una vez borrados al inicio
				$borrar=$conn->query("DELETE FROM agenda WHERE codigo='$id_borrar'");
				header('Location: paginador.php');
			}
		}
	};


	//PAGINACIÓN PARTE 1
	//Creamos la consulta para saber cuantos contactos tenemos en total, despues dividimos los contactos que tenemos entre los que queremos que se muestren
	//por pantalla, en este caso 6, y redondeamos y ya tenemos el número de páginas que se van a mostrar
	$sql='SELECT * FROM agenda';
	$sentencia= $conn->prepare($sql);
	$sentencia->execute();
	$resultado=$sentencia->fetchAll();
	$contactos_x_pagina=6;
	$total_contactos_db=$sentencia->rowCount();
	$paginas=$total_contactos_db/$contactos_x_pagina;
	$paginas=ceil($paginas);
?>


	<body>
		<div id="todo" class="todo">
		<!--formulario para buscar algún nombre-->
		<div class="cabecera">
			<form action="" method="post">
				<input type="submit" name="btnAgregar" value=" " class="agregar">   
			</form>
			<form action="" method="post" class="form-buscar">
			<input type="submit" name="btnEnviar" class="buscar" value=" "><input type="text" name="cod"  required><br>     
			</form>
		</div>
		<?php
		//si el usuario quiere buscar algún nombre se envía ese nombre a través de una variable de sesión y se redirige a otra página
		//también se oculta la tabla con todos los contactos para que, en la otra página, se muestren solo los buscados
		    if(isset($_POST["btnEnviar"])){ 
				$_SESSION['nombre']=$_POST['cod'];
				$nombre=$_SESSION['nombre'];
				$sql="SELECT * FROM agenda WHERE nombre LIKE '%$nombre%'";
				$sentencia= $conn->prepare($sql);
				$sentencia->execute();
				$resultado=$sentencia->fetchAll();
				if($resultado!=null){
					header('Location: buscar.php'); 
					
				}
				if($resultado==null){
					echo "<div class='alerta' id='mensaje'>Contacto no encontrado</div>";
				}
				
			}
		//creamos un if para que si nos pasamos de página, es decir, estoy en la última página 
		//no muestre el botçon de siguiente
			if(!$_GET){
				header('Location: paginador.php?pag=1');
			}

			if ( $_GET['pag']==1){
				?>
				<html><head><style>.anterior{display:none}</style></head></html>
				<?php
			}
		
		//PAGINADOR 2 parte
		//Creamos la consulta para obtener los contactos de 6 en 6
			$iniciar=($_GET['pag']-1)*$contactos_x_pagina;
			$sql_contactos='SELECT * FROM agenda LIMIT :inicar,:ncontactos';
			$sentencia_contactos=$conn->prepare($sql_contactos);
			$sentencia_contactos->bindParam(':inicar',$iniciar,  PDO::PARAM_INT);
			$sentencia_contactos->bindParam(':ncontactos',$contactos_x_pagina,  PDO::PARAM_INT);
			$sentencia_contactos->execute();
			$resultado_contactos=$sentencia_contactos->fetchAll();
			
			//obtenemos todos los nombres de las columnas de nuestra tabla, este código es independiente de la bbdd
			$nombres="SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='agenda' AND `TABLE_NAME`='agenda';";
			$consulta_nombres=$conn->prepare($nombres);
			$consulta_nombres->execute();
			$resultado_nombres=$consulta_nombres->fetchAll();
						echo "
			
			<form method='post' class='normal'>
				<table class='tabla-principal'>
				<tr class='primer-tr'>
					<td><input type='submit' class='eliminar-varios' name='borrar' value='Eliminar'></td>";
					//mostramos los nombres de las columnas
						foreach($resultado_nombres as $nombre_columna){	
							for($i=0;$i<count($nombre_columna)/2;$i++){
								echo "<td>".$nombre_columna[$i]."</td>";	
							}
						}
				echo "<td>Acciones</td>
				</tr>";
			
		//para poder mostrar los datos los recorremos con un foreach y creamos los botones de borrar y modificar y como value pasamos el id para poder 
		//utilizarlo luego
			foreach($resultado_contactos as $contacto){	
				echo "<tr>";
				echo "<td><input type='checkbox' name='eliminar[]' value='".$contacto[0]."'/></td>";	
				for($i=0;$i<count($contacto)/2;$i++){
					echo "<td>".$contacto[$i]."</td>";
				}
						echo"<td class='opciones'>
						<form action='' method='post'>
							<input type='hidden' name='dato' value='".$contacto[0]."'>
							<input type='submit' class='modificar' value='  ' name='modificar'/>
						</form>
						<form action='' method='post'>
							<input type='hidden' name='dato' value='".$contacto[0]."'>
							<input type='submit' name='quitar'  class='quitar' value='  '/>
						</form></td>
					</tr>";
			}	
			echo "</table></form>";
?>

		<!--PAGINADOR-->
		<div class="contenedor-pag">
		<ul class="paginador">
		<li class="<?php echo $_GET['pag']==1 ? 'desactivado' : '' ?>">
				<a href="paginador.php?pag=<?php echo $_GET['pag']-1?>"><img style="width: 23px" src="img/anterior.png"></a>
			</li>
				<!--con un bucle mostramos todas las páginas que hay-->
				<?php for($i=0;$i<$paginas;$i++): ?>
			<li  class="<?php echo $_GET['pag']==$i+1 ? 'activado' : ' '?>">
				<a href="paginador.php?pag=<?php echo $i+1 ?>">	
					<?php echo $i+1 ?>	
				</a>
			</li>

				<?php endfor ?>

				<!--Para ir a la página siguiente en el enlace ponemos que le lleve a la página actual +1-->
			<li class="<?php echo $_GET['pag']>=$paginas ? 'desactivado' : '' ?>">
				<a href="paginador.php?pag=<?php echo $_GET['pag']+1?>"><img style="width: 23px" src="img/siguiente.png"></a>
			</li>				
		</ul>
				</div>
	</div>
	</body>
</html>

<script type="text/javascript">
    //Esta sentencia de js nos permite que el mensaje de que el cliente ya está registrado o de que
    //el cliente se ha registrado aparezca solo durante unos segundos
    $('#mensaje').delay(2500).fadeOut(600); 
	let fondo=document.getElementById("todo");

$(document).mouseup(function(e) 
{
    var container = $("#container");
    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        container.hide();
		fondo.style.opacity = "0.9"; 
    }
});
</script>
