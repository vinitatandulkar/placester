<?php
/**
Plugin Name: Real Estate Pro by Placester
Description: Easily create a rich real estate web site for yourself or your agency.
Plugin URI: http://placester.com/wordpress/plugin/
Author: Frederick Townes, Matthew Barba, Placester
Version: 0.2.0
Author URI: http://www.placester.com/developer/wordpress
*/

/*  Copyright (c) 2011 Frederick Townes <frederick@placester.com>
	All rights reserved.

	Placester Promoter is distributed under the GNU General Public License, Version 2,
	June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
	St, Fifth Floor, Boston, MA 02110, USA

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
include_once( 'core/init.php' );
include_once( 'core/util.php' );
include_once( 'core/webservice_client.php' );
include_once( 'core/settings_functions.php' );
include_once( 'core/listings_list_util.php' );
include_once( 'options/init.php' );
include_once( 'admin/init.php' );
include_once( 'admin/widgets.php' );

register_activation_hook( __FILE__, 'placester_activate' );



/**
 * Registers filter form on a page which will control 
 * property lists / property maps on this page.
 * 
 * @param string $form_dom_id - DOM id of form object containing filter
 */
function placester_register_filter_form( $form_dom_id ) {
    require_once( 'core/register_filter_form.php' );
}



/**
 * Prints google maps object containing properties
 * 
 * @param array $parameters - configuration data
 *
 *        configuration elements:
 *
 *        js_on_marker_click =>
 *           js function name called when marker is clicked with prototype:
 *           function(markerData)
 *             markerData - array of all queried property fields
 *
 *        js_get_marker_class => 
 *           js function name called to get css class for marker with prototype:
 *           function(markerData)
 *             markerData - array of all queried property fields
 */
function placester_listings_map( $parameters = array() ) {
    require_once( 'core/listings_map.php' );
}



/**
 * Prints standalone list of properties
 * 
 * @param array $parameters - configuration data
 *        Configuration elements are different based on list mode.
 *        There are different modes defined by 'table_type' parameter.
 *
 *        For table_type = datatable list is displayed using datatables.net 
 *        library. Parameters are:
 *
 *          table_type => 'datatable'
 *          paginate =>
 *            number of rows for each page
 *          attributes
 *            array, fields to display, where key is field name
 *     
 *            fieldname => 
 *              label =>
 *                Name of field, how to display it
 *              width =>
 *                Width of field
 *              js_renderer
 *                JS function called to convert field content and return
 *                html representation of field to display
 *
 *        For table_type = html list is displayed as sequence of pure html <div>
 *        elements where each element represent single listing.
 *        Paramteres are:
 *        library. Parameters are:
 *
 *          table_type => 'html'
 *          js_row_renderer =>
 *            JS function name taking array of property fields data and 
 *            returning html to print.
 *          pager =>
 *            array. Elements are:
 *
 *            render_in_dom_element =>
 *              If specified - pager will be rendered to that DOM id
 *            rows_per_page =>
 *              Number of properties to print at single page
 *            css_current_button =>
 *              CSS style of "current page" button
 *            css_not_current_button =>
 *              CSS style of other page-switch buttons
 *            first_page =>
 *              array, configuration of "first page" button of pager.
 *              parameters are:
 *              visible =>
 *                true / false
 *              label =>
 *                html of button' text
 *            previous_page =>
 *              array, configuration of "previous page" button of pager.
 *              same as for "first page"
 *            next_page =>
 *              array, configuration of "next page" button of pager.
 *              same as for "first page"
 *            last_page' => 
 *              array, configuration of "last page" button of pager.
 *              same as for "first page"
 *            numeric_links =>
 *              array, configuration of numeric links buttons of pager.
 *              parameters are:
 *              visible =>
 *                true / false
 *              max_count => 
 *                maximum number of page links to show
 *              more_label
 *                if there are more pages than printed, this html is inserted
 *              css_outer
 *                CSS class of outer div for numberic links
 *          attributes =>
 *            array of fields name to extract from data storage.
 *            Dont ask for fields are really not displayed - that will
 *            unreasonably slow down requests.
 */
function placester_listings_list($parameters) {
    require_once('core/listings_list_lone.php');
}



/**
 * Prints list of properties which are displayed right now on the map
 * So this list can be used only on pages with map
 *
 * @param array $parameters - configuration data
 *        The same as for "placester_listings_list" function
 */
function placester_listings_list_of_map($parameters) {
    require_once('core/listings_list_of_map.php');
}