<?php

if ($peticionAjax) {
	require_once "../models/usuarioModelo.php";
} else {
	require_once "./models/usuarioModelo.php";
}

class usuarioControlador extends usuarioModelo
{

	/*--------- Controlador agregar usuario ---------*/
	public function agregar_usuario_controlador()
	{
		$rfc = mainModel::limpiar_cadena($_POST['usuario_rfc_reg']);
		$nombre = mainModel::limpiar_cadena($_POST['usuario_nombre_reg']);
		$apellido = mainModel::limpiar_cadena($_POST['usuario_apellido_reg']);
		$telefono = mainModel::limpiar_cadena($_POST['usuario_telefono_reg']);
		$direccion = mainModel::limpiar_cadena($_POST['usuario_direccion_reg']);

		$usuario = mainModel::limpiar_cadena($_POST['usuario_usuario_reg']);
		$email = mainModel::limpiar_cadena($_POST['usuario_email_reg']);
		$clave1 = mainModel::limpiar_cadena($_POST['usuario_clave_1_reg']);
		$clave2 = mainModel::limpiar_cadena($_POST['usuario_clave_2_reg']);

		$privilegio = mainModel::limpiar_cadena($_POST['usuario_privilegio_reg']);

		/*== comprobar campos vacios ==*/
		if ($rfc == "" || $nombre == "" || $apellido == "" || $usuario == "" || $clave1 == "" || $clave2 == "") {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No has llenado todos los campos que son obligatorios",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*== Verificando integridad de los datos ==*/
		if (mainModel::verificar_datos("[A-Z0-9-]{5,20}", $rfc)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El RFC no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $nombre)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El NOMBRE no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $apellido)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El APELLIDO no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if ($telefono != "") {
			if (mainModel::verificar_datos("[0-9()+]{8,20}", $telefono)) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "El TELEFONO no coincide con el formato solicitado",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if ($direccion != "") {
			if (mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}", $direccion)) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "La DIRECCION no coincide con el formato solicitado",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}", $usuario)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El NOMBRE DE USUARIO no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{5,100}", $clave1) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{5,100}", $clave2)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "Las CLAVES no coinciden con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*== Comprobando DNI ==*/
		$check_rfc = mainModel::ejecutar_consulta_simple("SELECT usuario_rfc FROM usuario WHERE usuario_rfc='$rfc'");
		if ($check_rfc->rowCount() > 0) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El RFC ingresado ya se encuentra registrado en el sistema",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*== Comprobando usuario ==*/
		$check_user = mainModel::ejecutar_consulta_simple("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
		if ($check_user->rowCount() > 0) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El NOMBRE DE USUARIO ingresado ya se encuentra registrado en el sistema",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*== Comprobando email ==*/
		if ($email != "") {
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$check_email = mainModel::ejecutar_consulta_simple("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
				if ($check_email->rowCount() > 0) {
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error inesperado",
						"Texto" => "El EMAIL ingresado ya se encuentra registrado en el sistema",
						"Tipo" => "error"
					];
					echo json_encode($alerta);
					exit();
				}
			} else {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "Ha ingresado un correo no valido",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*== Comprobando claves ==*/
		if ($clave1 != $clave2) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "Las claves que acaba de ingresar no coinciden",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		} else {
			$clave = mainModel::encryption($clave1);
		}

		/*== Comprobando privilegio ==*/
		if ($privilegio < 1 || $privilegio > 3) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El privilegio seleccionado no es valido",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		$datos_usuario_reg = [
			"RFC" => $rfc,
			"Nombre" => $nombre,
			"Apellido" => $apellido,
			"Telefono" => $telefono,
			"Direccion" => $direccion,
			"Email" => $email,
			"Usuario" => $usuario,
			"Clave" => $clave,
			"Estado" => "Activa",
			"Privilegio" => $privilegio
		];

		$agregar_usuario = usuarioModelo::agregar_usuario_modelo($datos_usuario_reg);

		if ($agregar_usuario->rowCount() == 1) {
			$alerta = [
				"Alerta" => "limpiar",
				"Titulo" => "usuario registrado",
				"Texto" => "Los datos del usuario han sido registrados con exito",
				"Tipo" => "success"
			];
		} else {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No hemos podido registrar el usuario",
				"Tipo" => "error"
			];
		}
		echo json_encode($alerta);
	} /* Fin controlador */

	/*--------- Controlador paginar usuarios ---------*/
	public function paginador_usuario_controlador($pagina, $registros, $privilegio, $id, $url, $busqueda)
	{

		$pagina = mainModel::limpiar_cadena($pagina);
		$registros = mainModel::limpiar_cadena($registros);
		$privilegio = mainModel::limpiar_cadena($privilegio);
		$id = mainModel::limpiar_cadena($id);
		$url = mainModel::limpiar_cadena($url);
		$url = SERVERURL.$url."/";
		$busqueda = mainModel::limpiar_cadena($busqueda);
		$tabla = "";
		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio  = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if (isset($busqueda) && $busqueda != "") {
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE ((usuario_id!='$id' AND usuario_id!='1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_rfc LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_telefono LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%')) ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
		} else {
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE usuario_id!='$id' AND usuario_id!='1' ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
		}

		$conexion = mainModel::conectar();
		$datos = $conexion->query($consulta);
		$datos = $datos->fetchAll();

		$total = $conexion->query("SELECT FOUND_ROWS()");
		$total = (int) $total->fetchColumn();
		$Npaginas = ceil($total / $registros);

		$tabla .= '<div class="table-responsive">
			<table class="table table-dark table-sm">
				<thead>
					<tr class="text-center roboto-medium">
						<th>#</th>
						<th>RFC</th>
						<th>NOMBRE</th>
						<th>APELLIDO</th>
						<th>TELÉFONO</th>
						<th>USUARIO</th>
						<th>EMAIL</th>
						<th>ACTUALIZAR</th>
						<th>ELIMINAR</th>
					</tr>
				</thead>
				<tbody>';

		if ($total >= 1 && $pagina <= $Npaginas) {
			$contador = $inicio + 1;
			$reg_inicio = $inicio + 1;
			foreach ($datos as $rows) {
				$tabla .= '
				<tr class="text-center" >
					<td>'.$contador.'</td>
					<td>'.$rows['usuario_rfc'].'</td>
					<td>'.$rows['usuario_nombre'].'</td>
					<td>'.$rows['usuario_apellido'].'</td>
					<td>'.$rows['usuario_telefono'].'</td>
					<td>'.$rows['usuario_usuario'].'</td>
					<td>'.$rows['usuario_email'].'</td>
					<td>
						<a href="'.SERVERURL.'user-update/'.mainModel::encryption($rows['usuario_id']).'/" class="btn btn-success">
								<i class="fas fa-sync-alt"></i>	
						</a>
					</td>
					<td>
						<form class="FormularioAjax" action="'.SERVERURL.'ajax/usuarioAjax.php" method="POST" data-form="delete" autocomplete="off">
						<input type="hidden" name = "usuario_id_del" value="'.mainModel::encryption($rows['usuario_id']).'">
							<button type="submit" class="btn btn-warning">
									<i class="far fa-trash-alt"></i>
							</button>
						</form>
					</td>
				</tr>';
				$contador++;
			}
			$reg_final = $contador - 1;
		} else {
			if ($total >= 1) {
				$tabla .= '<tr class="text-center"><td colspan="9">
					<a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">Haga click pra recargar el listado </a>
				<tr>';
			} else {
				$tabla .= '<tr class="text-center"><td colspan="9">No hay registros en el sistema </td><tr>';
			}
		}

		$tabla .= '</tbody></table></div>';

		if ($total >= 1 && $pagina <= $Npaginas) {
			$tabla.= '<p class = "text-right">Mostrando usuario'.$reg_inicio.' al '.$reg_final.' de un total de '.$total.'</p>';

			$tabla .=mainModel::paginador_tablas($pagina,$Npaginas,$url,7);
		}
		return $tabla;
	}/* fin funcion */


	/*--------- Controlador eliminar usuarios ---------*/
	public function eliminr_usuario_controlador(){
		/* recibiendo id del usuario */
		$id=mainModel::decryption($_POST['usuario_id_del']);
		$id=mainModel::limpiar_cadena($id);
		/* comprovando el usuario */
		if ($id == 1) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No se puede eliminar el usuario principal del sistema",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/* comprovando el usuario en la bd */
		$check_usuario = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM usuario WHERE usuario_id = '$id'");
		if ($check_usuario -> rowCount() <= 0) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El usuario que intenta eliminar no existe en el sistema",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/* comprovando los proyectos relacionados con el usuario*/
		$check_proyectos = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM proyecto WHERE usuario_id = '$id' LIMIT 1");
		if ($check_proyectos -> rowCount() > 0) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No se puede eliminar este usuario debido a que tiene proyectos asociados, recomendamos deshabilitar el usuario si ya no sera utilizado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
		/* comprovando los privilegios del usuario */
		session_start(['name' => 'Sistema']);
		if ($_SESSION['privilegio_sistema']!=1) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No tienes los permisos necesarios pra realizar esta operacion",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		$eliminar_usuario = usuarioModelo::eliminar_usuario_modelo($id);

		if ($eliminar_usuario -> rowCount()==1) {
			$alerta = [
				"Alerta" => "recargar",
				"Titulo" => "Usuario Eliminado",
				"Texto" => "El usuario ha sido eliminado del sistema exitosamente",
				"Tipo" => "success"
			];
			echo json_encode($alerta);
			exit();
		} else {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No se pudo eliminar el usuario, por favor intente nuevamente",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}	
	}/* fin funcion */

	/*--------- Controlador datos usuarios ---------*/
	public function datos_usuario_controlador($tipo,$id){
		$tipo = mainModel::limpiar_cadena($tipo);
		$id = mainModel::decryption($id);
		$id = mainModel::limpiar_cadena($id);

		return usuarioModelo::datos_usuario_modelo($tipo,$id);
	}/* fin funcion */


	/*--------- Controlador actualizar usuario ---------*/
	public function actualizar_usuario_controlador(){
		//Recibir el id para desencriptar y limpar la cadena para evitar sql inyecction
		$id = mainModel::decryption($_POST['usuario_id_update']);
		$id = mainModel::limpiar_cadena($id);

		// comprobar usuario en la bd con el id
		$check_user = mainModel::ejecutar_consulta_simple("SELECT * FROM usuario WHERE usuario_id = '$id'");
		if ($check_user -> rowCount() <= 0) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No hemos encontrado el usuario en el sistema",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		} else {
			$campos = $check_user -> fetch();
		}
		
		$rfc = mainModel::limpiar_cadena($_POST['usuario_rfc_up']);
		$nombre = mainModel::limpiar_cadena($_POST['usuario_nombre_up']);
		$apellido = mainModel::limpiar_cadena($_POST['usuario_apellido_up']);
		$telefono = mainModel::limpiar_cadena($_POST['usuario_telefono_up']);
		$direccion = mainModel::limpiar_cadena($_POST['usuario_direccion_up']);
		$usuario = mainModel::limpiar_cadena($_POST['usuario_usuario_up']);
		$email = mainModel::limpiar_cadena($_POST['usuario_email_up']);

		if (isset($_POST['usuario_estado_up'])) {
			$estado = mainModel::limpiar_cadena($_POST['usuario_estado_up']);
		} else {
			$estado = $campos['usuario_estado'];
		}

		if (isset($_POST['usuario_privilegio_up'])) {
			$privilegio = mainModel::limpiar_cadena($_POST['usuario_privilegio_up']);
		} else {
			$privilegio = $campos['usuario_privilegio'];
		}

		$admin_usuario = mainModel::limpiar_cadena($_POST['usuario_admin']);
		$admin_clave = mainModel::limpiar_cadena($_POST['clave_admin']);
		
		$tipo_cuenta = mainModel::limpiar_cadena($_POST['tipo_cuenta']);
		
		/*== comprobar campos vacios ==*/
		if ($rfc == "" || $nombre == "" || $apellido == "" || $usuario == "" || $admin_usuario == "" || $admin_clave == "") {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No has llenado todos los campos que son obligatorios",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*== Verificando integridad de los datos ==*/
		if (mainModel::verificar_datos("[A-Z0-9-]{5,20}", $rfc)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El RFC no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $nombre)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El NOMBRE no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $apellido)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El APELLIDO no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if ($telefono != "") {
			if (mainModel::verificar_datos("[0-9()+]{8,20}", $telefono)) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "El TELEFONO no coincide con el formato solicitado",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if ($direccion != "") {
			if (mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}", $direccion)) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "La DIRECCION no coincide con el formato solicitado",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}", $usuario)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El NOMBRE DE USUARIO no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}", $admin_usuario)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "Tu NOMBRE DE USUARIO no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{5,100}", $admin_clave)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "Tu CLAVE no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		$admin_clave = mainModel::encryption($admin_clave);

		if ($privilegio < 1 || $privilegio > 3) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El privilegio no corresponde a un valor valido",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
		
		if ($estado != 'Activa' && $estado != 'Deshabilitada') {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El estado de la cuenta no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
		
		/*== Comprobando rfc ==*/
		if($rfc != $campos['usuario_rfc']){
			$check_rfc = mainModel::ejecutar_consulta_simple("SELECT usuario_rfc FROM usuario WHERE usuario_rfc='$rfc'");
			if ($check_rfc->rowCount() > 0) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "El RFC ingresado ya se encuentra registrado en el sistema",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*== Comprobando usuario ==*/
		if ($usuario != $campos['usuario_usuario']) {
			$check_user = mainModel::ejecutar_consulta_simple("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
			if ($check_user->rowCount() > 0) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "El NOMBRE DE USUARIO ingresado ya se encuentra registrado en el sistema",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*== Comprobando email ==*/
		if ($email != $campos['usuario_email'] && $email != "") {
			if (filter_var($email,FILTER_VALIDATE_EMAIL)) {
				$check_email = mainModel::ejecutar_consulta_simple("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
				if ($check_email->rowCount() > 0) {
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error inesperado",
						"Texto" => "El nuevo correo ingresado ya se encuentra registrado en el sistema",
						"Tipo" => "error"
					];
					echo json_encode($alerta);
					exit();
				}
			} else {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "Ha ingresado un correo no valido",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}			
		}

		/*== Comprobando claves ==*/
		if($_POST['usuario_clave_nueva_1'] != "" && $_POST['usuario_clave_nueva_2'] != ""){
			if ($_POST['usuario_clave_nueva_1'] != $_POST['usuario_clave_nueva_2']) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "Las nuevas claves ingresadas son distintas",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			} else {
				if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{5,100}", $_POST['usuario_clave_nueva_1']) || $_POST['usuario_clave_nueva_2']){
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error inesperado",
						"Texto" => "Las nuevas claves ingresadas no coiden con el formato solicitado",
						"Tipo" => "error"
					];
					echo json_encode($alerta);
					exit();
				}
				$clave = mainModel::encryption($_POST['usuario_clave_nueva_1']);
			}
		}else {
			$clave = $campos['usuario_clave'];
		}


		/*== Comprobando credenciales para actualizar datos  ==*/
		if ($tipo_cuenta == "Propia") {
			$check_cuenta = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM usuario WHERE usuario_usuario='$admin_usuario' AND usuario_clave = '$admin_clave' AND usuario_id = '$id'");
		} else {
			session_start(['name'=>'Sistema']);
			if ($_SESSION['privilegio_sistema'] != 1) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "No tienes los permisos necesarios para actualizar los datos",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
			$check_cuenta = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM usuario WHERE usuario_usuario='$admin_usuario' AND usuario_clave = '$admin_clave'");
		}
		if ($check_cuenta -> rowCount()<=0) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "El Nombre y Clave de administrador no son validos",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*== Preparando datos para enviarlos al modelo  ==*/
		$datos_usuario_up = [
			"RFC" =>$rfc,
			"Nombre" => $nombre,
			"Apellido" => $apellido,
			"Telefono" => $telefono,
			"Direccion" => $direccion,
			"Email" => $email,
			"Usuario" => $usuario,
			"Clave" => $clave,
			"Estado" => $estado,
			"Privilegio" => $privilegio,
			"ID" => $id
		];
		if (usuarioModelo::actualizar_usuario_modelo($datos_usuario_up)) {
			$alerta = [
				"Alerta" => "recargar",
				"Titulo" => "Datos actualizados",
				"Texto" => "Los Datos han sido actualizados con exito",
				"Tipo" => "success"
			];
			
		}else {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No hemos podido Actualizar los datos, por favor intente nuevamente",
				"Tipo" => "error"
			];
		}
		echo json_encode($alerta);
	}/* fin funcion */
}
