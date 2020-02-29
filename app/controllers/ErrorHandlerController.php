<?php
use Phalcon\Mvc\Controller;

class ErrorHandlerController {

    public function displaySorryMsg($strQuery)
    {
        header('Content-Type: image/png');
        header("HTTP/1.0 404 Not Found");
        $returnMSG = "<img src=". HTTP_PROTOCOL . LOADDOMAINNAME . "/assets/noimage/noimage.jpg style=\"margin: 0 auto\">";
        error_log(print_r($strQuery, true));
        echo $returnMSG;
        die();
    }

    public function noImageFound($loaddomainname) {
        header('Content-Type: image/png');
        header("HTTP/1.0 404 Not Found");
        $returnMSG = "<img src=". HTTP_PROTOCOL . LOADDOMAINNAME . "/assets/noimage/noimage.jpg style=\"margin: 0 auto\">";
        error_log(print_r($loaddomainname, true));
        echo $returnMSG;
        die();

    }

}