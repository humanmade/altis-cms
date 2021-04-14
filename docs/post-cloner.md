# Post Cloner

This feature adds a button to the post edit screen that allows a user to clone a post with its metadata and terms to a new, identical post.

It is enabled by default but can be disabled via the configuration file:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"cloner": false
				}
			}
		}
	}
}
```

## Actions

**`post_cloner_after_meta_data_copy: (int) $post_id`**

Fires when post meta has been copied to cloned post.

## Filters

**`post_cloner_cloned_parent: (int) $parent_id`**

Allows overriding the parent that the new post should have. Defaults to the original post parent ID.

**`post_cloner_cloned_status: (string) $status`**

Allows overriding the status that the new post should have. Defaults to `draft`.

**`post_cloner_name_append: (string) $suffix`**

Allows overriding the string to append onto the end of post name to prevent collisions. Defaults to `-cloned`.

**`post_cloner_duplicate_post: (array) $post`**

Allows further modification of the duplicated post object data (as an array) before the cloned post is created.

**`post_cloner_meta_keys_to_remove: (array) $keys_to_remove`**

Allows modifying the denylist of keys of metadata that should not be passed through to the new post. Defaults to `[ '_edit_lock, '_edit_last' ]`.

**`post_cloner_meta_patterns_to_remove: (array) $patterns`**

Allows cleaning meta keys that match a regular expression. For example, to remove all post meta related to Apple News, use the following regular expression: `/^apple_news/`. Defaults to an empty array.

**`post_cloner_meta_data: (array) $post_meta`**

Allows overriding the post meta data assigned to the new post. Defaults to an associative array like the following:

```php
[
	'post_cloned' => true,
	'post_cloned_from' => $original_post_id,
]
```

**`post_cloner_post_keys_to_remove: (array) $keys_to_remove`**

Allows overriding the keys of post object data that should not be passed through to the new post. Defaults to `[ 'ID', 'guid', 'post_date_gmt', 'post_modified', 'post_modified_gmt' ]`.

**`post_cloner_override_single_post: (bool) $clonable, (int) $post_id`**

Allows overriding the clonable status of a single post if desired. Default value is `true` if the post type is in the clonable post types list, otherwise it is `false`.

**`post_cloner_clonable_post_types: (array) $post_types`**

Allows overriding the allowlist of post types that are eligible for cloning. Defaults to `[ 'post' ]`

**`post_cloner_clonable_statuses: (array) $statuses`**

Allows overriding the allowlist of post statuses that are eligible for cloning. Defaults to `[ 'publish', 'draft', 'pending' ]`.

**`post_cloner_permission_level: (string) $permission_level`**

Allows overriding the minimum capability that a user must have to clone a post. Defaults to `publish_posts`.
