
<ProductViewer>
	<!-- Fix Size -->
	<!-- width for the image view -->
	<viewWidth>330</viewWidth>
	<!-- height for the image view -->
	<viewHeight>206</viewHeight>


	<!-- value for the slide ease (value must be >= 1) -->
	<ease>100</ease>
	<!-- value for the padding ease (value must be >= 1) -->
	<padding_ease>2.5</padding_ease>
	<!-- Inertia amount: 0 is not inertia at all, 1 is keep rotating in the force you threw it in -->
	<inertia>1</inertia>
	
	<grab_hand_cursor>true</grab_hand_cursor>
	
	<!-- Mousewheel function: possibility to set it to 'rotate' or 'zoom' or 'none'-->
	<mouse_wheel_function>none</mouse_wheel_function>
	<mouse_wheel_speed>0.2</mouse_wheel_speed>
	
	
	<!-- value of the max zoom allowed 
	(usually to not lose quality you 
		set this to 1 if you're not using big images
		set this to the minimum between (bigImage width / normalImage width) AND (bigImage height / normalImage height)) -->
	<maxZoom>1</maxZoom>
	<!-- zoom speed on mousewheel and on click of the plus and minus buttons of the zoom-slider  -->
	<zoomSpeed>0.2</zoomSpeed>
	<!-- ease applied to the zooming (minimum value 1 for no ease)  -->
	<zoomEase>10</zoomEase>
	
	
	<!-- initial autoplay set to true or false  -->
	<autoplay>false</autoplay>
	<!-- autoplay rotation speed  -->
	<autoplaySpeed>3</autoplaySpeed>

	<!-- set to true if your images list isn't in positive rotation 
	(if by rotating your object is going in the oposite direction the you have to change this field) -->
	<reverse>false</reverse>
	
	<!-- set of controls to include in the menu in order -->
	<!-- 
	They can be:
		rotate - This button enables the rotation action
		pan - This button enables the pan action
		left - rotates the object left
		right - rotates the object right
		autoplay - play/pause autoplay button
		zoom-in - makes a full zoom in
		zoom-out - makes a full zoom out
		reset - restore both zoom and rotation to the starting position of the object
		zoom-slider - dragging this slider or clicking the background the object will zoom in or out
		playback-slider - dragging this slider or clicking the background the object will rotate
		hyperlink - Makes a hyperlink in the menu (this must have an href attribute with the link you want)
	-->
	<controls>
		<control>left</control>
		<control>playback-slider</control>
		<control href="http://codecanyon.net">hyperlink</control>
		<control>right</control>
	</controls>
	
	<!-- 
	Control Panel Options
	-->
	<panel>
		<width>250</width>
		<height>36</height>
		
		<xOffset>0</xOffset>
		<yOffset>-46</yOffset>
		
		<!-- 'always' or 'roll_over' -->
		<show>always</show>
		
		<background_color>#000000</background_color>
		<background_alpha>0.1</background_alpha>
		<background_pattern>none</background_pattern>
		
		<round_corners>7</round_corners>
		
		<buttons_side_margin>8</buttons_side_margin>
		<buttons_tween_time>500</buttons_tween_time>
		
		<!-- ONLY CHANGE THE FOLLOWING IF YOU WANT TO CHANGE THE UI IMAGES -->
		<ui_folder>http://rich-media-player.timico.dev/RMPAseetCache/ui_images1/</ui_folder>
		<!-- buttons options -->
		<buttons_width>36</buttons_width>
		<buttons_height>36</buttons_height>
		
		<!-- divider options -->
		<divider_width>1</divider_width>
		<divider_height>36</divider_height>
		
		<!-- slider options -->
		<slider_width>auto</slider_width>
		<slider_height>6</slider_height>
		<slider_background_color>#000000</slider_background_color>
		<slider_background_alpha>0.3</slider_background_alpha>
		<slider_background_pattern>none</slider_background_pattern>
		<slider_round_corners>2</slider_round_corners>
		
		<!-- zoom slider options -->
		<zoom_subbuttons_width>14</zoom_subbuttons_width>
		<zoom_subbuttons_height>14</zoom_subbuttons_height>
		<zoom_subbuttons_distance>12</zoom_subbuttons_distance>
		
		<!-- dragger options -->
		<dragger_width>36</dragger_width>
		<dragger_height>36</dragger_height>
	</panel>
	
	<!-- 
	Initial loading options
	-->
	<loading>
		<loading_text>#span#loaded_images/total_images#spanEnd#</loading_text>
		
		<background_color>#000000</background_color>
		<background_alpha>1</background_alpha>
		<background_pattern>none</background_pattern>
		
		<text_font>AllerRegular</text_font>
		<text_size>12</text_size>
		<text_color>#BBBBBB</text_color>
		<text_span_color>#FFFFFF</text_span_color>
		<text_background_color>#000000</text_background_color>
		<text_background_alpha>0.8</text_background_alpha>
		<text_background_pattern>none</text_background_pattern>
		<text_background_round_corner>5</text_background_round_corner>
	</loading>
	
	<!-- 
	Zoom Window options
	-->
	<include_zoom_window>false</include_zoom_window>
	<zoom_window>
		<window_width>150</window_width>
		<window_height>auto</window_height>
		
		<background_color>#000000</background_color>
		<background_alpha>0.4</background_alpha>
		<background_pattern>none</background_pattern>
		<padding>1</padding>
		
		<selection_line_color>#ff0000</selection_line_color>
		<selection_line_alpha>0.4</selection_line_alpha>
	</zoom_window>
	
	
	<!-- Tooltips options  -->
	<include_tooltips>true</include_tooltips>
	<tooltips_texts>
		<rotate>Rotate</rotate>
		<pan>Pan</pan>
		
		<play>Play rotation</play>
		<pause>Stop rotation</pause>
		
		<rotate_slider>Rotate rotate_number</rotate_slider>
		<rotate_left>Rotate left</rotate_left>
		<rotate_right>Rotate right</rotate_right>
		
		<reset>Reset</reset>
		
		<zoom_slider>Zoom zoom_number%</zoom_slider>
		<zoom_in>Zoom In</zoom_in>
		<zoom_out>Zoom Out</zoom_out>
		
		<hyperlink>Hyperlink</hyperlink>
	</tooltips_texts>
	
	<tooltips>
		<text_font>AllerRegular</text_font>
		<text_size>12</text_size>
		<text_color>#ffffff</text_color>
		
		<left_right_padding>10</left_right_padding>
		<top_bottom_padding>5</top_bottom_padding>
		
		<background_color>#FF3430</background_color>
		<background_alpha>1</background_alpha>
		
		<round_corners>5</round_corners>
		
		<fadeTime>200</fadeTime>
	</tooltips>
	
	
	
	<!-- Hotspots images path (must end in '/') -->
	<hotspotsImagesPath>http://rich-media-player.timico.dev/RMPAseetCache/ui_images1/</hotspotsImagesPath>
	<hotspotsButtons>
		
		<!-- HOTSPOTS' BUTTONS
				id			-> id for this button
				out 		-> button normal state image
				over		-> button over state image
				width		-> button image width
				height		-> button image height
				tweenTime	-> fade in/out time in miliseconds
		-->
		<button>
			<id>1</id>
			<out>hotspot_out.png</out>
			<over>hotspot_over.png</over>
			<width>40</width>
			<height>40</height>
			<tweenTime>300</tweenTime>
		</button>
		
		<button>
			<id>close</id>
			<out>close_button.png</out>
			<over>close_button.png</over>
			<width>36</width>
			<height>36</height>
			<tweenTime>300</tweenTime>
		</button>
	</hotspotsButtons>
	

	<!-- Images Path (must end in '/') -->
	<imagesPath>http://rich-media-player.timico.dev/RMPAseetCache/images/Expo360/Small/</imagesPath>
	<!-- Big Images Path (must end in '/') -->
	<imagesBigPath>none</imagesBigPath>
	
	<!-- Images List -->
	<images>
             {changeContect}
		
	</images>
</ProductViewer>