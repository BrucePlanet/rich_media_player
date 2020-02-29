///************************************************************************************************/
//  console.log("******************************************************************************************");
//  console.log("******************************************************************************************");
///*
//--------------------------------------------------------------------------------------------------------------
//Injections statauses
//--------------------------------------------------------------------------------------------------------------
//| 0 - start                                                                                                   |
//| 1 - JSlibrariesDone                                                                                         |
//| 2 - CSSs are injected                                                                                       |
//| 3 - asset request sent                                                                                      |
//| 4 - aseet respose received (got all good generated HTML)                                                    |
//| 5 - html injected                                                                                           |
//| 6 - all injection done                                                                                      |
//--------------------------------------------------------------------------------------------------------------
//*/
//
//console.log("Im coming from Phalcon now :) ");
//console.log("***********************************Device orientation******************************************");
//
// function setVideoList(){
//        $("#setVideoInnerPlaylist").css("top",$(".tabContects").innerHeight()-100+"px");
//	console.log("attempt");
//    }
//
//var height = $(window).height();
//var width = $(window).width();
//var r360loaded = false;
//
//var orientation = null;
//
//if(width>height) {
//    // Landscape
//    orientation = 2;
//    console.log("LANDSCAPE");
//  } else {
//    // Portrait
//    orientation = 1;
//    console.log("PORTRAIT");
//  }
//
//console.log("*************** bandwidth measurement ***************************************");
//
//
//      var DAY_IN_MS = 1;
//      var d1 = new Date();
//
//      $.ajax({
//          url: "http://rmp.timico.dev/files/bandwidth/100kdummyfile.jpg",
//          cache: false,
//          success: function(response){
//	    var d2 = new Date();
//	    console.log("done");
//	    console.log("dowload time : "+(d2 - d1) / DAY_IN_MS);
//
//	    var timediff = (d2 - d1) / DAY_IN_MS
//
//	    var speed = 100/(timediff/1000)*8;
//
//	    if (speed>0 && speed<=250 ) {
//	      quailityType = 2;
//	      console.log(speed+" Bandwidth : GPRS/EDGE -> using quality compression down to 35%");
//	    }
//
//	    if (speed>250 && speed<=800 ) {
//	      quailityType = 3;
//	      console.log(speed+" kb/s");
//	       console.log(speed+" Bandwidth : G3 -> using quality compression down to 50%");
//	    }
//
//	    if (speed>800 && speed<=3000  ) {
//	      quailityType = 4;
//	      console.log(speed+" kb/s");
//	       console.log(speed+" Bandwidth : cheap home DSL / 4G -> using quality compression down to 60%");
//	    }
//
//	    if (speed>3000  ) {
//	      quailityType = 5;
//	      console.log(speed+" kb/s");
//	       console.log(speed+" Bandwidth : a good internet lucky user -> using quality compression down to 75%");
//	    }
//
//
//
//
//console.log("***********************************Checking jquery******************************************");
//
//
//
//var loadingStatus = 0;
//
//if (typeof jQuery === 'undefined') {
//  //alert("There is no JQUERY available");
//
//}
//
//  console.log("***********************************START loading******************************************");
//
//
//  console.log("OK Jquery is fine");
//  console.log("***********************************START loading******************************************");
//
//
//
//
//  var jsCounter = 0;
//  jQuery.noConflict();
//  if (quailityType>2) {
//
//			    var jsLibraries = new Array(
//			  /************************************** general libraries ***************************************/
//
//              "http://rmp-bt.timico.dev/js/rmp/config/config.js",
//			  "http://rmp.timico.dev/js/rmp/libraries/jquery.js",
//			  "http://rmp.timico.dev/js/rmp/libraries/jquery.mobile.vmouse.js",
//			  "http://rmp.timico.dev/js/rmp/libraries/jquery.mousewheel.min.js",
//
//			  /************************************** RMP ***************************************/
//
//			  "http://rmp.timico.dev/js/rmp/expo360/pngFixer.js",
//			  "http://rmp.timico.dev/js/rmp/expo360/buttons.js",
//			  "http://rmp.timico.dev/js/rmp/expo360/Expo360.js",
//
//              /************************************** Video ***************************************/
//
//              "http://rmp-bt.timico.dev/js/rmp/video/video_player.js",
//
//			  /************************************** GALLERY ***************************************/
//
//			  "http://rmp.timico.dev/js/rmp/gallery/jquery.elevatezoom.js"
//
//			  );
//
//
//  }else{
//
//
//			  var jsLibraries = new Array(
//			  /************************************** general libraries ***************************************/
//			  "http://rmp.timico.dev/js/rmp/libraries/jquery.js",
//			  "http://rmp.timico.dev/js/rmp/libraries/jquery.mobile.vmouse.js",
//			  "http://rmp.timico.dev/js/rmp/libraries/jquery.mousewheel.min.js"
//			  );
//
//  }
//
//
//
//    /************************************************************************************************/
//    /************* including 3rd party contects as Jquery and other JS libraries ********************/
//
//   function loadLibrary(){
//
//     library = jsLibraries[jsCounter];
//     jQuery.getScript( library, function( data, textStatus, jqxhr ) {
//        console.log( library + " *** " + textStatus + " *** " + jqxhr.status ); // Data returned
//        jsCounter++;
//        if (jsCounter<jsLibraries.length) {
//          loadLibrary();
//        }else{
//           console.log("JS libraries are ready to use");
//          console.log("*********************************Loading CSS******************************************");
//          loadCss();
//
//        }
//
//    });
//    }
//
//    function onlyloadThisLibrary(){
//
//
//     console.log("hey");
//     library = jsLibraries[jsCounter];
//     $.getScript( library, function( data, textStatus, jqxhr ) {
//        console.log( library + " *** " + textStatus + " *** " + jqxhr.status ); // Data returned
//        jsCounter++;
//        if (jsCounter<jsLibraries.length) {
//          loadLibrary();
//        }else{
//           console.log("JS libraries are ready to use");
//
//        }
//
//    });
//    }
//
//    loadLibrary();
//
//    /****************************************CSS LOADER *********************************************/
//    /************************************************************************************************/
//    var cssCounter = 0;
//
//
//       var cssFiles = new Array(
//
//			    // basic css for the rmp framework layout
//                            "http://rmp.timico.dev/css/rmp/RMPFramework.css"
//
//			    // css for the rmp video player - attempt 1.
//                            ,"http://rmp.timico.dev/css/rmp/RMPVideoPlayer.css"
//
//			    // css for the rmp image gallery- attempt 1.
//                            ,"http://rmp.timico.dev/css/rmp/RMPGallery.css"
//
//                // css for the rmp details page.
//                            ,"http://rmp.timico.dev/css/rmp/RMPDetailsPage.css"
//
//			    ,"http://rmp.timico.dev/css/rmp/hotspots.css"
//
//
//			    // css for 360 Expo rotator
//                            //,"http://rmp.timico.dev/css/rmp/main.css"
//
//			    // css for 360 Expo rotators fancy font - not using - actually not sure what it is for
//			    //,"http://rich-media-player.timico.dev/css/fonts.css"
//
//                // Google Fonts Muli
//                ,"http://fonts.googleapis.com/css?family=Muli"
//
//
//                            );
//
//     /**********************************************************************************************/
//    /**********************************************************************************************/
//
//
//    function makeid()
//    {
//	var text = "";
//	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
//
//	for( var i=0; i < 10; i++ )
//	    text += possible.charAt(Math.floor(Math.random() * possible.length));
//
//	return text;
//    }
//
//    /**********************************************************************************************/
//    /**********************************************************************************************/
//
//    function loadCss(){
//
//
//
//     cssContent = cssFiles[cssCounter];
//     var cssLink = $("<link rel='stylesheet' type='text/css' href='"+cssContent+"'>");
//     $("head").append(cssLink);
//      console.log( cssContent + " *** StyleSheet has been loaded");
//      cssCounter++;
//
//        if (cssCounter<cssFiles.length) {
//          loadCss();
//        }else{
//
//          loadAssets();
//
//        }
//
//
//    }
//
//    /************************************************************************************************/
//    /************************************************************************************************/
//
//    function loadAssets(args) {
//
//     console.log("CSS files are ready to use");
//     console.log("******************************************************************************************");
//
//     var uniquRequstCode = makeid();
//
//      /*
//      var avilableWitdh = $("#RMPContentDiv").innerWidth();
//
//      if ( (orientation==1 && avilableWitdh>0 && avilableWitdh<=280)
//	   ||
//	   (orientation==2 && avilableWitdh>0 && avilableWitdh<=500)
//	  ) {
//
//	  // the image with what we need it 500px
//	  sizeTypeId = 1;
//
//      }else{
//	// the image with what we need it 880px
//	  sizeTypeId = 2;
//      }
//
//      */
//
//      // bandWith 3G - means image quailty is going to be half of the original image quality
//      //var quailityType = 4;
//
//      console.log("Asset request was sent setting ( size : "+$("#RMPContentDiv").innerWidth()+", quality : "+quailityType+")");
//      console.log("Directory"+domainlive);
//      rmpHTMLContentCreatorURL = "http://"+domainlive+"/asset/index/"+productCode+"/"+uniquRequstCode+"/"+orientation+"/"+$("#RMPContentDiv").innerWidth()+"/"+quailityType;
//      $.ajax({
//          url: rmpHTMLContentCreatorURL,
//          //dataType: "script",
//          cache: false,
//          success: function(response){
//            /************************************************************************************************/
//            /******************************* HTML injector **************************************************/
//
//            $("#RMPContentDiv").animate({ opacity: 0},250,function(){
//
//             // console.log(response);
//              $("#RMPContentDiv").hide();
//              $("#RMPContentDiv").html(response);
//              $("#RMPContentDiv").show();
//              $("#RMPContentDiv").css("opacity","0");
//              $("#RMPContentDiv").animate({ opacity: 1},1000);
//
//              /*****************tab windows handling************************/
//
//              $(".tab").click(function(){
//
//
//		if (r360loaded) {
//
//
//		console.log($(this).attr("tabindex"));
//
//                $(".tab").removeClass("active");
//                $(".tab[tabindex="+$(this).attr("tabindex")+"]").addClass("active");
//
//                $(".tabContent").hide();
//                $(".tabContent[tabindex="+$(this).attr("tabindex")+"]").css("opacity","0");
//                $(".tabContent[tabindex="+$(this).attr("tabindex")+"]").show();
//                $(".tabContent[tabindex="+$(this).attr("tabindex")+"]").animate({ opacity: 1},600);
//                setVideoList();
//
//		var myVar = null;
//		var mouseState = 0;
//
//		/*****************************************/
//
//		$(".tabContent").hover(function(){
//
//		   $( "#setVideoInnerPlaylistLayer" ).animate({
//			  opacity: 1
//			  }, 600 );
//		     mouseState = 1;
//		     console.log("in"+mouseState);
//
//
//		    myVar = setTimeout(function() {
//
//			   if (mouseState==1) {
//
//			     $( "#setVideoInnerPlaylistLayer" ).animate({
//				opacity: 0
//				}, 600 );
//
//			    console.log("timeout"+mouseState);
//
//			  }
//
// 		    }, 5000);
//
//
//		},function(){
//
//		    //console.log("out");
//		    //$("#setVideoInnerPlaylistLayer").css("opacity","0");
//
//		      mouseState = 0;
//		      $( "#setVideoInnerPlaylistLayer" ).animate({
//			  opacity: 0
//			  }, 200 , function(){
//
//			     $( "#setVideoInnerPlaylistLayer" ).clearQueue();
//			     console.log("clearQueue");
//			    });
//
//		         console.log("out"+mouseState);
//			 clearTimeout(myVar);
//
//		});
//
//		/******************************************/
//
//
//		}
//
//
//
//
//
//               /*
//
//                 $("#slider1_container").children("div").css("position","relative");*/
//                });
//
//              /********************************************************************************************************/
//              /********************************************************************************************************/
//              /******************************** switching ON tabs *****************************************************/
//
//              if (quailityType>2){
//		  var expo = new Expo360({xml:"http://"+domainlive+"/assets/xml/360RotatorRequest"+uniquRequstCode+".xml", where:"#viewer"});
//		  window.expo = expo;
//		  expo.goTo(48);
//
//		  $("#viewer>div>div").first().css("min-height","150px");
//
//	      }
//
//
//	      /********************************************************************************************************/
//	      /********************************************************************************************************/
//	      /****************************************Positioning the video playlist**********************************/
//
//		$( window ).resize(function() {$("#setVideoInnerPlaylist").css("top",$(".tabContects").innerHeight()-80+"px")
//
//		       $("#setVideoInnerPlaylist").css("top",$(".tabContects").innerHeight()-80+"px")
//
//		});
//
//	      /********************************************************************************************************/
//	      /**********************************************************************************************************/
//	      /************************************      Gallery Zoom     **********************************************/
//
//
//	      $("#zoomable").elevateZoom({
//		responsive : true,
//		zoomType : "lens",
//		lensShape : "round",
//		lensSize    : 400,
//		borderColour:	"#007d7d"
//	      });
//
//
//		$(".galleryItems").bind("click", function(e) {
//		   console.log("hey");
//		    var ez =   $('#zoomable').data('elevateZoom');
//		    ez.closeAll(); //NEW: This function force hides the lens, tint and window
//		    ez.swaptheimage($(this).attr("src"), $(this).attr("data-zoom-image"));
//		    return false;
//		});
//
//
//
//              });
//
//          }
//        });
//
//    }
//
//
//
//
//
//
//  }});
//
//
//
//
//
//
//
