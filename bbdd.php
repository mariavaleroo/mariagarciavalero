<html>
    <head>
        <style>
            body{
                background:#004c45;
                color:white;
                margin: 0px;
    padding: 0px;
    font-family: Nunito,sans-serif;
    font-size: 18px;
    font-weight: 400;
    line-height: 1.5;
    margin: 0;
    text-align: left;
    display: flex;
    align-items: center;
    justify-content: center;
            }
            a{
                text-decoration:underline;
                color:white
            }
        </style>
</head>
</html>
<?php
try {
    $bd="";
    $username="root";
    $password="";
    $servername="localhost";
    $oConBD =new PDO("mysql:host=$servername;dbname=$bd;charset=utf8",$username,$password);
    $oConBD->exec("CREATE USER 'agenda'@'localhost' IDENTIFIED BY '2DAWdwes';");
    $oConBD->exec("GRANT ALL PRIVILEGES ON `agenda`.* TO 'agenda'@'localhost';");
    $oConBD->exec("FLUSH PRIVILEGES;");
    $bd1=""; 
    $username1="agenda";
    $password1="2DAWdwes";
    $servername1="localhost";
    $oConBD1 =new PDO("mysql:host=$servername1;dbname=$bd1;charset=utf8",$username1,$password1);
    $oConBD1->exec("CREATE DATABASE if not exists agenda ;");
    $oConBD1->exec("CREATE TABLE `agenda`.`agenda` ( `codigo` INT(6) NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(50), `telefono` VARCHAR(12), `fechaNac` DATE , `correo` VARCHAR(25), PRIMARY KEY (`codigo`)) ENGINE = InnoDB;"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'maria', '632010120', '2021-01-01', 'maria@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'pepa', '636987452', '2022-10-02', 'pepa@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'juana', '632147896', '2020-02-03', 'juana@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'lucas', '630123654', '2019-03-04', 'lucas@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'mateo', '698532569', '2018-04-05', 'mateo@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'sofia', '654789654', '2017-05-06', 'sofia@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'javier', '632145632', '2016-06-07', 'javier@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'juan', '615951475', '2015-07-08', 'juan@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'pepe', '635753210', '2014-08-09', 'pepe@prueba.com');"); 
    $oConBD1->exec("INSERT INTO `agenda`.`agenda` (`codigo`, `nombre`, `telefono`, `fechaNac`, `correo`) VALUES (NULL, 'marta', '60001250', '2013-09-10', 'marta@prueba.com');"); 
    $oConBD1->exec("CREATE TABLE `agenda`.`usuarios` (`usuario` VARCHAR(50) NOT NULL, `contrasena` VARCHAR(100) NOT NULL) ENGINE = InnoDB;"); 
    $contra=password_hash("PHP4ever", PASSWORD_DEFAULT);
    $oConBD1->exec("INSERT INTO `agenda`.`usuarios` (`usuario`, `contrasena`) VALUES ('amiguis', '$contra');"); 
        header('Location: login.php');
} catch (PDOException $e) {
   echo "<div><br>Si ya tienes la base de datos pulsa <a href='login.php'>aquí</a></div>";
   return false;
}
?>
