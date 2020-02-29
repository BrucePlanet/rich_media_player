<?php
/**
 *
 * Validates asset server image cache files and removes invalid ones
 *
 * @Author: Daniel Schofield
 * @Created: 2015/11/10
 *
 */

ini_set('memory_limit', '512M');

$log_file = '/tmp/image_cache.txt';

if (file_exists($log_file)) {
    unlink($log_file);
}

// get file count
$fi = new FilesystemIterator(__DIR__.'/../../public/assets', FilesystemIterator::SKIP_DOTS);
$intTotalFiles = iterator_count($fi);

$fi = new FilesystemIterator(__DIR__.'/../../public/assets', FilesystemIterator::SKIP_DOTS);

$dteToday = date('Y-m-d');
$dteStart = date('Ymd', strtotime($dteToday . '- 1 month'));

$intOldFileCount = 0;
$intCorruptFileCount = 0;
$tStart = microtime(true);

for($i = 0; $i < $intTotalFiles; $i++) {

    $strFileName = $fi->getPathname();

    // get date file was modified
    $dteModified = date('Ymd', filemtime($strFileName));

    if ($dteModified < $dteStart) {
        // unlink file
        unlink($strFileName);
        $intOldFileCount++;
        file_put_contents($log_file, 'Deleting old file ' . $strFileName . "\r\n", FILE_APPEND);
    } else {

        $ext = pathinfo($strFileName, PATHINFO_EXTENSION);

        $strFunction = 'imagecreatefrom';
        if ($ext =='jpg') {
            $strFunction .= 'jpeg';
        } elseif ($ext = 'png') {
            $strFunction .= 'png';
        } else {
            // not an image or not handled extension
        }

        $bResult = @$strFunction($strFileName);

        if ( $bResult === false ) {
            // unlink file
            unlink($strFileName);
            $intCorruptFileCount++;
            file_put_contents($log_file, 'Found an issue with ' . $strFileName . "\r\n", FILE_APPEND);
        }

    }

    file_put_contents($log_file, 'Processing ' . ($i+1) . ' out of ' . $intTotalFiles . "\r\n", FILE_APPEND);

    $fi->next();
}

if (($intCorruptFileCount+$intOldFileCount) > 0) {

    $arrInfo['Total Images Before Prune'] = $intTotalFiles;
    $arrInfo['Corrupt Images Removed'] = $intCorruptFileCount;
    $arrInfo['Old Images Removed'] = $intOldFileCount;
    $arrInfo['Time Taken'] = microtime(true) - $tStart . ' s';
    $arrInfo['Memory Used'] = (memory_get_usage(true) / 1024 / 1024) . ' MB';
    var_dump($arrInfo);
}