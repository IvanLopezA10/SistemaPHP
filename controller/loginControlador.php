<?php

	if($peticionAjax){
		require_once "../models/loginModelo.php";
	}else{
		require_once "./models/loginModelo.php";
	}

	class loginControlador extends loginModelo{
         /*--------- Controlador para iniciar sesion ---------*/
         public function iniciar_sesion_controlador(){
             $usuario = mainModel::limpiar_cadena($_POST['usuario_log']);
             $clave = mainModel::limpiar_cadena($_POST['clave_log']);

             /*== Comprobando campos vacios ==*/
             if($usuario == "" || $clave == ""){
                echo '<script>
                    Swal.fire({
                        title: "Ocurrio un error inesperado",
                        text: "No ha llenado todos los campos requeridos",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                <script>';
                exit();
             }
             /*== Verificar integridad de los datos ==*/
             if(mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)){
                echo '<script>
                    Swal.fire({
                        title: "Ocurrio un error inesperado",
                        text: "El Nombre Usuario no coincide con el formato solicitado",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                <script>';
                exit();
             }
             if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{5,100}",$clave)){
                echo '<script>
                    Swal.fire({
                        title: "Ocurrio un error inesperado",
                        text: "La Clave no coincide con el formato solicitado",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                <script>';
                exit();
             }
             $clave = mainModel::encryption($clave);

             $datos_login = [
                "Usuario" => $usuario,
                "Clave" => $clave
             ];
             $datos_cuenta = loginModelo::iniciar_sesion_modelo($datos_login);

             if ($datos_cuenta ->rowCount() ==1) {               
                $row= $datos_cuenta ->fetch();
                session_start(['name'=>'Sistema']);
                $_SESSION['id_sistema'] =$row['usuario_id'];
                $_SESSION['nombre_sistema'] =$row['usuario_nombre'];
                $_SESSION['apellido_sistema'] =$row['usuario_apellido'];
                $_SESSION['usuario_sistema'] =$row['usuario_usuario'];
                $_SESSION['privilegio_sistema'] =$row['usuario_privilegio'];
                $_SESSION['token_sistema'] =md5(uniqid(mt_rand(),true));

                return header("Location: ".SERVERURL."home/");
             } else {
                echo '<script>
                    Swal.fire({
                        title: "Ocurrio un error inesperado",
                    text: "El Usuario o Clave son incorrectos o no se encuentran en la base de datos",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                <script>';
             }
         } /* fin funcion */

         /*--------- Controlador forzar el cierre de para sesion ---------*/
         public function forzar_cierre_sesion_controlador(){
            session_unset();
            session_destroy();
            if (headers_sent()) {
                return "<script> window.location.href='".SERVERURL."login/';</script>";
            } else {
                return header("Location: ".SERVERURL."login/");
            }
            
         } /* fin funcion */

         /*--------- Controlador cierre de sesion ---------*/
         public function cerrar_sesion_controlador (){
            session_start(['name'=>'Sistema']);
            $token = mainModel::decryption($_POST['token']);
            $usuario = mainModel::decryption($_POST['usuario']);

            if ($token == $_SESSION['token_sistema'] && $usuario == $_SESSION['usuario_sistema']) {
                session_unset();
                session_destroy();
                $alerta = [
                    "Alerta" => "redireccionar",
                    "URL" => SERVERURL."login/"
                ];
            } else {
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error al cerrar la sesion",
					"Texto"=>"No se pudo cerrar la sesion en el sistema",
					"Tipo"=>"error"
				];
            }
            echo json_encode($alerta);
			exit();
         }
    }