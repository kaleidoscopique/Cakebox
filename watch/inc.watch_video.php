<div id="reader-n-player">
	<?php if($config->video_player == "divxwebplayer"): ?>

		<!-- Embed DivX Player -->
		<object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="600" height="400" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
		    <param name="custommode" value="none" />
		    <param name="autoPlay" value="0" />
		    <param name="src" value="<?php echo $file->url; ?>" />
		    <embed type="video/divx" src="<?php echo $file->url; ?>" custommode="none" width="600" height="400" autoPlay="0" pluginspage="http://go.divx.com/plugin/download/"></embed>
		</object>
		<!-- / DivX -->

	<?php elseif($config->video_player == "vlc"): ?>

		<!-- Embed VLC -->
		<embed type="application/x-vlc-plugin" name="VLC" autoplay="yes" loop="no" volume="100" width="640" height="480" target="mymovie.avi">
		<!-- / VLC -->

	<?php endif; ?>
</div>