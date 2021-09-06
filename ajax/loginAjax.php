<?php 
	$peticionAjax=true;
	require_once "../config/app.php";

	if (isset($_POST['token']) && isset($_POST['usuario'])) {
        /*--------- Instancia al controlador ---------*/
		require_once "../controller/loginControlador.php";
		$ins_login = new loginControlador();

		echo $ins_login ->cerrar_sesion_controlador();

    }else{
		session_start(['name'=>'Sistema']);
		session_unset();
		session_destroy();
		header("Location: ".SERVERURL."login/");
		exit();
	}