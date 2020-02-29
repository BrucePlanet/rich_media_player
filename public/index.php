<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (!isset($_SERVER["HTTP_REFERER"])){
        $_SERVER["HTTP_REFERER"] = "";
    }

    try {
        /**
         * Read the configuration
         */
        if ($_SERVER["ENV"] == "DEV") {
// Get the app's config / runtime parameters.
            switch ($_SERVER['SERVER_ADDR']) {
                // DEV Asset Server 1 //
                case "192.168.10.11":
                    $config = include __DIR__ . "/../app/config/dev_ass_constants.php";
                    break;
                   // DEV Asset Server Timico.
                case "192.168.20.10":
                    $config = include __DIR__ . "/../app/config/dev_tim_constants.php";
                    break;
            }
        }

        if ($_SERVER["ENV"] == "TEST") {
// Get the app's config / runtime parameters.
            switch ($_SERVER['SERVER_ADDR']) {
                // TEST Asset Server 1 //
                case "192.168.10.201":
                    $config = include __DIR__ . "/../app/config/tes_ass_constants.php";
                    break;
                // TEST Asset Server Timico.
                case "192.168.21.10":
                    $config = include __DIR__ . "/../app/config/tes_tim_constants.php";
                    break;
                // TEST Asset Server Timico.
                case "192.168.21.13":
                    $config = include __DIR__ . "/../app/config/tes_tim_constants.php";
                    break;
            }
        }

        if ($_SERVER["ENV"] == "LIVE") {
// Get the app's config / runtime parameters.
            switch ($_SERVER['SERVER_ADDR']) {
                // DEV Asset Server 1 //
                case "10.0.1.195":
                    $config = include __DIR__ . "/../app/config/ass_constants.php";
                    break;
                // DEV Asset Server Timico.
                case "192.168.0.10":
                    $config = include __DIR__ . "/../app/config/tim_constants.php";
                    break;
                // DEV Asset Server Timico.
                case "192.168.0.11":
                    $config = include __DIR__ . "/../app/config/tim_constants.php";
                    break;
            }
        }

        /**
         * Read the configuration
         */
        $config = include __DIR__ . "/../app/config/config.php";
        /**
         * Read auto-loader
         */
        include __DIR__ . "/../app/config/loader.php";
        /**
         * Read services
         */
        include __DIR__ . "/../app/config/services.php";
        /**
         * Handle the request
         */
        $application = new \Phalcon\Mvc\Application($di);

        echo $application->handle()->getContent();

    } catch (\Exception $e) {



        error_log(print_r(debug_backtrace(), true));

        $errorHandler = new ErrorHandlerController;
        $errorHTML = $errorHandler->displaySorryMsg("DB query here");
        echo '$("#RMPContentDiv").html(\''.$errorHTML.'\');';
        echo '$("#RMPContentDiv").css(\'float\',\'left\');';
        die();

    }
