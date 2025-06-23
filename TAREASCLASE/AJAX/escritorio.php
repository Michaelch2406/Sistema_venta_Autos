<?php
require("../MODELOS/usuarios.php");
$usuario = new Usuario();

switch ($_GET["op"]) {
    case "listar":
        $result = $usuario->listar();
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $data[] = array(
                "<img src='../PUBLIC/Img/edit.png' class='editar-usuario' width='20' height='20' data-id='{$row[0]}' data-cedula='{$row[1]}' data-nombre='{$row[2]}' data-apellido='{$row[3]}' data-clave='{$row[4]}' data-usuario='{$row[5]}'/>",
                $row[0],
                $row[1],
                $row[2],
                $row[3],
                $row[4],
                $row[5]
            );
        }
        
        $sEcho = isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1;
        // Si listar() no implementa paginación/filtrado del lado del servidor,
        // iTotalRecords e iTotalDisplayRecords serán el total de filas devueltas.
        $totalRecords = count($data); // O idealmente un COUNT(*) de la DB para iTotalRecords

        $results_array = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $totalRecords, 
            "iTotalDisplayRecords" => $totalRecords, 
            "aaData" => $data
        );
        echo json_encode($results_array);
        break;

    case "combo_usu":
        $result = $usuario->listar();
        echo '<option value="">Seleccione un usuario</option>';
        while ($f = $result->fetch_array(MYSQLI_ASSOC)) { // Usar MYSQLI_ASSOC para combo
            echo "<option value='{$f['usu_id']}' data-cedula='{$f['usu_cedula']}' data-nombre='{$f['usu_nombre']}' data-apellido='{$f['usu_apellido']}' data-usuario='{$f['usu_usuario']}'>{$f['usu_nombre']} {$f['usu_apellido']}</option>";
        }
        break;
}
?>