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

**`post_cloner_cloned_parent: (int) $original_post['ID']`**

Allows overriding the parent that the new post should have.

**`post_cloner_cloned_status: (string) 'draft'`**

Allows overriding the status that the new post should have

**`post_cloner_name_append: (string) '-cloned'`**

Allows overriding the string to append onto the end of post name to prevent collisions

**`post_cloner_meta_keys_to_remove: (array) $keys_to_remove`**

Allows modifying the denylist of keys of metadata that should not be passed through to the new post.

**`post_cloner_meta_patterns_to_remove: (array) []`**

Allows overriding the regex patterns to look for when cleaning meta keys

**`post_cloner_meta_data: (array) $post_meta`**

Allows overriding the post meta data assigned to the new post

**`post_cloner_post_keys_to_remove: (array) $keys_to_remove`**

Allows overriding the keys of post object data that should not be passed through to the new post.

**`post_cloner_override_single_post: (bool) $clonable, (int) $post_id `**

Allows overriding the clonable status of a single post if desired

**`post_cloner_clonable_post_types: (array) [ 'post' ]`**

Allows overriding the allowlist of post types that are eligible for cloning.

**`post_cloner_clonable_statuses: (array) [ 'publish', 'draft', 'pending' ]`**

Allows overriding the allowlist of post statuses that are eligible for cloning.

**`post_cloner_permission_level: (string) 'publish_posts'`**

Allows overriding of minimum capability that a user must have to clone a post.
