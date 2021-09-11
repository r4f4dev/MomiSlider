<?php 

/**
* Plugin Name: MomiSlider
* Plugin URI: https://wordpress.com/
* Description: Simple slider
* Version: 1.0.0
* Author: @r4f4dev
* Author URI: https://t.me/r4f4dev
* License: GPL2
*/

//Закрываем доступ извне

if (!defined('ABSPATH')) {
	die;
}

/**
 * Core class
 */
class MomiSlider
{
	public $theme = 'skyblue';

			function __construct()
			{
				add_action('init', [$this, 'custom_post_type']);
				add_action('wp_enqueue_scripts', [$this, 'enqueue_front']);
				add_image_size('np_function', 600, 280, true);
				add_theme_support( 'post-thumbnails' );
				add_shortcode('MomiSlider', [$this, 'sliderFunc']);
				add_action( 'wp_footer', [$this, 'js_init'] );
				add_action('admin_menu', [$this,'add_admin_menu']);
				add_action( 'admin_init', [$this, 'settings_init'] );
				
			}

			//Регистрируем слайды как посты

			function custom_post_type()
			{
				register_post_type('MomiSlider',
					[
						'public' => true,
						'has_archive' => false,
						'label' => esc_html__('MomiSlider'),
						'supports' => ['title', 'thumbnail']
					]
				);
			}

			function js_init()
			{
				$options = get_option('momislidersettings_options');
				if (!$options['momislider_autolplay']) {
					$options['momislider_autolplay'] = true;
				}
				if (!$options['momislider_interval']) {
					$options['momislider_interval'] = 5000;
				}
				if (!$options['momislider_arrow']) {
					$options['momislider_arrow'] = true;
				}
				$result =  "
				<script>
				document.addEventListener( 'DOMContentLoaded', function () {
					new Splide( '.splide', {
						'cover' : true,
						'type' : 'fade',
						'heightRatio': 0.5,
						'interval' : ".$options['momislider_interval'].",
						'autoplay' : ".$options['momislider_autolplay'].",
						'arrows' : ".$options['momislider_arrow'].",
					} ).mount();
				} ); 
				</script> ";
				echo $result;
			}

			public function sliderFunc()
			{

				$args = array(
					'post_type' => 'MomiSlider',
					'posts_per_page' => 5
				);
				$result = '<div class="splide"><div class="splide__track"><ul class="splide__list">';


				$slides = get_posts( $args );

				foreach($slides as $row){
					$image = wp_get_attachment_image_src(get_post_thumbnail_id($row->ID),"full");
					$result .= '<li class="splide__slide"><img src="' . $image[0] . '"></li>';

				}


				$result .= '</ul></div></div>';

				return $result;

			}

			//Регистрируем стили и скрипты
			public function enqueue_front()
			{
				wp_enqueue_style('MomisliderStyle', plugins_url('assets/frontend/css/splide.min.css', __FILE__));
				wp_enqueue_style('MomisliderStyle2', plugins_url('assets/frontend/css/themes/splide-' . $this->theme .'.min.css', __FILE__));
				wp_enqueue_style('MomislidercustomStyles', plugins_url('assets/frontend/css/styles.css', __FILE__));
				wp_enqueue_script('MomisliderScript', plugins_url('assets/frontend/js/splide.min.js', __FILE__));
			}

			 //Add menu page
			public function add_admin_menu(){
				add_menu_page(
					esc_html__( 'MomiSlider settings page', 'momislider_settings' ),
					esc_html__('MomiSlider Settings','momislider'),
					'manage_options',
					'momislider_settings',
					[$this, 'momislider_page'],
					'dashicons-admin-multisite',
					100
				);
			}

			public function momislider_page()
			{
				require_once plugin_dir_path(__FILE__) . 'admin/admin.php';
			}

			//Добавляем настройки слайда
			public function settings_init()
			{
				register_setting('MomiSliderSettings', 'momislidersettings_options');

				add_settings_section('momislidersettings_section',
									 esc_html__('Settings', 'MomiSlider'),
									 [$this, 'setting_section_html'],
									 'momislider_settings');

				add_settings_field('momislider_autolplay',
									esc_html__('Autoplay slider','MomiSlider'),
									[$this, 'autoplay_html'], 'momislider_settings',
									'momislidersettings_section');

				add_settings_field('momislider_interval',
									esc_html__('Interval slider(in milliseconds)','MomiSlider'),
									[$this, 'interval_html'], 'momislider_settings',
									'momislidersettings_section');
				add_settings_field('momislider_arrows',
									esc_html__('Arrows','MomiSlider'),
									[$this, 'arrows_html'], 'momislider_settings',
									'momislidersettings_section');
			}

			public function setting_section_html()
			{
				
			} 

			public function autoplay_html()
			{
				 $options = get_option('momislidersettings_options');
				 $autoplay = $options['momislider_autolplay'];

				 if (isset($options['momislider_autolplay'])) {
				 	if ($autoplay == 'true') {
				 		echo '<div class="momiselect">
					  			<select name="momislidersettings_options[momislider_autolplay]">
					    			<option selected value="true">On</option>
					    			<option value="false">Off</option>
					  			</select>
							  </div>';
				 	}

				 	if ($options['momislider_autolplay'] == 'false') {
				 		echo '<div class="select">
					  			<select name="momislidersettings_options[momislider_autolplay]">
					    			<option value="true">On</option>
					    			<option selected value="false">Off</option>
					  			</select>
							  </div>';
				 	}
				 }

		 }

		 public function interval_html()
		 {
			 	 $options = get_option('momislidersettings_options');
				 $interval = $options['momislider_interval'];
				 if (isset($interval)) {
				 	$result	= '<input type="text" name="momislidersettings_options[momislider_interval]" value="' . $options['momislider_interval'] . '" />';
				 	echo $result;
				 }

				 if (!isset($interval)) {
				 	$result	= '<input type="text" name="momislidersettings_options[momislider_interval]" value=""/>';
				 	echo $result;
				 }
				
		}

		public function arrows_html()
		{
			 $options = get_option('momislidersettings_options');
				 $arrow = $options['momislider_arrow'];

				 if (isset($arrow)) {
				 	if ($arrow == 'true') {
				 		echo '<div class="momiselect">
					  			<select name="momislidersettings_options[momislider_arrow]">
					    			<option selected value="true">On</option>
					    			<option value="false">Off</option>
					  			</select>
							  </div>';
				 	}

				 	if ($arrow == 'false') {
				 		echo '<div class="select">
					  			<select name="momislidersettings_options[momislider_arrow]">
					    			<option value="true">On</option>
					    			<option selected value="false">Off</option>
					  			</select>
							  </div>';
				 	}
				 }
		}

}

		if (class_exists('MomiSlider')) {
			new MomiSlider();
		}