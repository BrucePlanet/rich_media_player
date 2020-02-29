
<?php

//use Phalcon\Mvc\Model\Criteria;
//use Phalcon\Paginator\Adapter\Model as Paginator;

class AssetController extends ControllerBase
{
    /**
     * Shows the index action
     */

    var $protocol = HTTP_PROTOCOL;
    var $loaddomainname = LOADDOMAINNAME;

    public function indexAction($customCSS,$channelCode,$productCode,$requestId,$orientationId,$initialwidth,$quailityTypeId,$mode="production")
    {
        $assetModel = new Asset();
        $assetModel->initDb($mode);
        $channelCSS = $customCSS;

        if ($channelCode == "KIT_1_15") {
            $layoutLeft = true;
        } else {
            $layoutLeft = false;
        }

        $urlData = parse_url($_SERVER["HTTP_REFERER"]);
        $hostData = explode('.', $urlData['host']);

        /***********************************************************************************************/
        /************************************* Referer Check********************************************/
        /***********************************************************************************************/

        $refererResult = $assetModel->confirmReferef($channelCode,$this->view->pageConfig->application->referer);

        if (!$refererResult) {

            echo "<img class=\"lb-image\" src=\"" . $this->protocol . $this->loaddomainname . "/img/denied.gif\" style=\"display: block; margin: 3px auto;  width: 20%;\">";
            die();
        }

        /***********************************************************************************************/
        /******************************************* RMP section ***************************************/
        /***********************************************************************************************/

        if (strrpos($_SERVER["HTTP_USER_AGENT"],"MSIE")!==false || strrpos($_SERVER["HTTP_USER_AGENT"],"Trident")!==false)
            $browser = "nonIE";
        else $browser = "nonIE";

        if ($browser == "IE") $allowzoom = false;
        else $allowzoom = true;

        $origWidth = $initialwidth;
        if ( 	($orientationId==1 && $initialwidth>0 && $initialwidth<=360)
            ||
            ($orientationId==2 && $initialwidth>0 && $initialwidth<=500)
        )
        {

            // the image with what we need it 500px
            $initialwidth = "500";
            $sizeTypeId = 2;

        }else{

            if ($initialwidth>=1000){
                $initialwidth = "1000";
                $sizeTypeId = 4;
            }else{
                $initialwidth = "700";
                $sizeTypeId = 3;
            }

        }

        $assetSet = $assetModel->getProductAssets($customCSS,$channelCode,$productCode,
            $initialwidth,
            $this->view->pageConfig->application->assetCacheDir,
            $this->view->pageConfig->application->galleryAssetCacheDir,
            $this->view->pageConfig->application->galleryAssetCacheDirRemote,
            $this->view->pageConfig->application->rotatorAssetDir,
            $this->view->pageConfig->application->rotatorAssetDirRemote,
            $this->view->pageConfig->application->xml360RotatorTemplate,
            $requestId,
            $sizeTypeId,
            $quailityTypeId,
            $origWidth,
            $this->view->pageConfig->application->loaddomainname,
            $this->view->pageConfig->application->protocol,$browser
        );


        /** If there are no RMP Assets get the product ID and get the assets from the  assets server */
        if ($assetSet === false) {
            $productID = $assetModel->getProductId($productCode);

        }

        /* pass to view the default domain name */

        $this->view->setVar("loaddomainname",$this->view->pageConfig->application->loaddomainname);
        $this->view->setVar("cacheBuster", $mode === 'draft' ? '?_=' . time() : '');
        $this->view->setVar("referer",$this->view->pageConfig->application->referer);
        $this->view->setVar("protocol",$this->view->pageConfig->application->protocol);
        $this->view->setVar("allowZoom",$allowzoom);
        $this->view->setVar("channelCSS",$channelCSS);
        $this->view->setVar("referrerURL",$hostData);
        $this->view->setVar("layoutLeft",$layoutLeft);

        /* image gallery array */

        if (isset($productID)) {
            $this->view->setVar("productID",$productID);

        } else {

//            var_dump($assetSet["galleryImageUrls"]);
//            die('asset controller');

            $this->view->setVar("assetGallerySet",$assetSet["galleryImageUrls"]);
        }

        $this->view->setVar("assetVideoSet",$assetSet["videoUrs"]);
        $this->view->arrHotspotResultSet = $assetModel -> getHotspotDetail();
        $this->view->setVar("checkAllData",$assetSet);
        $this->view->setVar("firstGo",$assetSet["360Settings"]["firstGo"]);

    }

}