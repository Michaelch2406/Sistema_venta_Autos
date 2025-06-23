<?php
	require_once "global.php";
	class conexion{
		function conecta(){
			if(!($conexion1=new MySQLi(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME))){
				echo "Error conectando a la base de datos";
				exit();
			}
			return $conexion1;
		}
		function ejecutarSP($sql){ 
            $Cn = $this->conecta();
            if (!$Cn) {
                return false;
            }
            $query = $Cn->query($sql);
            if (!$query) {
                error_log("Error en la consulta SQL: " . $Cn->error . " (SQL: " . $sql . ")");
            }
            $Cn->close();
            return $query;
        }
	}
?>