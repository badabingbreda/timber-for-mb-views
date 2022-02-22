<?php
/**
 * Timber for MB Views
 *
 * @package     timber-for-mbviews
 * @author      Badabingbreda
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Timber for MB Views
 * Plugin URI:  https://www.badabing.nl
 * Description: Unlock Timber for MB Views
 * Version:     1.2.1
 * Author:      Badabingbreda
 * Author URI:  https://www.badabing.nl
 * Text Domain: timber-for-mb-views
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

use TimberMBViews\StylesScripts;

define( 'TIMBERMBVIEWS_VERSION', '1.2.1' );
define( 'TIMBERMBVIEWS_DIR', plugin_dir_path( __FILE__ ) );
define( 'TIMBERMBVIEWS_FILE', __FILE__ );
define( 'TIMBERMBVIEWS_URL', plugins_url( '/', __FILE__ ) );


require_once( 'inc/StylesScripts.php' );
require_once( 'inc/TimberMBViews.php' );

new StylesScripts();

if ( !function_exists( 'try_to_use_timber' ) ) {
    add_action( 'plugins_loaded' , 'try_to_use_timber' );
    function try_to_use_timber() {
        if ( class_exists( 'Timber' ) ) {
            new TimberMBViews\Main();
        }
    }
}
