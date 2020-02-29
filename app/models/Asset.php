<?php
/**
 * User: gergely.hajcsak
 * Date: 26/01/2014
 * Time: 15:18
 */

use Phalcon\Mvc\Model;

class Asset extends BaseModel
{
    /**
     * @var integer
     */
    public $category;
    private $_connectionD;

    /*
     *  Asset properties
     */

    public $productCode;
    private $assetSet;
    private $assetSetOriginal;
    private $channelCode;

    public $imageWith;
    public $imageHeight;
    private $arrayPublicUrl;
    private $assetDir;
    private $galleryAssetDir;
    private $galleryAssetDirRemote;
    private $rotatorAssetDir;
    private $rotatorAssetDirRemote;
    private $xml360RotatorTemplate;
    public $requestId;
    private $qualityPercantage;
    public $origWidth;
    public $cachedAssetFoundFolder;
    public $arrOriginalURL;

    /******************************/

    public $allowAnyProductSQL = "";

    /******************************/

    public $sizeTypeId;
    public $quailityTypeId;

    /******************************/

    public $loaddomainname;
    public $protocol;
    public $browser;
    public $allowzoom;
    public $_db = "localProductonDb";

    /****************************/


    public function initDb($mode)
    {
        if ($mode == "production") $this->_db = "localProductonDb";
        else  $this->_db = "localDraft";
    }

    private $returnAssetArray = array("360Settings" => array(
        "initAction" => null,
        "details" => null,
        "frames" => null,
        "firstGo" => 0
    ),
        "galleryImageUrls" => array(
            "small" => null,
            "zoom" => null,
            "zoomable" => null,
        ),
        "videoUrs" => array(
            "video" => null,
            "poster" => null,
        )
    );

    public function getProductAssets($customCSS, $channelCode, $productCode, $imageWith, $assetDir, $galleryAssetDir, $galleryAssetDirRemote, $rotatorAssetDir, $rotatorAssetDirRemote, $xml360RotatorTemplate,
                                     $requestId, $sizeTypeId, $quailityTypeId, $origWidth, $loaddomainname, $protocol, $browser, $secondRun = false)
    {


        // Do some housekeeping on old cache files.
        // This will delete any file older than one month.
        $path = $galleryAssetDir;
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ((time()-filectime($path.$file)) >= 2592000) {
                    if (preg_match('/\.jpg$/i', $file)) {
                        unlink($path.$file);
                    }
                }
            }
        }


        ini_set('memory_limit', '1024M');
        $di = \Phalcon\DI::getDefault();

        $this->_connectionD = new Database($di, $this->_db);

        $this->channelCode = $channelCode;

        /*   Allow not to restrict the queries to channels
            if the channel; code is the special "__ANY__"*/

        if ($channelCode == "__ANY__") {
            $this->allowAnyProductSQL = "LEFT ";
        }

        $this->productCode = $productCode;
        $this->imageWith = $imageWith;
        $this->assetDir = $assetDir;
        $this->galleryAssetDir = $galleryAssetDir;
        $this->galleryAssetDirRemote = $galleryAssetDirRemote;
        $this->rotatorAssetDir = $rotatorAssetDir;
        $this->rotatorAssetDirRemote = $rotatorAssetDirRemote;
        $this->xml360RotatorTemplate = $xml360RotatorTemplate;
        $this->requestId = $requestId;
        $this->sizeTypeId = $sizeTypeId;
        $this->origWidth = $origWidth;
        $this->loaddomainname = $loaddomainname;
        $this->protocol = $protocol;
        $this->browser = $browser;
        $this->allowzoom = true;

        if ($this->browser == "IE") $this->allowzoom = false;
        else $this->allowzoom = true;

        /***************** get image resizing setting **************/

        if ($quailityTypeId == 2) {
            $this->qualityPercantage = 40;
            $this->quailityTypeId = $quailityTypeId;
        }

        if ($quailityTypeId == 3) {
            $this->qualityPercantage = 50;
            $this->quailityTypeId = $quailityTypeId;
        }

        if ($quailityTypeId == 4) {
            $this->qualityPercantage = 60;
            $this->quailityTypeId = $quailityTypeId;
        }

        if ($quailityTypeId == 5) {
            $this->qualityPercantage = 70;
            $this->quailityTypeId = $quailityTypeId;
        }

        if ($secondRun != true) {

            /************* ASSET TYPES *************/

            define("ASSET_TYPE_360", 3);
            define("ASSET_TYPE_GALLERY_IMAGE", 1);
            define("ASSET_TYPE_VIDEO", 2);
            define("ASSET_TYPE_VIDEO_POSTER", 21);
            //define("ASSET_TYPE_VIDEO_HOTSPOS", "3");

            /************* ASSET TYPES *************/

        }

        /**********************************************************************************************/
        /*********************************** RMP images preparation ***********************************/
        /**********************************************************************************************/

        /** Are there any 360 Rotator Assets */
        $this->assetSet = $this->getOriginal360Assets($this->channelCode, $this->productCode, ASSET_TYPE_360, 1, 1);


//        if (count($this->assetSet) != 0) {

        /***************** Get 360 image resizing setting **************/
        //** Check the DB for any cached asset entries  **//

//            $assetSet = $this->checkDBCachedAssets($this->channelCode, $this->productCode, ASSET_TYPE_360, $this->sizeTypeId, $this->quailityTypeId);

//            if (count($assetSet) > 0) {
                /****  If there are cached 360 images listed then use them ***/
//                $this->assetSet = $assetSet;

//            }

        /* Go get the 360 rotator assets */
//            $this->resamplingFor360Rotator();

//        }

        /********************************************************************************************************/
        /*********************************** Images gallery images preparation ***********************************/
        /*********************************************************************************************************/
        /* Request converted cached Gallery images recorded in the DB */
        /* This no longer does that it only checks the database for all assets it does not check for cached assets */
        $this->assetSetOriginal = $this->checkDBCachedAssets($channelCode, $productCode, ASSET_TYPE_GALLERY_IMAGE, 1, 1);

        if (count($this->assetSetOriginal) === 0) {
            return false;
        }

        $strImageNumber = count($this->assetSetOriginal);

        $arrCachedImages = $this->checkCacheFolderImages($this->assetSetOriginal);

        if ($arrCachedImages != $strImageNumber) {

            /** There are no recorded Cached Gallery images in the DB **/
            /** Use the original Gallery images to create new cached assets **/
            foreach ($this->assetSetOriginal as $arrayResultSetOriginalkeys => $arrayResultSetOriginalvalues) {
                $arrayResultSetOriginal[$arrayResultSetOriginalkeys]["url"] = "";
            }

            /** There are no cached Gallery assets create new cached assets **/
            $this->resamplingforGalleryOriginal($this->assetSetOriginal);

        }

        $arrCachedZoomImages = $this->checkCacheFolderZoom($this->assetSetOriginal);

        if ($arrCachedZoomImages != $strImageNumber) {

            /** There are no recorded Cached Gallery images in the DB **/
            /** Use the original Gallery images to create new cached assets **/
            foreach ($this->assetSetOriginal as $arrayResultSetOriginalkeys => $arrayResultSetOriginalvalues) {
                $arrayResultSetOriginal[$arrayResultSetOriginalkeys]["url"] = "";
            }

            /** There are no cached Gallery assets create new cached assets **/
            $this->resamplingforGalleryOriginal($this->assetSetOriginal);

        }

        /********************************************************************************************************/
        /******************************************** Video *****************************************************/
        /********************************************************************************************************/

        $strDbQuery = "SELECT  
                          assets.id,
                          assets.original_file_name,
                          assets.product_code,
                          assets.asset_type,
                          assets.size_type,
                          assets.quality_type,
                          assets.content_blob,
                          assets.assetIndex,
                          assets.last_cached,
                          assets.url,
                          assets.zoomable
                       FROM
                          assets
                          " . $this->allowAnyProductSQL . "JOIN channel_products chp on chp.product_code = assets.product_code 
                          " . $this->allowAnyProductSQL . "JOIN channels ch on ch.channel_id = chp.channel_id and  ch.channel_code = ?
                          " . $this->allowAnyProductSQL . "JOIN channel_settings chs on ch.channel_id = chs.channel_id and chs.tab_id= assets.asset_type  
                       WHERE
                          assets.product_code    = ?
                          and assets.asset_type      in (?,?)
                          and assets.size_type       = ?
                          and assets.quality_type    = ?
                       GROUP BY assets.id                        
                       ORDER BY assets.assetIndex; ";

        $arrParameters = array($this->channelCode, $this->productCode, ASSET_TYPE_VIDEO, ASSET_TYPE_VIDEO_POSTER, 1, $quailityTypeId);
        $arrvideoResultSet = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

        foreach ($arrvideoResultSet as $arrvideoResultSetKeys => $arrvideoResultSetValues) {

            if ($arrvideoResultSetValues["asset_type"] == 2) // This is a video ULR
                $this->returnAssetArray["videoUrs"]["video"][] = $arrvideoResultSetValues["url"];

            if ($arrvideoResultSetValues["asset_type"] == 21) // This the video poster URL
                $this->returnAssetArray["videoUrs"]["poster"][] = $arrvideoResultSetValues["url"];

        }

        return $this->returnAssetArray;
    }


    private function resamplingforGalleryOriginal($arrImages)
    {

        /* At this point we should know that there are original images in the database */
        /* We should also know that there are no cached images recorded in the database */
        /* OR That there are missing images from the cache folder */

        $di = \Phalcon\DI::getDefault();
        $this->_connectionD = new Database($di, $this->_db);
        $doWeNeedToCache = false;
        $cacheInsertSQL = null;
        $arrayAssetZoomableImages = null;

        foreach ($arrImages as $assetsKey => $assetsValues) {

            $data = $assetsValues["content_blob"];

            /***************************************************************************
             * Resampling to a preset size
             ***************************************************************************/

            $src = imagecreatefromstring($data);

            if ($this->allowzoom) $arrayAssetZoomableImages[] = $assetsValues["zoomable"];
            else $arrayAssetZoomableImages[] = 0;

            // setting up the new image names
            $asssetId = $assetsValues["id"];

            $justTheNewFileName = $assetsValues["product_code"] . "_" . $assetsValues["id"] . $assetsValues["assetIndex"] . "_o.jpg";

            $outputServerPath = $this->galleryAssetDir . $justTheNewFileName;

            $outputServerPathRemote = $this->galleryAssetDirRemote . $justTheNewFileName;

            // physically saving the new images on the cache area
            imagejpeg($src, $outputServerPath, $this->qualityPercantage);

            // physically saving the new images on the cache area
            imagejpeg($src, $outputServerPathRemote, $this->qualityPercantage);

            // setting up the public url of the new images
            $asssetURL = $justTheNewFileName;
            $arrayAssetZoomImages[] = $asssetURL;

            $arrayAssetURL[] = $asssetURL;

        }

        /* saving the new cached files into the database */
        $this->returnAssetArray["galleryImageUrls"]["zoom"] = $arrayAssetZoomImages;
        $this->returnAssetArray["galleryImageUrls"]["zoomable"] = $arrayAssetZoomableImages;
        $this->createZoomImages($arrImages);

    }


    /**
     * check if assets exist server folder, and re-create if
     * at least one missing
     */
//    private function resamplingforGallery()
//    {
//
//        $di = \Phalcon\DI::getDefault();
//        $this->_connectionD = new Database($di, $this->_db);
//        $cacheInsertSQL = NULL;
//        $cacheUpdateSQL = NULL;
//
//        $images = $this->doesTheFileExist($this->assetSet);
//
//        if (is_array($images)) {
//            $this->returnAssetArray["galleryImageUrls"]["small"] = $images;
//            $this->setZoomImages($this->assetSetOriginal);
//        } else {
//            $this->resamplingforGalleryOriginal($this->assetSetOriginal);
//        }
//
//        $images = $this->doesTheFileExist($this->assetSetOriginal);
//
//        if ($images === false) {
//            $this->resamplingforGalleryOriginal($this->assetSetOriginal);
//        }
//
//    }
//
//    private function setZoomImages($arrImages)
//    {
//        foreach ($arrImages as $assetsKey => $assetsValues) {
//
//            if ($this->allowzoom) {
//                $this->returnAssetArray["galleryImageUrls"]["zoomable"][] = $assetsValues["zoomable"];
//            } else {
//                $this->returnAssetArray["galleryImageUrls"]["zoomable"] = 0;
//            }
//
//            $this->returnAssetArray["galleryImageUrls"]["zoom"][] = $assetsValues["product_code"] . "_" . $assetsValues["id"] . $assetsValues["assetIndex"] . "_o.jpg";;
//        }
//    }

    private function checkCacheFolderImages($arrImages)
    {

        /* At this point we should know that there are original images in the database */
        /* We need to check for cached images in the folder */

        $di = \Phalcon\DI::getDefault();
        $this->_connectionD = new Database($di, $this->_db);
        $doWeNeedToCache = false;
        $cacheInsertSQL = null;
        $arrayAssetZoomableImages = null;

        $strFoundMkr = null;

        foreach ($arrImages as $assetsKey => $assetsValues) {

            if ($this->allowzoom) $arrayAssetZoomableImages[] = $assetsValues["zoomable"];
            else $arrayAssetZoomableImages[] = 0;

            $justTheNewFileName = $assetsValues["product_code"] . "_" . $assetsValues["id"] . $assetsValues["assetIndex"] . "_o.jpg";

            $outputServerPath = $this->galleryAssetDir . $justTheNewFileName;

            $strOneMonth = date('d/m/Y',strtotime('-30 days',strtotime(date("Y-m-d H:i:s")))) . PHP_EOL;

            // Check the cache folder to see if the file exists
            if (file_exists($outputServerPath) && filectime($outputServerPath) >= $strOneMonth) {

                // setting up the public url of the new images
                $asssetURL = $justTheNewFileName;
                $arrayAssetZoomImages[] = $asssetURL;

                $arrayAssetURL[] = $asssetURL;

                /* saving the new cached files into the database */
                $this->returnAssetArray["galleryImageUrls"]["zoom"] = $arrayAssetZoomImages;
                $this->returnAssetArray["galleryImageUrls"]["zoomable"] = $arrayAssetZoomableImages;

                $strFoundMkr ++;

            }

        }

        return $strFoundMkr;

    }


    private function checkCacheFolderZoom($arrImages)
    {

        $cacheInsertSQL = null;
        $strZFoundMkr = null;

        foreach ($arrImages as $assetsKey => $assetsValues) {

            $justTheNewFileName = $assetsValues["product_code"] . "_" . $assetsValues["id"] . "_" . $assetsValues["assetIndex"] . "_s.jpg";

            $outputServerPath = $this->galleryAssetDir . $justTheNewFileName;

            // Check the cache folder to see if the file exists
            if (file_exists($outputServerPath)) {

                // setting up the public url of the new images
                $asssetURL = $justTheNewFileName;
                $arrayAssetURL[] = $asssetURL;
                $this->returnAssetArray["galleryImageUrls"]["small"] = $arrayAssetURL;

                $strZFoundMkr++;
            }
        }

        return $strZFoundMkr;

    }


    private function createZoomImages($arrImages)
    {

        $cacheInsertSQL = null;

        foreach ($arrImages as $assetsKey => $assetsValues) {

            $data = $assetsValues["content_blob"];
            $size = $this->imageWith;
            $src = imagecreatefromstring($data);
            $width = imagesx($src);
            $height = imagesy($src);
            $aspect_ratio = $height / $width;

            if ($width >= $size || $height >= $size) {
                if ($width >= $height) {
                    $new_w = $size;
                    $new_h = round(abs($new_w * $aspect_ratio));
                } else {
                    $new_h = $size;
                    $new_w = round(abs($new_h * 1 / $aspect_ratio));
                }
            } else {
                $new_w = $width;
                $new_h = $height;
            }

            $this->imageHeight = $new_h;

            // creates a new true color image resource
            $img = imagecreatetruecolor($new_w, $new_h);

            // resizes the source image and puts it into the new resource
            imagecopyresampled($img, $src, 0, 0, 0, 0, $new_w, $new_h, $width, $height);

            // setting up the new image names
            $asssetId = $assetsValues["id"];

            $justTheNewFileName = $assetsValues["product_code"] . "_" . $assetsValues["id"] . "_" . $assetsValues["assetIndex"] . "_s.jpg";

            $outputServerPath = $this->galleryAssetDir . $justTheNewFileName;

            // physically saving the new images in the cache folder
            imagejpeg($img, $outputServerPath, $this->qualityPercantage);

            // setting up the public url of the new images
            $asssetURL = $justTheNewFileName;
            $arrayAssetURL[] = $asssetURL;

        }

        $this->returnAssetArray["galleryImageUrls"]["small"] = $arrayAssetURL;

    }

    private function checkDBCachedAssets($channelCode, $productCode, $assetType, $sizeTypeId, $quailityTypeId, $intAssetId = null)
    {

        $strDbQuery = "SELECT
                           assets.id,
                           assets.original_file_name,
                           assets.product_code,
                           assets.asset_type,
                           assets.size_type,
                           assets.quality_type,
                           assets.content_blob,
                           assets.assetIndex,
                           assets.last_cached,
                           assets.url,
                           assets.zoomable
                       FROM assets
                           " . $this->allowAnyProductSQL . "JOIN channel_products chp on chp.product_code = assets.product_code
                           " . $this->allowAnyProductSQL . "JOIN channels ch on ch.channel_id = chp.channel_id and  ch.channel_code = ?
                           " . $this->allowAnyProductSQL . "JOIN channel_settings chs on ch.channel_id = chs.channel_id and chs.tab_id= assets.asset_type  
                       WHERE
                           assets.product_code=?
                           and     assets.asset_type = ?
                           and     assets.size_type=?
                           and     assets.quality_type=? ";

        if ($intAssetId === null) {
            $arrParameters = array($channelCode, $productCode, $assetType, $sizeTypeId, $quailityTypeId);
        } else {

            $strDbQuery .= ' and assets.original_file_name = ? ';

            $arrParameters = array($channelCode, $productCode, $assetType, $sizeTypeId, $quailityTypeId, $intAssetId);
        }

        $strDbQuery .= ' GROUP BY assets.id        
                       ORDER BY assets.assetIndex; ';

        $assetSet = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

        return $assetSet;


    }

    private function getOriginal360Assets($channelCode, $productCode, $assetType, $sizeTypeId, $quailityTypeId)
    {

        $strDbQuery = "SELECT
                          assets.id,
                          assets.original_file_name,
                          assets.product_code,
                          assets.asset_type,
                          assets.size_type,
                          assets.quality_type,
                          assets.content_blob,
                          assets.assetIndex,
                          assets.zoomable
                       FROM assets
                           " . $this->allowAnyProductSQL . "JOIN channel_products chp on chp.product_code = assets.product_code
                           " . $this->allowAnyProductSQL . "JOIN channels ch on ch.channel_id = chp.channel_id and  ch.channel_code = ?
                           " . $this->allowAnyProductSQL . "JOIN channel_settings chs on ch.channel_id = chs.channel_id and chs.tab_id= assets.asset_type  
                       WHERE
                           assets.product_code=?
                           and     assets.asset_type = ?
                           and     assets.size_type=?
                           and     assets.quality_type=?
                       GROUP BY assets.id        
                       ORDER BY assets.assetIndex;";

        $arrParameters = array($channelCode, $productCode, $assetType, $sizeTypeId, $quailityTypeId);
        $assetSet = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

        return $assetSet;

    }

    public function getOriginalGalleryAssets()
    {

        // gets the original assets
        $strDbQuery = "SELECT
                          assets.id,
                          assets.original_file_name,
                          assets.product_code,
                          assets.asset_type,
                          assets.size_type,
                          assets.quality_type,
                          assets.content_blob,
                          assets.assetIndex,
                          assets.last_cached,
                          assets.url,
                          assets.zoomable
                       FROM assets
                           " . $this->allowAnyProductSQL . "JOIN channel_products chp on chp.product_code = assets.product_code
                           " . $this->allowAnyProductSQL . "JOIN channels ch on ch.channel_id = chp.channel_id and  ch.channel_code = ?
                           " . $this->allowAnyProductSQL . "JOIN channel_settings chs on ch.channel_id = chs.channel_id and chs.tab_id= assets.asset_type
                       WHERE
                           assets.product_code=?
                           and      assets.asset_type = ?
                           and      assets.size_type=1
                           and      assets.quality_type=1
                       GROUP BY assets.id
                       ORDER BY assets.assetIndex;";

        $arrParameters = array($this->channelCode, $this->productCode, ASSET_TYPE_360);
        $this->assetSet = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

    }



    public function create360Image($assetsValues)
    {

        $doWeNeedToCache = true;

        $data = $assetsValues["content_blob"];

        /***************************************************************************
         * Resampling to a preset size
         ***************************************************************************/

        // setting up the new width
        $size = $this->imageWith;

        $src = imagecreatefromstring($data);
        $width = imagesx($src);
        $height = imagesy($src);
        $aspect_ratio = $height / $width;

        if ($width <= $size) {
            $new_w = $width;
            $new_h = $height;
        } else {
            $new_w = $size;
            $new_h = abs($new_w * $aspect_ratio);
        }

        $this->imageHeight = $new_h;

        // creates a new true color image resource
        $img = imagecreatetruecolor($new_w, $new_h);

        // resizes the source image and puts it into the new resource
        imagecopyresampled($img, $src, 0, 0, 0, 0, $new_w, $new_h, $width, $height);

        // setting up the new image names
        $asssetId = $assetsValues["id"];

        $justTheNewFileName = $assetsValues["product_code"] . "_" . $assetsValues["id"] . "_" . $assetsValues["assetIndex"] . ".jpg";

        $outputServerPath = $this->rotatorAssetDir . $justTheNewFileName;

        $outputServerPathRemote = $this->rotatorAssetDirRemote . $justTheNewFileName;

        // physically saving the new images on the cache area
        imagejpeg($img, $outputServerPath, $this->qualityPercantage);

        // physically saving the new images on the cache area
        imagejpeg($img, $outputServerPathRemote, $this->qualityPercantage);

        // setting up the public url of the new images
        $asssetURL = $justTheNewFileName;

        $cacheInsertSQL = "insert into assets (
                                                original_file_name, 
                                                product_code, 
                                                asset_type, 
                                                size_type, 
                                                quality_type, 
                                                content_blob, 
                                                assetIndex,
                                                last_cached, 
                                                url)
                                    values( '" . $assetsValues["original_file_name"] . "',
                                    '" . $assetsValues["product_code"] . "',
                                    " . $assetsValues["asset_type"] . ",
                                    " . $this->sizeTypeId . ",
                                    " . $this->quailityTypeId . ",
                                    null,
                                    '" . $assetsValues["assetIndex"] . "',
                                    timestamp(now()),
                                    '" . $justTheNewFileName . "')
                                    ON DUPLICATE KEY update last_cached = timestamp(now()), url = '" . $justTheNewFileName . "';";

        $cacheUpdateSQL = "UPDATE  assets
                                    SET url = '" . $justTheNewFileName . "', last_cached = timestamp(now())
                                    WHERE
                                        product_code = '" . $assetsValues["product_code"] . "' 
                                    and asset_type = " . $assetsValues["asset_type"] . "
                                    and size_type = 1
                                    and assetIndex = " . $assetsValues["assetIndex"] . "
                                    and quality_type =1;";

        return [
            'asset_url' => $justTheNewFileName,
            'insert_sql' => $cacheInsertSQL,
            'update_sql' => $cacheUpdateSQL];

    }


    public function getHotspotDetail()
    {

        $strDbQuery = "SELECT id, product_code, assetIndexFrom, assetIndexTo, button, x, y, type, action,
                              width, bg_color, bg_alpha, round_corners, fadeTime, html
                       FROM hotspot_details
                       WHERE
                            product_code = ? and button = 'detail'
                       ORDER BY assetIndexFrom, assetIndexTo;";

        $arrParameters = array($this->productCode);
        $arrhotspotResultSet = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

        return $arrhotspotResultSet;

    }

    public function confirmReferef($channelCode, $referer)
    {
        $di = \Phalcon\DI::getDefault();

        if ($channelCode == "__ANY__") return true;

        $this->_connectionD = new Database($di, $this->_db);
        $strDbQuery = "SELECT
                          channel_code,
                          active,
                          accepted_referer
                       FROM  channels
                       WHERE channel_code = ?
                            limit 1";

        $arrParameters = array($channelCode);
        $arrhotspotResultSet = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

        // only one record can be found otherwise it returns false
        if (count($arrhotspotResultSet) == 1) {

            $arrhotspotResultSet = $arrhotspotResultSet[0];

            // default return value
            // we return with true - as request is accpented - if we find a matching stgored referel
            $isallowed = false;
            $message = "no matching HTTP referel found";

            //converting json to php array
            $allowedReferels = json_decode($arrhotspotResultSet["accepted_referer"]);

            //going throgh the array
            if (is_array($allowedReferels)) {

                foreach ($allowedReferels as $allowedReferelsKeys => $allowedReferelsValues) {

                    if ($allowedReferelsValues == "") {

                        $message = "allow any";
                        $isallowed = true;
                        break;
                    }

                    // checks if any accetped referred can be found in the HTTP referal
                    if (strstr($referer, $allowedReferelsValues) != false) {

                        //if its found then set the return value true and finish function
                        $message = "matching HTTP referel found";
                        $isallowed = true;
                        break;
                    }
                }
            } else {

                // the accepted referal field value is not an array, rejecting
                $message = "no matching HTTP referel found";
                $isallowed = false;

            }

        } // not existing channel or not active channel, rejecting

        else {

            $message = "not existing channel or not active channel";
            $isallowed = false;

        }

        return $isallowed;
    }

    private function getHotspots()
    {

        $strDbQuery = "SELECT id, product_code, assetIndexFrom, assetIndexTo, button, x, y, type, 
                              action, width, bg_color, bg_alpha, round_corners, fadeTime, html
                       FROM hotspot_details
                       WHERE
                            product_code = ?
                       ORDER BY assetIndexFrom, assetIndexTo;";

        $arrParameters = array($this->productCode);
        $arrvideoResultSet = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

        return $arrvideoResultSet;

    }


//    private function generate360RotatorXML()
//    {
//
//        //gets the pure template
//        $xmlFileContect = file_get_contents($this->xml360RotatorTemplate . "360RotatorTemplate.xml");
//
//        //generates file list
//
//        if (count($this->arrayPublicUrl) > 0)
//            $this->returnAssetArray["360Settings"]["frames"] = count($this->arrayPublicUrl);
//
//
//        $hotspotSettins = $this->getHotspots();
//        $frameHotSpotSettins = array();
//        foreach ($hotspotSettins as $hotspotSettinsKeys => $hotspotSettinsValues) {
//
//            $this->returnAssetArray["360Settings"]["details"] = 1;
//
//            if ($hotspotSettinsValues["type"] == "link" || $hotspotSettinsValues["type"] == "small" || $hotspotSettinsValues["type"] == "image") {
//
//                //echo $hotspotSettinsValues["assetIndexFrom"]."/".$hotspotSettinsValues["assetIndexTo"]."<br>";
//
//                for ($i = $hotspotSettinsValues["assetIndexFrom"]; $i <= $hotspotSettinsValues["assetIndexTo"]; $i++) {
//
//                    $hotspotSettinsValues["html"] = str_replace("[loaddomainname]", $this->loaddomainname, $hotspotSettinsValues["html"]);
//                    $hotspotSettinsValues["html"] = str_replace("[protocol]", $this->protocol, $hotspotSettinsValues["html"]);
//
//                    $frameHotSpotSettins[$i][$hotspotSettinsValues["type"]][] = array("button" => $hotspotSettinsValues["button"],
//                        "x" => $hotspotSettinsValues["x"],
//                        "y" => $hotspotSettinsValues["y"],
//                        "action" => $hotspotSettinsValues["action"],
//                        "width" => $hotspotSettinsValues["width"],
//                        "bg_color" => $hotspotSettinsValues["bg_color"],
//                        "bg_alpha" => $hotspotSettinsValues["bg_alpha"],
//                        "round_corners" => $hotspotSettinsValues["round_corners"],
//                        "fadeTime" => $hotspotSettinsValues["fadeTime"],
//                        "html" => $hotspotSettinsValues["html"],
//                        "action" => $hotspotSettinsValues["action"]
//                    );
//                }
//            }
//
//            if ($hotspotSettinsValues["type"] == "firstGo") {
//
//                /*echo $hotspotSettinsValues["action"];
//                 die();*/
//
//                $this->returnAssetArray["360Settings"]["firstGo"] = $hotspotSettinsValues["action"];
//
//            }
//
//        }
//
//        $fileFile = "";
//        foreach ($this->arrayPublicUrl as $urlValuesKes => $urlValues) {
//
//
//            if (isset($frameHotSpotSettins[$urlValuesKes])) {
//
//                /* there is hotspot to diplay */
//
//                $fileFile .= '
//                <image src = "' . $urlValues . '">';
//
//                if (isset($frameHotSpotSettins[$urlValuesKes]["small"])) {
//
//                    foreach ($frameHotSpotSettins[$urlValuesKes]["small"] as $frameHotSpotSettinsKeys => $frameHotSpotSettinsValues) {
//                        $fileFile .= '<hotspot>
//                                      <button_id>' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["button"] . '</button_id>
//                                      <x>' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["x"] . '</x>
//                                      <y>' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["y"] . '</y>
//                                      <type>small</type>
//                                      <content>
//                                      <width>' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["width"] . '</width>
//                                      <background_color>' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["bg_color"] . '</background_color>
//                                      <background_alpha>0.8</background_alpha>
//                                      <background_pattern>0.7</background_pattern>
//                                      <opacity>0.6</opacity>
//                                      <padding>5</padding>
//                                      <round_corners>' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["round_corners"] . '</round_corners>
//                                      <fadeTime>' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["fadeTime"] . '</fadeTime>
//                                      <html><![CDATA[' . $frameHotSpotSettins[$urlValuesKes]["small"][$frameHotSpotSettinsKeys]["html"] . ']]></html>
//                                      </content>
//                                      </hotspot>';
//                    }
//                }
//
//                if (isset($frameHotSpotSettins[$urlValuesKes]["link"])) {
//
//
//                    foreach ($frameHotSpotSettins[$urlValuesKes]["link"] as $frameHotSpotSettinsKeys => $frameHotSpotSettinsValues) {
//
//                        $fileFile .= '<hotspot>
//                                      <button_id>' . $frameHotSpotSettins[$urlValuesKes]["link"][$frameHotSpotSettinsKeys]["button"] . '</button_id>
//                                      <x>' . $frameHotSpotSettins[$urlValuesKes]["link"][$frameHotSpotSettinsKeys]["x"] . '</x>
//                                      <y>' . $frameHotSpotSettins[$urlValuesKes]["link"][$frameHotSpotSettinsKeys]["y"] . '</y>
//                                      <type>link</type>
//                                      <content>' . $frameHotSpotSettins[$urlValuesKes]["link"][$frameHotSpotSettinsKeys]["action"] . '</content>
//                                      </hotspot>';
//                    }
//                }
//
//                if (isset($frameHotSpotSettins[$urlValuesKes]["image"])) {
//
//                    foreach ($frameHotSpotSettins[$urlValuesKes]["image"] as $frameHotSpotSettinsKeys => $frameHotSpotSettinsValues) {
//
//                        $fileFile .= '<hotspot>
//                                      <button_id>' . $frameHotSpotSettins[$urlValuesKes]["image"][$frameHotSpotSettinsKeys]["button"] . '</button_id>
//                                      <x>' . $frameHotSpotSettins[$urlValuesKes]["image"][$frameHotSpotSettinsKeys]["x"] . '</x>
//                                      <y>' . $frameHotSpotSettins[$urlValuesKes]["image"][$frameHotSpotSettinsKeys]["y"] . '</y>
//                                      <type>image</type>
//                                      </hotspot>';
//                    }
//
//                }
//
//                $fileFile .= '</image>';
//            } else {
//                $fileFile .= '<image src = "' . $urlValues . '"/>';
//            }
//
//            $i++;
//        }
//
//        //puts the new filelist into a new XML file
//        $xmlFileContect = str_replace("[serverName]", $this->loaddomainname, $xmlFileContect);
//        $xmlFileContect = str_replace("[serverProtocol]", $this->protocol, $xmlFileContect);
//        $xmlFileContect = str_replace("[serverDir]", ROTATOR_ROOT_ALIAS, $xmlFileContect);
//        $xmlFileContect = str_replace("[filesUrls]", $fileFile, $xmlFileContect);
//        $xmlFileContect = str_replace("[imageWidth]", $this->imageWith, $xmlFileContect);
//        $xmlFileContect = str_replace("[imagegHeight]", $this->imageHeight, $xmlFileContect);
//
//        file_put_contents($this->assetDir . "xml/360RotatorRequest" . $this->requestId . ".xml", $xmlFileContect);
//
//    }

    public function getProductId()
    {

        $strDbQuery = "SELECT distinct(pc.product_code),
                                       pc.product_id, 
                                       ali.source_id, 
                                       ali.asset_id 
                       FROM catalog.product_catalog pc
                       " . $this->allowAnyProductSQL . "join asset_library.asset_library_index ali on pc.product_id = ali.source_id 
                       WHERE
                            product_code = ?
                       ORDER BY product_id;";

        $arrParameters = array($this->productCode);
        $arrProductID = $this->_connectionD->fetchAll($strDbQuery, $arrParameters);

        return $arrProductID;

    }

}
