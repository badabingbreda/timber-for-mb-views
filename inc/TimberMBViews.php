<?php
namespace TimberMBViews;

class Main {

    public function __construct() {

        add_filter( 'timber/twig'           , __CLASS__ . '::mb_extend', 10, 1 );
        add_filter( 'timber/loader/paths'   , __CLASS__ . '::add_mbv_fs_paths', 10, 1 );
        add_filter( 'timber/loader/loader'  , __CLASS__ . '::extend_fs_loader', 20, 1 );
        add_filter( 'mbv_data_timber'       , __CLASS__ . '::extend_mbv_data', 10, 1 );
        add_filter( 'mbv_render_output'     , __CLASS__ . '::render', 10, 3 );

    }

    /**
     * mb_extend
     *
     * add MB $data to the environment
     * @param  mixed $twig
     * @return void
     */
    public static function mb_extend( $twig ) {
        // Proxy for all PHP/WordPress functions under 'mb' namespace.
        $twig->addGlobal( 'mb', new \MBViews\TwigProxy );

        $twig = apply_filters( 'mbv_twig_env', $twig );

        return $twig;

    }

    /**
     * add_mbv_fs_paths
     *
     * Add the mbv_fs_paths to the list of paths for Timber    
     * @param  mixed $paths
     * @return void
     */
    public static function add_mbv_fs_paths( $paths ) {
        // Allow developers to add Twig Filesystem Loader(s) by providing path(s)
        $mbv_fs_paths = apply_filters( 'mbv_fs_paths', [] );
        return array_merge( $paths , $mbv_fs_paths );
    }
    
    /**
     * extend_fs_loader
     *
     * Extend the Timber FS loader with our mbv_fs_loader filters
     * @param  mixed $fs
     * @return void
     */
    public static function extend_fs_loader( $fs ) {

        // load our alternative TwigLoader
        require_once( 'TwigLoader.php' );

        $customloader = new TwigLoader;
        $chainloader = new \Twig\Loader\ChainLoader( [ $customloader , $fs ] );
        return $chainloader;
    }
    
    /**
     * extend_mbv_data
     *
     * Add data to the the filter about the post, posts, query vars
     * @param  mixed $data
     * @return void
     */
    public static function extend_mbv_data( $data ) {

        // return $data when Timber isn't active
        if ( !self::timber_active() ) return $data;

        global $wp_query;
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
        $data[ 'query' ][ 'found_posts' ] = $wp_query->found_posts;

        return $data;
    }
    
    /**
     * render
     * 
     * Change the render method used to Timber
     * @param  mixed $output
     * @param  mixed $view
     * @param  mixed $data
     * @return void
     */
    public static function render( $output , $view , $viewdata ) {
        
        // return $data when Timber isn't active
        if ( !self::timber_active() ) return $output;

        $render_as = 'compile';

        // Allow developers to add Twig Filesystem Loader(s) by providing path(s)
        $mbv_fs_paths = apply_filters( 'mbv_fs_paths', [] );

        if ( is_numeric( $view ) ) {      		// Get view by ID.
            $view_from_post = get_post( $view );
        } elseif ( is_string( $view ) ) { 		// Get view by slug.
            $view_from_post = get_page_by_path( $view, OBJECT, 'mb-views' );
		} elseif ( is_a( $view , 'WP_Post' ) ) {
			$view_from_post = $view;
        }

        // Else: view is a post object.
        if ( !empty( $view_from_post ) ) {
            $render = $view_from_post;
            $render_as = 'compile_string';
        } else {
            // check if additional mbv_twig_paths have been set
            // if so, assume view is a filepath. Otherwise return ''
            if ( sizeof($mbv_fs_paths)==0 )	{
                return '';
            } 
        }

        $data = \Timber::get_context();
        // merge viewdata with Timber context
        $data = array_merge( $viewdata , $data );
        $data = apply_filters( 'mbv_data_timber', $data );

        if ( 'compile' == $render_as ) {
            $output .= \Timber::compile( $view , $data );
        } else {
            $output .= \Timber::compile_string( $render->post_content , $data );
        }

        return $output;
    }
    
    /**
     * timber_active
     *
     * @return void
     */
    private static function timber_active() {
        return class_exists( 'Timber' ); 
    }


}