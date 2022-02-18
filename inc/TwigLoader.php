<?php

namespace TimberMBViews;

use Twig\Loader\LoaderInterface;
use Twig\Source;
use Twig\Error\LoaderError;

class TwigLoader implements LoaderInterface {
	/**
	 * Returns the source context for a given template logical name.
	 *
	 * @param string $name The template logical name.
	 *
	 * @return Source
	 *
	 * @throws LoaderError When $name is not found.
	 */
	public function getSourceContext( $name ) {
		$view = is_numeric( $name ) ? get_post( $name ) : get_page_by_path( $name, OBJECT, 'mb-views' );

		if ( empty( $view ) ) {
			// Translators: %s - Name of the view.
			throw new LoaderError( sprintf( __( 'View "%s" is not defined.', 'mb-views' ), $name ) );
		}

		$type       = get_post_meta( $view->ID, 'type', true );
		$mode       = get_post_meta( $view->ID, 'mode', true );
		$filter_css = in_array( $type, [ 'singular', 'archive', 'code' ], true ) && 'layout' === $mode;
		$source     = $view->post_content;

		if ( $view->post_excerpt && ! $filter_css ) {
			$source .= "\n<style>\n{$view->post_excerpt}\n</style>";
		}
		if ( $view->post_content_filtered ) {
			$source .= "\n<script>\n{$view->post_content_filtered}\n</script>";
		}

        $twig_source = new Source( $source , $name );
		return $twig_source->getCode();
	}

	/**
	 * Gets the cache key to use for the cache for a given template name.
	 *
	 * @param string $name The name of the template to load.
	 *
	 * @return string The cache key.
	 *
	 * @throws LoaderError When $name is not found.
	 */
	public function getCacheKey( $name ) {
		if ( ! $this->exists( $name ) ) {
			// Translators: %s - Name of the view.
			throw new LoaderError( sprintf( __( 'View "%s" is not defined.', 'mb-views' ), $name ) );
		}
		return $name;
	}

	/**
	 * Returns true if the template is still fresh.
	 *
	 * @param string $name The template name.
	 * @param int    $time The last modification time of the cached template.
	 *
	 * @return bool  true if the template is fresh, false otherwise
	 *
	 * @throws LoaderError When $name is not found.
	 */
	public function isFresh( $name, $time ) {
		$view = is_numeric( $name ) ? get_post( $name ) : get_page_by_path( $name, OBJECT, 'mb-views' );

		if ( empty( $view ) ) {
			// Translators: %s - Name of the view.
			throw new LoaderError( sprintf( __( 'View "%s" is not defined.', 'mb-views' ), $name ) );
		}

		return strtotime( $view->post_modified_date ) <= $time;
	}

	/**
	 * Check if we have the source code of a template, given its name.
	 *
	 * @param string $name The name of the template to check if we can load.
	 *
	 * @return bool    If the template source code is handled by this loader or not.
	 */
	public function exists( $name ) {
		$view = is_numeric( $name ) ? get_post( $name ) : get_page_by_path( $name, OBJECT, 'mb-views' );
		return ! empty( $view );
	}

    public function getSource( $name ) {
        return $this->getSourceContext( $name );
    }
}