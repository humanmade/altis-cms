## Helper Functions

**`whitelist_html( string $text, array $allowedtags = [], string $context = '' ) : string`**

When translating strings in WordPress, the most common functions to use are `__()` (translate and return) or `_e()` (translate and output). Where possible, these need to be escaped too, to ensure that translations don't accidentally break your output. For this reason, `esc_html_e()`, `esc_attr_e()`, etc are offered by WordPress as convenience functions.

However, this falls down when you need to have HTML in the translation.

This function provides a nice, easy, performant way to perform sanitization on translated strings. Rather than requiring you to work with the internals of kses, it's much closer to functions like `esc_html()`.

For the most part, `whitelist_html()` can be used in exactly the same way developers are used to using other escaping functions.

To allow `a` tags only in a translated string:

```php
$text = whitelist_html(
	sprintf(
		__( 'This is some text <a href="%1$s">with a link</a>'),
		'http://example.com/'
	),
	'a'
);
```
It works with multiple elements as well, using a comma-separated string or list of elements:

```php
$text = whitelist_html(
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
$text = whitelist_html(
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
