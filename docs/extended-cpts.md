# Extended Custom Post Types

For the full documentation, refer to the [Extended CPTs wiki](https://github.com/johnbillion/extended-cpts/wiki).

## Registering Post Types

The `register_extended_post_type()` function is the core of Extended CPTs. It's a wrapper for WordPress' own `register_post_type()` function, which means that any parameters which are accepted by `register_post_type()` are accepted.

The function's signature looks like this:

```php
register_extended_post_type( string $post_type, array $args = [], array $names = [] ) : Extended_CPT
```

Need a simple post type with no frills? You can register a post type with a single parameter:

```php
add_action( 'init', function() {

	register_extended_post_type( 'article' );

} );
```

Try it. You'll have a hierarchical public post type with an admin UI, and all the labels and post updated messages will be automatically generated.

Arguments go into the second parameter, and the third parameter is used to override the singular, plural, and slug arguments which are used as the basis for the post type labels, post updated messages, and permalinks.

```php
add_action( 'init', function() {

	register_extended_post_type( 'story', [
		# Add the post type to the site's main RSS feed:
		'show_in_feed' => true,

		# Show all posts on the post type archive:
		'archive' => [
			'nopaging' => true,
		],

		# Add some custom columns to the admin screen:
		'admin_cols' => [
			'featured_image' => [
				'title'          => 'Illustration',
				'featured_image' => 'thumbnail',
			],
			'published' => [
				'title'       => 'Published',
				'meta_key'    => 'published_date',
				'date_format' => 'd/m/Y',
			],
			'genre' => [
				'taxonomy' => 'genre',
			]
		],

		# Add a dropdown filter to the admin screen:
		'admin_filters' => [
			'genre' => [
				'taxonomy' => 'genre',
			]
		]
	],
	[
		# Override the base names used for labels:
		'singular' => 'Story',
		'plural'   => 'Stories',
		'slug'     => 'stories',
	] );

} );
```

And that easily, we have a 'Stories' post type, with correctly generated labels and post updated messages, three custom columns in the admin area (two of which are sortable), stories added to the main RSS feed, and all stories displayed on the post type archive.

## Registering Taxonomies

The `register_extended_taxonomy()` is a wrapper for WordPress' own `register_taxonomy()` function, which means that any parameters accepted by `register_taxonomy()` are accepted.

The function's signature looks like this:

```php
register_extended_taxonomy( string $taxonomy, $object_type, array $args = [], array $names = [] ) : Extended_Taxonomy
```

Need a simple taxonomy with no frills? You can register a taxonomy with two parameters:

```php
register_extended_taxonomy( 'location', 'post' );
```

Try it. You'll have a hierarchical public taxonomy with an admin UI, and all the labels and term updated messages will be automatically generated. Or for a bit more functionality:

```php
register_extended_taxonomy( 'story', 'post', [

	# Use radio buttons in the meta box for this taxonomy on the post editing screen:
	'meta_box' => 'radio',

	# Show this taxonomy in the 'At a Glance' dashboard widget:
	'dashboard_glance' => true,

	# Add a custom column to the admin screen:
	'admin_cols' => [
		'updated' => [
			'title'       => 'Updated',
			'meta_key'    => 'updated_date',
			'date_format' => 'd/m/Y',
		],
	],

], [
	# Override the base names used for labels:
	'singular' => 'Story',
	'plural'   => 'Stories',
	'slug'     => 'tales',

] );
```

And just like that, we have a 'Stories' taxonomy attached to the Post post type, with correctly generated labels and term updated messages, radio buttons in place of the standard meta box for this taxonomy on the post editing screen, a custom column in the admin area (you need to handle the term meta population yourself), and a count of the terms in this taxonomy in the 'At a Glance' dashboard widget.

### Default Arguments For Custom Taxonomies.
Several of these differ from the defaults in WordPress' register_taxonomy() function.
```php
'public'            => true,  
'show_ui'           => true,  
'hierarchical'      => true,  
'query_var'         => true,  
'exclusive'         => false, # Custom arg  // true means: just one can be selected  
'allow_hierarchy'   => false, # Custom arg  //  
'meta_box'          => null,  # Custom arg  // can be null, 'simple', 'radio', 'dropdown' -> 'radio' and 'dropdown' just allow exclusive choices (will overwrite the set choise), simple has exclusive and multi options  
'dashboard_glance'  => false, # Custom arg  // show or not on dashboard glance  
'checked_ontop'     => null,  # Custom arg  //   
'admin_cols'        => null,  # Custom arg  // added admin columns  
'required'          => false, # Custom arg  // 
```

## Admin Columns

The `admin_cols` argument allows you to declare various table columns for the post type listing screen without having to deal with WordPress' long-winded actions and filters for list table columns.

Extended CPTs provides built-in columns which display post meta fields, taxonomy terms, featured images, post fields, Posts 2 Posts connections, and custom callback functions. Column sorting is handled where appropriate (for post meta, taxonomy terms, and post fields), and output is escaped for safety.

### Example

```php
register_extended_post_type( 'article', [

	'admin_cols' => [
		// A featured image column:
		'featured_image' => [
			'title'          => 'Illustration',
			'featured_image' => 'thumbnail',
		],
		// The default Title column:
		'title',
		// A meta field column:
		'published' => [
			'title'       => 'Published',
			'meta_key'    => 'published_date',
			'date_format' => 'd/m/Y',
		],
		// A taxonomy terms column:
		'genre' => [
			'taxonomy' => 'genre',
		],
	],

] );
```

For more information regarding admin columns and the availabile column types, please see the Extended CPTs wiki page on [admin columns](https://github.com/johnbillion/extended-cpts/wiki/Admin-columns).

## Admin Filters

Extended CPTs provides several controls that can be added to the top of the post type listing screen so your editors can filter the screen by various fields. These controls live next to the default date and category dropdowns which WordPress provides.

Admin filters are specified with the `admin_filters` parameter.
### Example

```php
register_extended_post_type( 'article', [

	'admin_filters' => [
		'foo' => [
			'title'    => 'Foo',
			'meta_key' => 'foo',
		],
		'bar' => [
			'title'           => 'Bar',
			'meta_search_key' => 'bar',
		],
		'genre' => [
			'title'    => 'Genre',
			'taxonomy' => 'genre',
		],
	],

] );
```

For more information regarding admin filters and the availabile column types, please see the Extended CPTs wiki page on [admin filters](https://github.com/johnbillion/extended-cpts/wiki/Admin-filters).

## Custom Permalink Structure

Custom post types registered with WordPress' `register_post_type()` function don't allow for a custom permalink structure, unless you want to dive into the Rewrite API and pull your hair out in the process.

Extended CPTs allows a custom permalink structure to be specified via the `permastruct` parameter in the `rewrite` argument.

### Examples

```php
register_extended_post_type( 'article', [
	'rewrite' => [
		'permastruct' => '/foo/%custom_tax%/%article%',
	],
] );
```

```php
register_extended_post_type( 'article', [
	'rewrite' => [
		'permastruct' => '/articles/%year%/%monthnum%/%article%',
	],
] );
```

All of [WordPress' built-in rewrite tags](https://codex.wordpress.org/Using_Permalinks#Structure_Tags) are supported, including dates and custom taxonomies.


## Query Vars For Filtering

Extended CPTs provides a mechanism for registering public query vars which allow users to filter your post type archives by various fields. This also works in `WP_Query`, although the main advantage is the fact these are _public_ query vars accessible via URL parameters.

Think of these as the front-end equivalent of list table filters in the admin area, minus the UI.

The array keys in the `site_filters` array are used as the names of the query vars, which is `my_foo` and `my_genre` in the example below. Be careful with query var name clashes!

### Example

```php
register_extended_post_type( 'article', [
	'site_filters' => [
		'my_foo' => [
			'meta_key' => 'foo',
		],
		'my_genre' => [
			'taxonomy' => 'genre',
		],
	],
] );
```

This allows your post type archive to be filtered thusly:

`example.com/articles/?my_foo=bar`

It also allows you to filter posts in `WP_Query` thusly:

```php
new WP_Query( [
	'post_type' => 'article',
	'my_foo'    => 'bar',
] );
```

For more information regarding query vars for filtering and the availabile filter types, please see the Extended CPTs wiki page on [query vars for filtering](https://github.com/johnbillion/extended-cpts/wiki/Query-vars-for-filtering).

## Query Vars For Sorting

Extended CPTs provides a mechanism for registering values for the public `orderby` query var, which allows users to sort your post type archives by various fields. This also works in `WP_Query`, which makes ordering custom post type listings very powerful and dead easy.

Think of these as the front-end equivalent of sortable columns in the admin area, minus the UI.

The array keys in the `site_sortables` array are used for the `orderby` value, which is `my_foo` and `my_genre` in the example below.

### Example

```php
register_extended_post_type( 'article', [
	'site_sortables' => [
		'my_foo' => [
			'meta_key' => 'foo',
		],
		'my_genre' => [
			'taxonomy' => 'genre',
		],
	],
] );
```

This allows your post type archive to be ordered thusly:

`example.com/articles/?orderby=my_foo&order=asc`

It also allows you to order posts in `WP_Query` thusly:

```php
new WP_Query( [
	'post_type' => 'article',
	'orderby'   => 'my_genre',
	'order'     => 'DESC',
] );
```


For more information regarding query vars for sorting and the availabile sort fields, please see the Extended CPTs wiki page on [query vars for sorting](https://github.com/johnbillion/extended-cpts/wiki/Query-vars-for-sorting).

## Available Filters and Actions

For more information, please see the Extended CPTs wiki page on [available filters and actions](https://github.com/johnbillion/extended-cpts/wiki/Available-filters-and-actions).
