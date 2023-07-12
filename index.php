<?php

error_reporting(0);


set_include_path( get_include_path()     . PATH_SEPARATOR . "controllers/"
                                                    . PATH_SEPARATOR . "controllers/project_controllers/"
                                                    . PATH_SEPARATOR . "models/"
                                                    . PATH_SEPARATOR . "models/project_models/"
                                                    . PATH_SEPARATOR . "views/"
                                                    . PATH_SEPARATOR . "views/templates/"
                                                    . PATH_SEPARATOR . "config/"
                                                    . PATH_SEPARATOR . "uploaded_files/" );

spl_autoload_register( function ($class)
    {
        (!include_once ($class . '.php')) ? require_once 'IndexController.php' : require_once ($class . '.php');
    }
);



session_start();
/*session_destroy();*/

$frontController = new FrontController();
$frontController->route();

