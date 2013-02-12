<?php
/**
 * Sets up the core framework's widgets. 
 *
 * @package PlacesterBlueprint
 * @subpackage Functions
 */

/* Register Hybrid widgets. */
add_action( 'widgets_init', 'pls_register_widgets' );

/**
 * Registers the core frameworks widgets.  These widgets typically overwrite the equivalent default WordPress
 * widget by extending the available options of the widget.
 *
 * @since 0.0.1
 * @uses register_widget() Registers individual widgets with WordPress
 * @link http://codex.wordpress.org/Function_Reference/register_widget
 */
function pls_register_widgets() {

	/** Load the Placester Agent widget. */
	require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/agent.php' );

	/** Load the Placester Office widget. */
	require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/office.php' );

	/** Load the Placester Contact widget. */
	if( ! pls_has_plugin_error() ) {
	  require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/contact.php' );
    register_widget( 'Placester_Contact_Widget' );
	}

	/** Load the Placester Recent Posts widget. */
	require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/recent-posts.php' );

	/** Load the Placester Quick Search widget. */
	require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/quick-search.php' );

	/** Load the Placester Listings widget. */
	require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/listings.php' );

  /** Load the Placester Mortgage Calculator widget. */
  require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/mortgage-calculator.php' );

  /** Load the Placester Feedburner Subscribe Form widget. */
  require_once( trailingslashit( PLS_FUNCTIONS_DIR ) . 'widgets/feedburner-subscribe-form.php' );

	/* Register each of the widgets. */
	register_widget( 'PLS_Widget_Agent' );
	register_widget( 'PLS_Widget_Office' );
	register_widget( 'PLS_Widget_Recent_Posts' );
	register_widget( 'PLS_Quick_Search_Widget' );
	register_widget( 'PLS_Widget_Listings' );

  if ( current_theme_supports( 'pls-widget-mortgage-calculator' ) ) {
    register_widget( 'PLS_Widget_Mortgage_Calculator' );
  }
  
  if ( current_theme_supports( 'pls-widget-feedburner-form' ) ) {
    register_widget( 'PLS_Widget_Feedburner_Widget' );
  }
  
}
