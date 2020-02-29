<?php

set_time_limit(20000000000);
ini_set('memory_limit','2048M');
/**
 * User: gergely.hajcsak
 * Date: 26/01/2014
 * Time: 15:18
 */

use Phalcon\DI;
use Phalcon\Mvc\Model;

class Migrate extends Model
{

    private $_connectionRMPAsset;
    private $_connectionAsset;

    private $assetDir;
    private $product_limit_per_channel = 3 ;

    public function showDebugInfo($variable){

        echo "<pre>";
        print_r($variable);
        echo "</pre>";

    }

    public function migrateImages($assetDir,$mode = null ,$productCode = null){


        $this->assetDir = $assetDir;

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //
        //  Setting up RMP db connection
        //


        $di = DI::getDefault();
        $this->_connectionRMPAsset = new Database($di, "rmpAssetLibraryTEST");


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //
        //  Getting available channels
        //

        $channelSetQueery = "
                        select channel_id, channel_code 
                        from  `rmp-asset-library`.channels
                        where active = 1  
                        ";
        $arrParametersOrig = array();
        $channelSetArray = $this->_connectionRMPAsset->fetchAll($channelSetQueery,$arrParametersOrig);

        //$this->showDebugInfo($channelSetArray);

        $channelCodeArray   = array();
        foreach($channelSetArray as $channelSetArrayKeys => $channelSetArrayValues){

            $channelCodeArray[$channelSetArrayValues["channel_code"]] = $channelSetArrayValues["channel_id"];
        }

        //
        //
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //
        //  Setting up asset db server connections
        //

        $di = DI::getDefault();
        $this->_connectionAsset = new Database($di, "asset_server");

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //
        //  querying the products catalog by channels in a loop
        //

        $allProdustArray = array();

        foreach($channelSetArray as $channelSetArrayKey => $channelSetArrayValue){

            if ($mode=="single" &&  trim($productCode)!="" && !empty($productCode)){
                $strDbQuery = "
                SELECT DISTINCT product_id, product_code, channel
                FROM catalog.product_catalog
                WHERE
                  channel = :channel and product_code  = :product_code
                UNION ALL
                SELECT
                    product.product_id,
                    product.product as product_code,
                    parent.channel
                FROM
                  portfolio.parent
                INNER JOIN portfolio.product ON parent.pk = product.fk_parent
                  WHERE
                    parent.channel = :channel and product  = :product_code
            ";


                echo "<br>[SINGLE UPDATE]CHANNEL : ". str_replace("_","/",$channelSetArrayValue["channel_code"]);
                $arrParameters = ['channel' => str_replace("_","/",$channelSetArrayValue["channel_code"]), 'product_code' => $productCode];

            }else{

                $strDbQuery = "

                SELECT DISTINCT product_id, product_code, channel
                FROM catalog.product_catalog
                WHERE
                  channel = :channel
                UNION ALL
                SELECT
                    product.product_id,
                    product.product as product_code,
                    parent.channel
                FROM
                  portfolio.parent
                INNER JOIN portfolio.product ON parent.pk = product.fk_parent
                  WHERE
                    parent.channel = :channel
            ";

                echo "<br>CHANNEL : ". str_replace("_","/",$channelSetArrayValue["channel_code"]);

                $arrParameters = array('channel' => str_replace("_","/",$channelSetArrayValue["channel_code"]));

            }

            $assetSet = $this->_connectionRMPAsset->fetchAll($strDbQuery,$arrParameters);
            //$this->showDebugInfo($assetSet);

            foreach($assetSet as $assetSetKeys => $assetSetValues){

                // products code hasn't been added yet to the common table
                if (!isset($allProdustArray[$assetSetValues["product_code"]])){

                    $allProdustArray[$assetSetValues["product_code"]]= $assetSet[$assetSetKeys];

                }
                // its been added alredy to it. then they need to be combined
                //and add the channel code to the channel code field with comma.
                else{

                    $allProdustArray[$assetSetValues["product_code"]]["channel"]=  $allProdustArray[$assetSetValues["product_code"]]["channel"] .",". $assetSetValues["channel"];

                }

            }

        }

        $assetSet = $allProdustArray;


        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        //
        //  Going through the products
        //



        foreach($assetSet as $assetSetKeys => $assetSetValues){

            $channel_id = $assetSetValues["channel"];
            $product_code = $assetSetValues["product_code"];

            echo "<br>*************************************************************<br>
        ";
            $sourve_id = $assetSetValues["product_id"];

            $strDbOrigQuery = "
        select * from asset_library.images  li
        join asset_library.asset_library_index ali on (ali.asset_id = li.asset_id and  source_id = ? and asset_type_id = 49) 
        limit 10 ";

            $arrParametersOrig = array($sourve_id);
            $origAssetSet = $this->_connectionAsset->fetchAll($strDbOrigQuery,$arrParametersOrig);

            if (count($origAssetSet)==0){

                $strDbOrigQuery = "
          select * from asset_library.images  li
          join asset_library.asset_library_index ali on (ali.asset_id = li.asset_id and  source_id = ? and asset_type_id in (1,2,5) ) order by asset_type_id 
          limit 10 ";

                $arrParametersOrig = array($sourve_id);
                $origAssetSet = $this->_connectionAsset->fetchAll($strDbOrigQuery,$arrParametersOrig);
            }

            $assetIndex = 1;

            /******************** checking if the asset are already in the RMP library ***************/
            /******************** checking if the asset are already in the RMP library ***************/
            /******************** checking if the asset are already in the RMP library ***************/

            $identical = true;
            $origAssetArray = array();
            $origAssetIndex = 1;
            $saveoriginalImages = $origAssetSet;

            foreach($saveoriginalImages as $origAssetSetKeys => $origAssetSetValues ){
                $origAssetArray[]=array("product_code"=>$assetSetValues["product_code"],
                    "asset_index"=>(string)$origAssetIndex,
                    "size" => $origAssetSetValues["bytes"]  );
                $origAssetIndex++;
            }

            $strDbOrigQuery = "
                    select product_code, assetIndex as asset_index, orig_content_size as size
                    from  `rmp-asset-library`.assets
                    where  product_code = ? and  asset_type = 1 and size_type = 1 and quality_type = 1
                    order by assetIndex";

            $arrParametersOrig = array($assetSetValues["product_code"]);
            try{
                $RMPAssetSet = $this->_connectionRMPAsset->fetchAll($strDbOrigQuery,$arrParametersOrig);
            }catch(PDOException $e){

                // reconnect
                $di = DI::getDefault();
                $this->_connectionRMPAsset = new Database($di, "rmpAssetLibraryTEST");
                // reexecute
                $RMPAssetSet = $this->_connectionRMPAsset->fetchAll($strDbOrigQuery,$arrParametersOrig);

            }

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //
            //  Comment : Checking if RMP has assets
            //

            if($RMPAssetSet){

                echo "<font color=\"green\">". $product_code ." : (".$channel_id.") RMP library has assets. Sync not required.</font>";


            }else{

                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //
                //  Comment : Some images changed or some have been added/deleted, so needs update
                //

                if (count($RMPAssetSet)==0){
                    echo "<font color=\"blue\"> ". $product_code ." : (".$channel_id.") Insertintg new enrties </font>"; //Not the same }
                }else{
                    echo "<font color=\"red\"> ". $product_code ." : (".$channel_id.") Images have been changed in asset library - refreshing RMP lib. </font>"; //Not the same }

                }

                foreach($origAssetSet as $origAssetSetKeys => $origAssetSetValues ){


                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //
                    //  Comment : Images resize
                    //


                    $zoomable = 0;

                    $data = $origAssetSetValues["asset_blob"];

                    /***************************************/
                    $size = 900;
                    /***************************************/

                    $src = imagecreatefromstring($data);
                    $width = imagesx($src);

                    if ($width>=600) $zoomable = 1;

                    $height = imagesy($src);
                    $aspect_ratio = $height/$width;

                    // if it smaller then $size then leave it how it is.

                    if ($width>= $size || $height>=$size) {

                        if ($width>=$height){
                            echo "[normal image]";
                            $new_w = $size;
                            $new_h = round(abs($new_w * $aspect_ratio));
                        }else{

                            echo "[dodgy image]";
                            $new_h = $size;
                            $new_w = round(abs($new_h * 1/$aspect_ratio));
                        }
                    }else{

                        // if its bigger then resize it down to $size

                        echo "small image no need to resize";
                        $new_w = $width;
                        $new_h = $height;


                    }

                    // creates a new true color image resource
                    $img = imagecreatetruecolor($new_w,$new_h);
                    // resizes the source image and puts it into the new resource

                    /************************/

                    $whiteBackground = imagecolorallocate($img, 255, 255, 255);
                    imagefill($img,0,0,$whiteBackground); // fill the background with white

                    /************************/

                    echo "<br>image resize: ".$origAssetSetValues["original_filename"]." --  ".$new_w." / ".$new_h;

                    imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);

                    ob_start();
                    imagejpeg($img);
                    $imageStr = ob_get_contents();
                    ob_clean();

                    // setting up the new image names


                    $justTheNewFileName = $origAssetSetValues["original_filename"].$assetIndex."_o_".date('dmYHis').".jpg";
                    $outputServerPath = $this->assetDir.$justTheNewFileName;

                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //
                    //  Comment : Inserting images into db
                    //

                    $InsertSQL= "
                                        insert into `rmp-asset-library`.assets (
                                            original_file_name, product_code, asset_type, size_type, quality_type, content_blob,
                                            assetIndex,last_cached, url,orig_content_size,zoomable)
                                        values( ?,
                                        ?,
                                        1,
                                        1,
                                        1,
                                        ?,
                                        ?,
                                        now(),
                                        null,
                                        ?,
                                        ?);";

                    $params = array($origAssetSetValues["original_filename"],
                        $assetSetValues["product_code"],
                        $imageStr,
                        $assetIndex,
                        //$justTheNewFileName,
                        strlen($origAssetSetValues["asset_blob"]),
                        $zoomable
                    );

                    echo "size:".strlen($origAssetSetValues["asset_blob"]);

                    try{
                        $this->_connectionRMPAsset->execute($InsertSQL,$params);
                    }catch(PDOException $e){

                        // reconnect
                        $di = DI::getDefault();
                        $this->_connectionRMPAsset = new Database($di, "rmpAssetLibraryTEST");
                        // reexecute
                        $this->_connectionRMPAsset->execute($InsertSQL,$params);

                    }

                    //
                    //
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    $assetIndex++;
                }




            }

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //
            //  Inserting channel procuct entries
            //
            $arrAffectedChannels = explode(',', $channel_id);

            //$this->showDebugInfo($arrAffectedChannels);

            foreach($arrAffectedChannels as $arrAffectedChannelsKeys ){

                $InsertChannelProductsSQL= "
                                            insert IGNORE  into `rmp-asset-library`.channel_products
                                                (channel_id,product_code)
                                            values(
                                            ?,
                                            ?
                                            );";



                if ($channelCodeArray[str_replace("/","_",$arrAffectedChannelsKeys)]!=""){

                    $paramsChannelProduct = array($channelCodeArray[str_replace("/","_",$arrAffectedChannelsKeys)],
                        $product_code
                    );

                    echo "<br>assigning to channel : " . $channelCodeArray[str_replace("/","_",$arrAffectedChannelsKeys)] ." - ".$product_code;

                    try{
                        $this->_connectionRMPAsset->execute($InsertChannelProductsSQL,$paramsChannelProduct);
                    }catch(PDOException $e){
                        // reconnect
                        $di = DI::getDefault();
                        $this->_connectionRMPAsset = new Database($di, "rmpAssetLibraryTEST");
                        // reexecute
                        $this->_connectionRMPAsset->execute($InsertChannelProductsSQL,$paramsChannelProduct);
                    }

                }


            }




            //
            //
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////



        }
    }
}
