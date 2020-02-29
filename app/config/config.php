<?php

return new \Phalcon\Config([
    'database' => [
        [
            'di_name' => 'asset_server',
            'adapter' => 'Mysql',
            'host' => HOST_ASS,
            'username' => USER_ASS,
            'password' => PASSWORD_ASS,
            'port' => PORT_ASS,
            'fail_over_host' => HOST_ASS_FAILOVER,
            'fail_over_username' => USER_ASS_FAILOVER,
            'fail_over_password' => PASSWORD_ASS_FAILOVER,
            'fail_over_port' => PORT_ASS_FAILOVER,
            'dbname' => 'asset-library'
        ],
        [
            'di_name' => 'koms',
            'adapter' => 'Mysql',
            'host' => HOST_KOM,
            'username' => USER_KOM,
            'password' => PASSWORD_KOM,
            'port' => PORT_KOM
        ],

        //the rmp db is being used
        [
            'di_name' => 'localProductonDb',
            'adapter' => 'Mysql',
            'host' => HOST_PROD,
            'username' => USER_PROD,
            'password' => PASSWORD_PROD,
            'port' => PORT_PROD,
            'fail_over_host' => HOST_PROD_FAILOVER,
            'fail_over_username' => USER_PROD_FAILOVER,
            'fail_over_password' => PASSWORD_PROD_FAILOVER,
            'fail_over_port' => PORT_PROD_FAILOVER,
            'dbname' => 'rmp-asset-library'
        ],
        //the rmp db is being used
        [
            'di_name' => 'localDraft',
            'adapter' => 'Mysql',
            'host' => HOST_DRAFT,
            'username' => USER_DRAFT,
            'password' => PASSWORD_DRAFT,
            'port' => PORT_DRAFT,
            'fail_over_host' => HOST_DRAFT_FAILOVER,
            'fail_over_username' => USER_DRAFT_FAILOVER,
            'fail_over_password' => PASSWORD_DRAFT_FAILOVER,
            'fail_over_port' => PORT_DRAFT_FAILOVER,
            'dbname' => 'rmp-asset-library_draft'
        ],
    ],

    'application' => array(
        'controllersDir' => __DIR__ . '/../controllers/',
        'modelsDir' => __DIR__ . '/../models/',
        'viewsDir' => __DIR__ . '/../views/',
        'pluginsDir' => __DIR__ . '/../plugins/',
        'libraryDir' => __DIR__ . '/../library/',
        'cacheDir' => __DIR__ . '/../cache/',
//        'assetCacheDir' => __DIR__ . '/../../public/assets/',
        'assetCacheDir' => ASSET_SVR_ROOT,
        'galleryAssetCacheDir' => GALLERY_ASSET_SVR_ROOT,
        'galleryAssetCacheDirRemote' => GALLERY_ASSET_SVR_ROOT,
        'rotatorAssetDir' => ROT_ASSET_SVR_ROOT,
        'rotatorAssetDirRemote' => ROT_ASSET_SVR_ROOT,
        'loaddomainname' => LOADDOMAINNAME,
        /*'loaddomainname' => 'rmp.kondor.co.uk',  */
//        'loaddomainname' => 'rmp-gh.timico.dev',
        //'referer' => $_SERVER["HTTP_REFERER"],
        /*'loaddomainname' => 
          str_replace("ee-accessories","rich-media-player",parse_url($_SERVER["HTTP_REFERER"], PHP_URL_HOST)).
          $whatever=(!empty(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PORT)))?":".parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PORT):""*/

        'referer' => $_SERVER["HTTP_REFERER"],
        'imgs' => __DIR__ . '/../../public/img/',
        'xml360RotatorTemplate' => __DIR__ . '/../../public/xml/Expo360XML/',
        'serviceDir' => __DIR__ . '/../service/',
        'baseUri' => '/',
        'protocol' =>   !empty(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_SCHEME)) ? parse_url($_SERVER["HTTP_REFERER"], PHP_URL_SCHEME)."://" :"http://",
    )
]);