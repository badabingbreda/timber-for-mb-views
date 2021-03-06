# Timber for MB Views
Use Timber to render the View, instead of the default environment. 

The Timber environment comes with the complete Timber Context, so you can use:

    post
    site
    body_class
    request
    theme

It has been extended with

    posts
    query.vars
    query.found_posts
    mb

Timber comes with the useful TimberImage(), Post() and many other WP specific functionality.

The `mb` Proxy Class has been added for your convenience.

---
You can use the following filters to add custom template paths to your setup:

### `mbv_fs_paths`

Add additional paths to the filesystem loader

    add_filter( 'mbv_fs_paths' , function( $paths ) { 
        return array_merge( $paths, [  FL_CHILD_THEME_DIR . '/views'  ] ); 
    } ) ;

### `mbv_fs_loader`

Can be used to add namespaced paths.

    add_filter( 'mbv_fs_loader' , function( $fs_loader ) { 
        $fs_loader->addPath( FL_CHILD_THEME_DIR . '/views' , 'views'  );
        $fs_loader->addPath( FL_CHILD_THEME_DIR . '/views/metabox/components' , 'mbc'  );
        return $fs_loader; 
    } );

changelog
|version|date|changes|
|---|---|---|
|1.2.1|feb 22th 2022|Added checks for Timber being active, in case temporarily disabled, twig compiled using MB Twig version|
|1.2.0|feb 22th 2022|Added CSS to hide "Insert Field" button|
|1.1.0|feb 18th 2022|Added support for mb-view post import,include, block etcetera by adding twig_chain_loader to Timber|