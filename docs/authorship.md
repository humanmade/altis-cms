# Authorship

Authorship is a modern approach to author attribution in WordPress. It supports attributing posts to multiple authors and to guest authors, provides a great UI, and treats API access to author data as a first-class citizen.

By default this feature is not enabled. You can switch on using the following configuration:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"authorship": true
				}
			}
		}
	}
}
```

## Features

* Multiple authors per post
* Guest authors (that can be created in place on the post editing screen)
* A convenient and user-friendly UI that feels like a part of WordPress
* Works with the block editor
* Full CRUD support in the REST API and WP-CLI
* Full support in RSS feeds
* Fine-grained user permission controls

## Template Functions

The following template functions are available for use in your theme to fetch the attributed author(s) of a post:

* `\Authorship\get_author_names( $post )`
  - Returns a comma-separated list of the names of the attributed author(s)
* `\Authorship\get_author_names_sentence( $post )`
  - Returns a sentence stating the names of the attributed author(s), localised to the current language
* `\Authorship\get_author_names_list( $post )`
  - Returns an unordered HTML list of the names of the attributed author(s)
* `\Authorship\get_authors( $post )`
  - Returns a list of user objects of the attributed authors
* `\Authorship\get_author_ids( $post )`
  - Returns a list of user ids of the attributed authors

## REST API

The following REST API endpoints and fields are available:

### `authorship/v1/users` endpoint

This endpoint allows:

* Searching all users who can be attributed to content
* Creating guest authors

### `authorship` field

This field is added to the endpoint for all post types that have post type support for `author`, for example `wp/v2/posts`. This field is readable and writable and accepts and provides an array of IDs of users attributed to the post.

## WP-CLI

The following WP-CLI flags are available:

### `--authorship` flag

When creating or updating posts the `--authorship` flag can be used to specify the IDs of users attributed to the post. The flag accepts a comma-separated list of user IDs. Examples:

* `wp post create --post_title="My New Post" --authorship=4,11`
* `wp post update 220 --authorship=13`

If this flag is *not* set:

* When creating a new post, if the `--post_author` flag is set then it will be used for attributed authors
* When updating an existing post, no change will be made to attributed authors

## Email Notifications

Authorship does not send any email notifications itself, but it does instruct WordPress core to additionally send its emails to attributed authors when appropriate.

* When a comment on a post is held for moderation, the comment moderation email also gets sent to all attributed authors who have the ability to moderate the comment and have a valid email address
* When a comment on a post is published, the comment notification email also gets sent to all attributed authors who have a valid email address

This plugin only adjusts the list of email addresses to which these emails get sent. If you want to disable these emails entirely, see the "Email me whenever" section of the Settings -> Discussion screen in WordPress.

## Security, Privileges, and Privacy

Great care has been taken to ensure this feature makes no changes to the user capabilities required to edit content or view sensitive user data on your site. What it *does* do is:

* Grant users who are attributed to a post the ability to edit that post if their capabilities allow it
* Grant users the ability to create and assign guest authors to a post
* Allow this behaviour to be changed at a granular level with custom capabilities

### Assigning Attribution

The capability required to change the attribution of a post matches that which is required by WordPress core to change the post author. This means a user needs the `edit_others_post` capability for the post type. The result is no change in behaviour from WordPress core with regard to being able to attribute a post to another user.

* Administrators and Editors can change the attributed authors of a post
* Authors and Contributors cannot change the attributed authors, and instead see a read-only list when editing a post

Authorship allows the attribution to be changed for any post type that has post type support for `author`, which by default is Posts and Pages.

### Editing Posts

When a user is attributed to a post, that user becomes able to manage that post according to their capabilities as if they were the post author. This means:

* A post that is attributed to a user with a role of Author can be edited, published, and deleted by that user
* A post that is attributed to a user with a role of Contributor can be edited by that user while in draft, but cannot be not published, and cannot be edited once published

From a practical point of view this feature only affects users with a role of Author or Contributor. Administrators and Editors can edit other users' posts by default and therefore edit, publish, and delete posts regardless of whether they are attributed to it.

### Searching Users

The `authorship/v1/users` REST API endpoint provides a means of searching users on the site in order to attribute them to a post. Access to this endpoint is granted to all users who have the capability to change the attributed authors of the given post type, which means Editors and Administrators by default. The result is no change in behaviour from WordPress core with regard to being able to search users.

In addition, this endpoint has been designed to expose minimal information about users, for example it does not expose email addresses or capabilities. This allows lower level users such as users with a role of Author to be granted the ability to attribute users to a post without unnecessarily exposing sensitive information about other users.

### Creating Guest Authors

The `authorship/v1/users` REST API endpoint provides a means of creating guest authors that can subsequently be attributed to a post. Access to this endpoint is granted to all users who have the ability to edit others' posts, which means Editors and Administrators by default.

More work is still to be done around the ability to subsequently edit guest authors, but it's worth noting that this is the one area where Authorship diverges from the default capabilities of WordPress core. It allows an Editor role user to create a new user account, which they usually cannot do. However it is tightly controlled:

* An email address cannot be provided unless the user has the `create_users` capability, which only Administrators do
* A user role cannot be provided, it is always set to Guest Author

### Capability Customisation

The following custom user capabilities are used by Authorship. These can be granted to or denied from users or roles in order to adjust user access:

* `attribute_post_type`
   - Used when attributing users to a given post type
   - Maps to the `edit_others_posts` capability of the post type by default
* `create_guest_authors`
   - Used when creating a guest author
   - Maps to `edit_others_posts` by default
