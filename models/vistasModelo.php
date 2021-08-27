<?php
    class vistasModelo {

        /* -----Modelo obtener vistas------  */
        protected static function obtener_vistas_modelo($vistas){
            $listaBlanca = ["home","client-list"];
            if (in_array($vistas, $listaBlanca)) {
                if (is_file("./views/content/".$vistas."-view.php")) {
                    $contenido = "./views/content/".$vistas."-view.php";
                } else {
                    $contenido="404";
                }
                
            }else if($vistas == "login" || $vistas =="index") {
                $contenido = "login";
            }else{
                $contenido="404";
            }
            return $contenido;
        }
    }