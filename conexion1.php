<?php

//Damos valor a las variables
$servername="localhost";
$username="agenda";
$password="2DAWdwes";


try{
    //Creamos un objeto PDO para que se conecte a nuestra bbdd
    $conn=new PDO("mysql:host=$servername;dbname=agenda;charset=utf8",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //echo "<br>conectado<br><br>";

    //Capturamos error en caso de error:
}catch(PDOException $e){
    echo "Conexión fallida".$e->getMessage();
}

?>
