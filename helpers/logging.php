<?php 

PL_Logging::init();
class PL_Logging {

	static $hook;

	 function init() {
	 	$logging_option = PL_Option_Helper::get_log_errors();
	 	if ($logging_option) {
			add_action('admin_head', array(__CLASS__, 'start'));
			add_action('admin_footer', array(__CLASS__, 'events'));
			add_action('admin_enqueue_scripts', array(__CLASS__, 'record_page'));
			register_activation_hook( PL_PARENT_DIR, 'activation' );
		}
	 }

	 function record_page ($hook) {
	 	self::$hook = $hook;
	 }

	 function start () {
	 	$hook = self::$hook;
	 	$pages = array('placester_page_placester_properties', 'placester_page_placester_property_add', 'placester_page_placester_settings', 'placester_page_placester_support', 'placester_page_placester_theme_gallery');
		if (!in_array($hook, $pages)) { return; }

	 	ob_start();
	 	?>
	 		<script type="text/javascript">
			    (function(c,a){window.mixpanel=a;var b,d,h,e;b=c.createElement("script");
			    b.type="text/javascript";b.async=!0;b.src=("https:"===c.location.protocol?"https:":"http:")+
			    '//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';d=c.getElementsByTagName("script")[0];
			    d.parentNode.insertBefore(b,d);a._i=[];a.init=function(b,c,f){function d(a,b){
			    var c=b.split(".");2==c.length&&(a=a[c[0]],b=c[1]);a[b]=function(){a.push([b].concat(
			    Array.prototype.slice.call(arguments,0)))}}var g=a;"undefined"!==typeof f?g=a[f]=[]:
			    f="mixpanel";g.people=g.people||[];h=['disable','track','track_pageview','track_links',
			    'track_forms','register','register_once','unregister','identify','alias','name_tag',
			    'set_config','people.set','people.increment','people.track_charge','people.append'];
			    for(e=0;e<h.length;e++)d(g,h[e]);a._i.push([b,c,f])};a.__SV=1.2;})(document,window.mixpanel||[]);
			    mixpanel.init("9186cdb540264089399036dd672afb10");
			</script>
	 	<?php
	 	echo ob_get_clean();
	 }

	 function events () {
	 	$hook = self::$hook;
	 	$pages = array('placester_page_placester_properties', 'placester_page_placester_property_add', 'placester_page_placester_settings', 'placester_page_placester_support', 'placester_page_placester_theme_gallery');
		if (!in_array($hook, $pages)) { return; }

	 	ob_start();

	 	if (!PL_Option_Helper::api_key()) {
	 		?>
		 		<script type="text/javascript">
		 			jQuery('#signup_wizard').live('dialogopen', function () {
		 				mixpanel.track("SignUp: Overlay Opened");			
		 			});
		 			jQuery('#signup_wizard').live('dialogclose', function () {
		 				mixpanel.track("SignUp: Overlay Closed");			
		 			});
		 			jQuery('#pls_search_form input#email').live('focus', function() {
		 				mixpanel.track("SignUp: Edit Sign Up Email");			
		 			});
		 			jQuery('#confirm_email_button').live('click', function() {
		 				mixpanel.track("SignUp: Confirm Email Click");			
		 			});
		 		</script>	
		 	<?php	
	 	}

	 	if ($hook == 'placester_page_placester_property_add') {
		 	?>
		 		<script type="text/javascript">
		 			jQuery(document).ready(function($) {
		 				mixpanel.track("Add Property: View");		
		 				$('#add_listing_publish').bind('click', function () {
		 					mixpanel.track("Add Property: Submit");		
		 				});
			 		});
		 		</script>	
		 	<?php	
	 	}

	 	if ($hook == 'placester_page_placester_theme_gallery') {
		 	?>
		 		<script type="text/javascript">
		 			jQuery(document).ready(function($) {
		 				mixpanel.track("Theme Gallery: View");		
		 				$('#theme_gallery_placester').bind('click', function () {
		 					mixpanel.track("Theme Gallery: To Placester");		
		 				});
			 		});
		 		</script>	
		 	<?php	
	 	}

	 	echo ob_get_clean();
	 }

	 function activation () {

	 }
}
