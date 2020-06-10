## Helper Functions

### `clean_html( string $text, array $allowedtags = [], string $context = '' ) : string`

When translating strings in WordPress, the most common functions to use are `__()` (translate and return) or `_e()` (translate and output). Where possible, these need to be escaped too, to ensure that translations don't accidentally break your output. For this reason, `esc_html_e()`, `esc_attr_e()`, etc are offered by WordPress as convenience functions.

However, this falls down when you need to have HTML in the translation.

This function provides a nice, easy, performant way to perform sanitization on translated strings. Rather than requiring you to work with the internals of kses, it's much closer to functions like `esc_html()`.

#### Parameters

**`$text`**

_(string)(required)_ Content to escape

**`$allowedtags`**

_(array)_ Allowed tags, see examples.

**`$context`**

_(string)_ kses context to use (see [wp_kses_allowed_html](http://developer.wordpress.org/reference/functions/wp_kses_allowed_html/))

#### Return
_(string)_ Escaped string for output into HTML context.

#### Example usage

For the most part, `clean_html()` can be used in exactly the same way developers are used to using other escaping functions.

To allow `a` tags only in a translated string:

```php
$text = clean_html(
	sprintf(
		__( 'This is some text <a href="%1$s">with a link</a>'),
		'http://example.com/'
	),
	'a'
);
```
It works with multiple elements as well, using a comma-separated string or list of elements:

```php
$text = clean_html(
	sprintf(
		__( 'This is <code>some</code> text <a href="%1$s">with a link</a>'),
		'http://example.com/'
	),
	'a, code' // or [ 'a', 'code' ]
);
```
If you need custom attributes, you can use kses-style attribute specifiers.
These can be mixed too:

```php
$text = clean_html(
	sprintf(
		__( 'This is <span class="x">some</span> text <a href="%1$s">with a link</a>'),
		'http://example.com/'
	),
	[
		'a',
		'span' => [
			'class' => true,
		],
	]
);
```

### `print_clean_html( string $text, array $allowedtags = [], string $context = '' ) : void`

Equivalent of `clean_html()` which echoes output directly rather than returning.

#### Parameters

**`$text`**

_(string)(required)_ Content to escape

**`$allowedtags`**

_(array)_ Allowed tags, see examples under `clean_html`.

**`$context`**

_(string)_ kses context to use (see [wp_kses_allowed_html](http://developer.wordpress.org/reference/functions/wp_kses_allowed_html/))

#### Return
_(void)_ Escaped string is echoed to the browser directly.

### `wp_hash_password( string $password )`

Hash password using bcrypt. This function calls `password_hash` instead of WP's default password hasher.

The `wp_hash_password_options` filter is available to set the [options](http://php.net/manual/en/function.password-hash.php) that `password_hash` can accept.

#### Parameters

**`$password`**

_(string)(required)_ Plaintext password

#### Return
_(bool|string)_

### `wp_check_password( string $password, string $hash, int|string $userId )`

Check if user has entered correct password, supports bcrypt and pHash.

At its core, this function just calls `password_verify` instead of the default.
However, it also checks if a user's password was *previously* hashed with the old MD5-based hasher and re-hashes it with bcrypt. This means you can still install this plugin on an existing site and everything will work seamlessly.

The `check_password` filter is available just like the default WP function.

#### Parameters

**`$password`**

_(string)(required)_ Plaintext password

**`$hash`**

_(string)(required)_ Hash of password

**`$userId`**

_(int|string)_ ID of user to whom the password belongs

#### Return
_(mixed|void)_

### `wp_set_password( string $password, int $userId )`

Set password using bcrypt.

This function is included here verbatim but with the addition of returning the hash. The default WP function does not return anything which means you end up hashing it twice for no reason.

#### Parameters

**`$password`**
_(string)(required)_ Plaintext password

**`$userId`**
_(int)(required)_ ID of user to whom password belongs

#### Return
_(bool|string)_