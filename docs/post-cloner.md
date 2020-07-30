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

**`post_cloner_cloned_parent: (int) $parent Parent - defaults to original post ID.`**

Allows overriding the parent that the new post should have.

**`post_cloner_cloned_status: (string) $status Status - defaults to draft.`**

Allows overriding the status that the new post should have.

**`post_cloner_name_append: (string) $suffix String to append to the end of a cloned post's name. - defaults to '-cloned'`**

Allows overriding the string to append onto the end of post name to prevent collisions.

**`post_cloner_meta_keys_to_remove: (array) $keys_to_remove Metadata keys.`**

Allows modifying the denylist of keys of metadata that should not be passed through to the new post.

**`post_cloner_meta_patterns_to_remove: (array) $patterns Regex patterns to look for when cleaning meta keys.`**

Allows overriding the regex patterns to look for when cleaning meta keys.

**`post_cloner_meta_data: (array) $post_meta All post meta to assign to the new post.`**

Allows overriding the post meta data assigned to the new post.

**`post_cloner_post_keys_to_remove: (array) $keys_to_remove WP_Post array keys.`**

Allows overriding the keys of post object data that should not be passed through to the new post.

**`post_cloner_override_single_post: (bool) $clonable, (int)  $post_id ID of post that we're checking. `**

Allows overriding the clonable status of a single post if desired.

**`post_cloner_clonable_post_types: (array) $types Post types that are eligible for cloning. - defaults to ['post']`**

Allows overriding the allowlist of post types that are eligible for cloning.

**`post_cloner_clonable_statuses: (array) $statuses Post statuses that are eligible for cloning.- defaults to [ 'publish', 'draft', 'pending' ]`**

Allows overriding the allowlist of post statuses that are eligible for cloning.

**`post_cloner_permission_level: (string) $permission_level Minimum capability that a user must have to clone a post. - defaults to 'publish_posts'`**

Allows overriding the minimum capability that a user must have to clone a post.
