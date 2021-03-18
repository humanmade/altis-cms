# Block Editor

The block editor is the core editing experience of the CMS and provides a true WYSIWYG experience.

## Reusable Blocks

WordPress's built in Reusable Blocks feature has been supercharged in Altis with the following goals:

* Provide a much more seamless implementation of reusable blocks into enterprise-level setups and workflows.
* Provide an improved user interface that allows for better block discovery, including search and filtering.

Within the block editor it's possible to reuse blocks of content across multiple posts or pages. In Altis these blocks can be more easily be created and managed from a single location and any updates will be reflected everywhere the block is used.

You can toggle these enhancements on or off in your `composer.json` file by setting the `reusable-blocks` property for the CMS module to `true` or `false`.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"reusable-blocks": false
				}
			}
		}
	}
}
```

### Features

Altis Reusable Blocks includes features and improvements both for the creation and the discovery/usage of reusable blocks.

#### Relationship and usage tracking

Keep track of all usages of reusable blocks within your posts. Within the edit screen for your reusable blocks, you will find the Relationships sidebar with a paginated view of all the posts that are using the reusable block that you are currently editing.

On the reusable blocks post list table, you can see at a quick glance the usage count for that reusable block.

#### Admin Bar and Menu

By default, reusable blocks are somewhat hidden and can only be accessed from a submenu item in the block editor.
With Altis Reusable Blocks, however, reusable blocks are upgraded to first-party citizens in the admin area.

As with every other content type, the admin menu on the left contains a dedicated submenu for reusable blocks, offering shortcuts to see all existing reusable blocks, to create a new reusable block, and to see and manage categories, as well as any other publicly available taxonomy registered for reusable blocks.
Also, the admin bar at the top now contains a shortcut to create a new reusable block, just like it is possible to do for posts, media, pages or users.

#### Categories

Just like posts or pages, reusable blocks can have one or more categories assigned to them.
This helps in discovering relevant blocks by making use of the dedicated Category filter included in the block picker.

#### Filtering

When looking for an existing reusable block to insert into a post, the new block picker allows to search/filter based on a category.

By default, the Category filter is set to the (main) category of the current post.
However, this can be changed, without affecting the post's categories.

#### Search

In addition to the Category filter, the block picker also provides a search field.
The search query is used to find reusable blocks with either a matching title or content, or both.
Search results are sorted based on a smart algorithm using different weights for title matches vs. content matches, and exact matches vs. partial matches.
As a result, more relevant blocks are displayed first.

The search input also supports numeric ID lookups.
By entering a block ID, the result set will be just that one according block, ready to be inserted.
If the provided ID is a post ID, the results will be all reusable blocks referenced by that post, if any.

### PHP Filters

#### `altis_post_types_with_reusable_blocks`

This filter allows the user to manipulate the post types that can use reusable blocks and should have the relationship for the shadow taxonomy.

**Arguments:**

* `$post_types` (`string[]`): List of post type slugs.

**Example:**

```php
// Add the "page" post type.
add_filter( 'altis_post_types_with_reusable_blocks', function ( aray $post_types ): array {

	$post_types[] = 'page';

	return $post_types;
} );
```

#### `rest_get_relationship_item_additional_fields_schema`

This filter allows the user to modify the schema for the relationship data before it is returned from the REST API.

**Arguments:**

* `$schema` (`array`): Item schema data.

**Usage Example:**

```php
// Add the post author to the schema.
add_filter( 'rest_get_relationship_item_additional_fields_schema', function ( array $additional_fields ): array {

	$additional_fields['author'] = [
		'description' => __( 'User ID for the author of the post.' ),
		'type'        => 'integer',
		'context'     => [ 'view' ],
		'readonly'    => true,
	];

	return $additional_fields;
} );
```

#### `rest_prepare_relationships_response`

This filter allows the user to modify the relationship data right before it is returned from the REST API.

**Arguments:**

* `$response` (`WP_REST_Response`): Response object.
* `$post` (`WP_Post`): Post object.
* `$request` (`WP_REST_Request`): Request object.

**Usage Example:**

```php
// Add the post author to the REST response.
add_filter( 'rest_prepare_relationships_response', function ( WP_REST_Response $response, WP_Post $post ): WP_REST_Response {

	$response->data['author'] = $post->post_author;

	return $response;
}, 10, 2 );
```
