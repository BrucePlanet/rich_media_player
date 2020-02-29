<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class IndexController extends ControllerBase
{
    /**
     * Shows the index action
     */
    public function indexAction()
    {

       $this->view->setVar("loaddomainname",$this->view->pageConfig->application->loaddomainname);
	   $this->view->setVar("protocol",$this->view->pageConfig->application->protocol);

	   $indexModel = new Index();
       $indexModel->getMgm();
    }


}

