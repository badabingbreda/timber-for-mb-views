<?php
namespace TimberMBViews;

class StylesScripts {

    public function __construct() {

        add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );

    }

	private static function is_screen() {
		return 'mb-views' === get_current_screen()->id;
	}


    public static function styles_scripts() {

		if ( !self::is_screen() ) return;
        if ( !is_admin() ) return;
        // if Timber is deactivated for some reason, don't hide the button
        if ( !class_exists( 'Timber' ) ) return;

        wp_enqueue_style( 'timber-mb-views', TIMBERMBVIEWS_URL . 'css/timber-for-mb-views.css', array(), TIMBERMBVIEWS_VERSION );


    }
}
