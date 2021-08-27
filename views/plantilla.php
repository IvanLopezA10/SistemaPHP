<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title><?php echo COMPANY; ?></title>

	<!--=============================================
	=                      links                    =
	==============================================-->
    <?php include "./views/inc/link.php"; ?>

</head>
<body>
	
	<?php
		$peticionAjax = false;
		require_once "./controller/vistasControlador.php";
		$IV = new vistasControlador();
		$vistas = $IV->obtener_vistas_controlador();
		if($vistas == "login" || $vistas == "404"){
			require_once "./views/content/".$vistas."-view.php";
		}else{

	?>
	<!-- Main container -->
	<main class="full-box main-container">
		<!-- Nav lateral -->

        <?php  include "./views/inc/NavLateral.php"; ?>

		<!-- Page content -->
		<section class="full-box page-content">

			<?php 
				include "./views/inc/NavBar.php"; 
				include $vistas;
			?>

		</section>
	</main>
	<!--=============================================
	=            Include JavaScript files           =
	==============================================-->
	<?php 
		}
		include "./views/inc/Scripts.php"; 
	?>
</body>
</html>
    