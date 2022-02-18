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
 * Version:     1.1.0
 * Author:      Badabingbreda
 * Author URI:  https://www.badabing.nl
 * Text Domain: timber-for-mb-views
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

require_once( 'inc/TimberMBViews.php' );

if ( !function_exists( 'try_to_use_timber' ) ) {
    add_action( 'plugins_loaded' , 'try_to_use_timber' );
    function try_to_use_timber() {
        if ( class_exists( 'Timber' ) ) {
            new TimberMBViews\Main();
        }
    }
}
