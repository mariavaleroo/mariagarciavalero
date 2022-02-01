<?php 
session_start();
include "../archivos/conexion1.php"; 

if (isset($_COOKIE['contrasena'])||isset($_SESSION['contra'])){
}else{
	header('Location: /login.php');
	die() ;
}
?>
<html>
	<head>
	<link rel=StyleSheet href="../archivos/style.css" type="text/css" media=screen>

	</head>
	<body>
	<div class="todo">
        <a href="paginador.php" ><img src="img/volver.png" class="volver" alt="volver atrás"></a>
<?php

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
					echo "<div class='alerta' id='mensaje'>El teléfono o correo ya se encuentra registrado</div>";
				}else{
					for($i=1;$i<count($nombress);$i++){
						$nombress[$i][0];
						$sql="UPDATE agenda SET  ".$nombress[$i][0]."=:nom  WHERE codigo=".$_POST['dato'][0].";";
						$stmt=$conn->prepare($sql);
						$stmt->bindParam(":nom",$_POST['dato'][$i]);
						$stmt->execute();
					}
					echo "<div class='alerta' id='mensaje'>Contacto actualizado</div>";
				}	
			}else{
				echo "<div class='alerta' id='mensaje'>El teléfono debe ser numérico y el correo debe contener '@' y '.' </div>";

			}				
	};

//BORRAR
	if(isset($_POST['borrar'])){
		if (empty($_POST['eliminar'])){
			echo '<h1>No se ha seleccionado ningun registro</h1>';
		}else{
			foreach($_POST['eliminar'] as $id_borrar){
				//creamos la consulta que borra todos los contactos con los id que se han pasado y que nos redirige una vez borrados al inicio
				$borrar=$conn->query("DELETE FROM agenda WHERE codigo='$id_borrar'");
				header('Location: buscar.php');
			}
		}
	};
$nombre=$_SESSION['nombre'];
	$sql="SELECT * FROM agenda WHERE nombre LIKE '%$nombre%'";
	$sentencia= $conn->prepare($sql);
	$sentencia->execute();
	$resultado=$sentencia->fetchAll();
	$contactos_x_pagina=6;
	$total_contactos_db=$sentencia->rowCount();
	$paginas=$total_contactos_db/$contactos_x_pagina;
	$paginas=ceil($paginas);
		
		//creamos un if para que si nos pasamos de página, es decir, estoy en la última página y le vuelvo a dar a siguiente que me lleve a la pagina 1
			if(!$_GET){
				header('Location: buscar.php?pag=1');
			}
			if($_GET['pag']>$paginas || $_GET['pag']<=0){
				header('Location: buscar.php?pag=1');

			}

		//Creamos la consulta para obtener los contactos de 6 en 6
			$iniciar=($_GET['pag']-1)*$contactos_x_pagina;
			$sql_contactos="SELECT * FROM agenda WHERE nombre LIKE '%$nombre%' LIMIT :inicar,:ncontactos";
			$sentencia_contactos=$conn->prepare($sql_contactos);
			$sentencia_contactos->bindParam(':inicar',$iniciar,  PDO::PARAM_INT);
			$sentencia_contactos->bindParam(':ncontactos',$contactos_x_pagina,  PDO::PARAM_INT);
			$sentencia_contactos->execute();
			$resultado_contactos=$sentencia_contactos->fetchAll();
				
		/*
		$sentencia_contactos=$conn->query("SELECT * FROM contactos WHERE nombre LIKE '%$id_buscar%'");
*/
				
		//mostramos la tabla con los contactos
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
				<a href="buscar.php?pag=<?php echo $_GET['pag']-1?>"><img style="width: 23px" src="img/anterior.png"></a>
			</li>
				<!--con un bucle mostramos todas las páginas que hay-->
				<?php for($i=0;$i<$paginas;$i++): ?>
			<li  class="<?php echo $_GET['pag']==$i+1 ? 'activado' : ' '?>">
				<a href="buscar.php?pag=<?php echo $i+1 ?>">	
					<?php echo $i+1 ?>	
				</a>
			</li>

				<?php endfor ?>

				<!--Para ir a la página siguiente en el enlace ponemos que le lleve a la página actual +1-->
			<li class="<?php echo $_GET['pag']>=$paginas ? 'desactivado' : '' ?>">
				<a href="buscar.php?pag=<?php echo $_GET['pag']+1?>"><img style="width: 23px" src="img/siguiente.png"></a>
			</li>				
		</ul>
				</div>
	</div>
	</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
    //Esta sentencia de js nos permite que el mensaje de que el cliente ya está registrado o de que
    //el cliente se ha registrado aparezca solo durante unos segundos
    $('#mensaje').delay(1000).fadeOut(600); 
</script>