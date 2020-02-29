/**
 * Created by bruce.tomalin on 24/12/2014.
 */

function downloadAssets(assetIDs,elementID) {

    var html = "<div style=\"width: 140px; line-height: 27px; text-align: center; font-size: 10px;\"><img src=\"/images/ajax-loader.gif\" style=\"float: left;\" /> Preparing Download</div>";
    $("#"+elementID).html(html);

    $path = "/ajax/index.php?m=productdetails&f=download&assetIDs="+assetIDs;

    //alert($path);

    $("#"+elementID).load($path);

}

function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

function getCompatibility(pid,manufacturer) {
    //var html = "<a href=\"#\" onClick=\"disablePopUp();\" id=\"popUpClose\">x</a><div style=\"width: 400px%; padding-top: 100px; text-align: center; font-size: 12px;\"><img src=\"/images/ajax-loader.gif\" /><br />Preparing Compatibility Information</div>";
    //$("#popUpContent").html(html);

    $path = "/ajax/index.php?m=productdetails&f=compatibility&pid="+pid+"&manufacturer="+manufacturer;
    $.colorbox({href:$path,maxHeight:500,maxWidth:800});
}