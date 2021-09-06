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
		$check_rfc = mainModel::ejecutar_consulta_simple("SELECT usuario_dni FROM usuario WHERE usuario_dni='$rfc'");
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
		$url = SERVERURL . $url . "/";
		$busqueda = mainModel::limpiar_cadena($busqueda);
		$tabla = "";
		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio  = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if (isset($busqueda) && $busqueda != "") {
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE ((usuario_id!='$id' AND usuario_id!='1') AND (usuario_rfc LIKE %$busqueda% OR usuario_nombre LIKE %$busqueda% OR usuario_apellido LIKE %$busqueda% OR usuario_telefono LIKE %$busqueda% OR usuario_direccion LIKE %$busqueda% OR usuario_email LIKE %$busqueda% OR usuario_usuario LIKE %$busqueda% OR )) ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
		} else {
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE usuario_id!='$id' AND usuario_id!='1' ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
		}

		$conexion = mainModel::conectar();
		$datos = $conexion->query($consulta);
		$datos = $datos->fetchAll();

		$total = $conexion->query("SELECT FOUND_ROWS");
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

		if ($total >= 1 && $Npaginas ) {
			# code...
		} else {
			# code...
		}
		$tabla .= '</tbody></table></div>';
	}
}
