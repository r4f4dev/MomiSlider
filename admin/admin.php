<div>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
		<?php 
			settings_fields('MomiSliderSettings');
			do_settings_sections('momislider_settings');
			submit_button();
		?>
	</form>
	<p>Use with [MomiSlider] shortcode</p>
	<p>@r4f4dev</p>
</div>
