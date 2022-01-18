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
 * Version:     1.0.0
 * Author:      Badabingbreda
 * Author URI:  https://www.badabing.nl
 * Text Domain: timber-for-mb-views
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


if ( !function_exists( 'try_to_use_timber' ) ) {

    add_action( 'plugins_loaded' , 'try_to_use_timber' );

    function try_to_use_timber() {
    
        if ( class_exists( 'Timber' ) ) {
    
            // add MB $data to the environment
            add_filter( 'timber/twig' , function( $twig ) {
        
                // Proxy for all PHP/WordPress functions under 'mb' namespace.
                $twig->addGlobal( 'mb', new \MBViews\TwigProxy );
        
                $twig = apply_filters( 'mbv_twig_env', $twig );
        
                return $twig;
        
            } , 10, 1 );
        
            // Add the mbv_fs_paths to the list of paths for Timber
            add_filter( 'timber/loader/paths' , function( $paths ) {
        
                // Allow developers to add Twig Filesystem Loader(s) by providing path(s)
                $mbv_fs_paths = apply_filters( 'mbv_fs_paths', [] );
                return array_merge( $paths , $mbv_fs_paths );
            } );
        
            // Extend the Timber FS loader with our mbv_fs_loader filters
            add_filter( 'timber/loader/loader' , function( $fs ) {
                return apply_filters( 'mbv_fs_loader', $fs );
            } );
        
            // setup the Timber output
            add_filter( 'mbv_render_output' , function( $output , $view , $data ) {
        
                global $wp_query;
        
                $render_as = 'compile';
        
                // Allow developers to add Twig Filesystem Loader(s) by providing path(s)
                $mbv_fs_paths = apply_filters( 'mbv_fs_paths', [] );
        
                if ( is_numeric( $view ) ) {      		// Get view by ID.
                    $view_from_post = get_post( $view );
                }elseif ( is_string( $view ) ) { 		// Get view by slug.
                    $view_from_post = get_page_by_path( $view, OBJECT, 'mb-views' );
                }
                // Else: view is a post object.
                if ( !empty( $view_from_post ) ) {
                    $render = $view_from_post->post_content;
                    $render_as = 'compile_string';
                } else {
                    // check if additional mbv_twig_paths have been set
                    // if so, assume view is a filepath. Otherwise return ''
                    if ( sizeof($mbv_fs_paths)==0 )	{
                        return '';
                    } 
                }
        
                $data = \Timber::get_context();
                // current post
                $data['post'] = new \TimberPost();
        
                // get the wp_query->query_vars
                $args = $wp_query->query_vars;
        
                if ( is_archive() ) {
                    // the query posts
                    $data['posts'] = new \Timber\PostQuery( $args );
                } else {
                    $data[ 'posts' ] = null;
                }
        
                $data['query']['vars'] = $args;
        
                $data = apply_filters( 'mbv_data_timber', $data );
        
                if ( 'compile' == $render_as ) {
                    $output .= \Timber::compile( $view , $data );
                } else {
                    $output .= \Timber::compile_string( $render , $data );
                }
        
                return $output;
            } , 10 , 3 );
            
        }
        
    
    }
    

}

