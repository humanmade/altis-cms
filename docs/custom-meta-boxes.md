# Custom Meta Boxes

It's common practice to need to extend the CMS admin in a variety of ways, especially when creating custom post types or adding new admin pages.

HM Platform provides the [CMB2](https://github.com/cmb2/cmb2) framework to facilitate the rapid development of this functionality without reinventing the wheel.

The framework allows you to:

- Add custom meta boxes for post types
- Add custom meta boxes for taxonomy terms
- Add custom options pages
- Add custom user profile fields
- Add custom forms on the front end
- Register custom data for the REST API
- Create new custom field types

There is a [large eco-system of custom field types](https://github.com/CMB2/CMB2#custom-field-types) available as well.

## Creating a basic post metabox

At it's most basic adding a custom meta field starts on the `cmb2_init` or `cmb2_admin_init` hook. The following example adds a field for an alternative short headline to posts.

```php
add_action( 'cmb2_init', function () {

	// Create the meta box.
	$meta_box = new_cmb2_box( [
		'id' => 'editorial-options',
		'title' => esc_html__( 'Editorial options' ),
		'object_types'  => [ 'post' ], // Post types
		'show_in_rest' => WP_REST_Server::ALLMETHODS,
	] );

	// Add a text field to the meta box.
	$meta_box->add_field( [
		'id' => 'short-title',
		'name' => esc_html__( 'Short headline' ),
		'desc' => esc_html__( 'Alternative short headline for use on article listings and social media' ),
		'type' => 'text',
	] );

} );
```

The metadata can then be retrieved using `get_post_meta()`.

```php
$short_title = get_post_meta( get_the_ID(), 'short-title', true );
```

You can [find complete documentation for CMB2 here](https://cmb2.io/) and [full code examples for creating each type of form in the `example-functions.php` file of the CMB2 plugin](https://github.com/CMB2/CMB2/blob/develop/example-functions.php).

## Disabling the framework

You can toggle loading of the framework via the config if you need to by setting the `custom-meta-boxes` property to true or false.

```json
{
	"extra": {
		"platform": {
			"modules": {
				"cms": {
					"custom-meta-boxes": false
				}
			}
		}
	}
}
```
