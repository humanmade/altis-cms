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

**`post_cloner_cloned_parent: (int) $parent - defaults to original post ID`**

Allows overriding the parent that the new post should have.

**`post_cloner_cloned_status: (string) $status - defaults to 'draft'`**

Allows overriding the status that the new post should have.

**`post_cloner_name_append: (string) $suffix - defaults to '-cloned'`**

Allows overriding the string to append onto the end of post name to prevent collisions.

**`post_cloner_meta_keys_to_remove: (array) $keys_to_remove - defaults to ['_edit_lock, '_edit_last']`**

Allows modifying the denylist of keys of metadata that should not be passed through to the new post.

**`post_cloner_meta_patterns_to_remove: (array) $patterns - default is empty array`**

Allows cleaning meta keys that match a regular expression. For example, to remove all post meta related to Apple News,
use the following regular expression: `/^apple_news/`

**`post_cloner_meta_data: (array) $post_meta - defaults to assoc array with keys 'post_cloned' and 'post_cloned_from'`**

Allows overriding the post meta data assigned to the new post.

**`post_cloner_post_keys_to_remove: (array) $keys_to_remove - defaults to ['ID','guid','post_date_gmt','post_modified','post_modified_gmt']`**

Allows overriding the keys of post object data that should not be passed through to the new post.

**`post_cloner_override_single_post: (bool) $clonable, (int)  $post_id`**

Allows overriding the clonable status of a single post if desired.

**`post_cloner_clonable_post_types: (array) $types - defaults to ['post']`**

Allows overriding the allowlist of post types that are eligible for cloning.

**`post_cloner_clonable_statuses: (array) $statuses - defaults to [ 'publish', 'draft', 'pending' ]`**

Allows overriding the allowlist of post statuses that are eligible for cloning.

**`post_cloner_permission_level: (string) $permission_level - defaults to 'publish_posts'`**

Allows overriding the minimum capability that a user must have to clone a post.
