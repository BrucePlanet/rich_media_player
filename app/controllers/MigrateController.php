
<?php

class MigrateController extends ControllerBase
{
    public function indexAction()
    {
        $migrateModel = new Migrate();
        $migrateSet = $migrateModel->migrateImages($this->view->pageConfig->application->assetCacheDir);
        
    }
    
    public function singleAction($productCode=null)
    {
    
        $migrateModel = new Migrate();
        $migrateSet = $migrateModel->migrateImages($this->view->pageConfig->application->assetCacheDir,"single",$productCode);
        
    }

}


?>
