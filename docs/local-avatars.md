# Local Avatars

Altis provides a way to customize your profile picture via the [Simple Local Avatars](https://github.com/10up/simple-local-avatars) plugin. By default, a [Gravatar](https://en.gravatar.com/) will be used to fetch your profile picture. However, if you don't have an existing Gravatar, and have no desire to create one, you can use any image uploaded to the media library -- or upload a new image -- and set that as your profile picture or "avatar".

By default, local avatars is enabled and the default profile picture field on the Edit Profile page is hidden. However, this can be disabled and the default functionality restored by updating your Altis config:

```json
{
	"extra": {
		"altis": {
			"cms": {
				"local-avatars": false
			}
		}
	}
}
```

## Filters

### `simple_local_avatars_dynamic_resize`

Allows automatic rescaling to be turned off. 

**Parameters**

**`$allow_resize`** _(bool)_ Whether to allow dynamic resizing.

**Example**

```php
add_filter( 'simple_local_avatars_dynamic_resize', '__return_false' );
```

### `simple_local_avatars_upload_limit`

Allows overriding of the maximum allowable file size for avatar uploads. Defaults to the WordPress maximum default upload size.

**Parameters**

**`$bytes`** _(int)_ The maximum byte size.

**Example**

```php
add_filter( 'simple_local_avatars_upload_limit', function() {
	return 2 * 1024; // Max 2KB.
} );
```
