<?php 
	$peticionAjax=true;
	require_once "../config/app.php";

	if(isset($_POST['usuario_rfc_reg']) || isset($_POST['usuario_id_del']) || isset($_POST['usuario_id_update'])){

		/*--------- Instancia al controlador ---------*/
		require_once "../controller/usuarioControlador.php";
		$ins_usuario = new usuarioControlador();


		/*--------- Agregar un usuario ---------*/
		if(isset($_POST['usuario_rfc_reg']) && isset($_POST['usuario_nombre_reg'])){
			echo $ins_usuario->agregar_usuario_controlador();
		}

		/*--------- Eliminar un usuario ---------*/
		if(isset($_POST['usuario_id_del'])){
			echo $ins_usuario->eliminr_usuario_controlador();
		}

		/*--------- Actualizar un usuario ---------*/
		if(isset($_POST['usuario_id_update'])){
			echo $ins_usuario->actualizar_usuario_controlador();
		}


		
	}else{
		session_start(['name'=>'Sistema']);
		session_unset();
		session_destroy();
		header("Location: ".SERVERURL."login/");
		exit();
	}

