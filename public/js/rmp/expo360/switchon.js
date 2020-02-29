
       var expo = new Expo360({xml:"http://rich-media-player.timico.develop/RMPAseetCache/Expo360XML/settings3.xml", where:"#viewer"});
				
				$("#goFront").click(function(){
					expo.goTo(0);
					return false;
				});


  