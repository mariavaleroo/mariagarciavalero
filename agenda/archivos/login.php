<?php session_start();
    if((isset($_COOKIE['contrasena']))&&(isset($_COOKIE['usuario']))){
        header ('Location: paginador.php');

    }else{

        $contra1="";
        $usu1="";
        
    }
?>

<html>

<head>	
    <link rel=StyleSheet href="../archivos/style.css" type="text/css" media=screen>
</head>
<body style="background:#004c45;    display: flex;
    align-items: center;
    justify-content: center;
">
<div class='contenedor'>
<form action="" method="post"  >
        <input style="    font-size: 18px;" type="text" name="cod" placeholder="🙎‍♂️Usuario" value="<?= isset($_COOKIE['usuario']) ? $_COOKIE['usuario'] : $usu1 ?>" required><br>
        <input style="    font-size: 18px;" type="password" name="con"  placeholder="🔒Contraseña" required value="<?= isset($_COOKIE['contrasena']) ? $_COOKIE['contrasena'] : $contra1 ?>"><br>
        <div style="    display: flex;
    align-items: center;"><input style="    font-size: 18px;" type="checkbox" name="recordar" value="recordar"><br><p style="font-size: 18px;">Recordar contraseña</p> </div>
         <input style="    font-size: 18px;" type="submit" name="btnEnviar" value="Iniciar sesión" class='eliminar-varios'>

</form>
<form action="" method="post">
<input  style="    font-size: 18px;" type="submit" name="btnRegistrar" value="Registrarme" class='eliminar-varios'>

</form>
</div>
</body>
</html>


<?php



include "../archivos/conexion1.php"; 
if (isset($_POST['recordar'])) {
    $contrasena=$_POST["con"];
    $usuario=$_POST["cod"];
    setcookie("contrasena",$contrasena,time()+600);
    setcookie("usuario",$usuario,time()+600);

}
    if(isset($_POST["btnEnviar"])){ 

        
        $consulta="SELECT contrasena,usuario FROM usuarios WHERE usuario=:cod;";
            $sql=$conn ->prepare($consulta);
            $sql->bindParam(':cod',($_POST['cod']));
            $sql->execute();
            $consulta=$sql->fetchALL(PDO::FETCH_ASSOC);
           if($consulta>0){
                foreach($consulta as $consulta1){
                    $contra=$_POST['con'];
                    $descifrada=password_verify($contra, $consulta1['contrasena']);
                    if($descifrada==1){
                        $_SESSION['contra']=$descifrada;
                        header('Location: paginador.php');
                    }else{

                        echo "<div class='alerta2' id='mensaje'>Usuario o contraseña incorrecto</div>";
                    }
                }
            }else{
                echo "<div class='alerta2' id='mensaje'>Usuario o contraseña incorrecto</div>";
            }
    }



    if (isset($_POST['btnRegistrar'])) {
        ?>

<html><head><style>.contenedor{display:none}</style></head></html>
<?php
        echo "   <div style='   font-size: 18px;'class='contenedor2'>
        <form action=''  method='post'  >
                <input style='   font-size: 18px;' type='text' name='cod' placeholder='🙎‍♂️Usuario'  required><br>
                <input style='   font-size: 18px;' type='password' name='con1' minlength='6' placeholder='🔒Contraseña'><br>
                <input style='   font-size: 18px;' type='password' name='con2' minlength='6'  placeholder='🔒Repetir contraseña'><br>
                <input style='   font-size: 18px;'  type='submit' class='eliminar-varios' name='btnRegistrar2' value='Registrarme'>
        </form>
        <form action='' method='post'>                
        <input style='   font-size: 18px;' type='submit' class='eliminar-varios' name='cancelar' value='Cancelar'></input>
        </form>        
</div>";
    }
    if(isset($_POST['cancelar'])){
		header("Location: paginador.php");
	}
if(isset($_POST["btnRegistrar2"])){ 
    $contrasena1=$_POST['con1'];
    $contrasena2=$_POST['con2'];
    if($contrasena1==$contrasena2){

          
        $consulta="SELECT contrasena,usuario FROM usuarios WHERE usuario=:cod;";
            $sql=$conn ->prepare($consulta);
            $sql->bindParam(':cod',($_POST['cod']));
            $sql->execute();
            $consulta=$sql->fetchALL();
           if($consulta>0){
               echo "<div class='alerta2' id='mensaje'>Usuario ya registrado</div>";
           }
           if($consulta==null){
               //desciframos la contraseña para comprobar si es igual a lo que e usuario ha introducido
            $contra=password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
            $usu=$_POST['cod'];
            $conn->exec("INSERT INTO `agenda`.`usuarios` (`usuario`, `contrasena`) VALUES ('$usu', '$contra');"); 
            header('Location: ../archivos/login.php');
        }
    }
    if($contrasena1!=$contrasena2){
        echo "<div class='alerta2' id='mensaje'>Las contraseñas deben ser iguales</div>";
    }
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
    //Esta sentencia de js nos permite que el mensaje de que el cliente ya está registrado o de que
    //el cliente se ha registrado aparezca solo durante unos segundos
    $('#mensaje').delay(1000).fadeOut(600); 
</script>