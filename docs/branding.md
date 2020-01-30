# Branding

It's a common task to apply custom branding to match the site to various parts of the CMS, especially those that are public facing.

## Login screen

You can change the logo shown on the CMS login screen using the `login-logo` configuration option to provide a project root relative path to an image file.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"login-logo": "/content/site-logo.svg"
				}
			}
		}
	}
}
```

## Custom Admin Colour Scheme

If the default colour scheme does not meet the accessibility needs of your staff you can register custom schemes to provide high contrast alternatives. To register a custom admin colour scheme add the following function on the `admin_init` action.

```php
wp_admin_css_color( string $key, string $name, string $url, array $colors = [], array $icons = [] );
```
NB: note the American spelling of `color` in the function name.
- `key`: The unique key for this theme.
- `name`: The name of the theme.
- `url`: The URL of the CSS file containing the colour scheme.
- `colors`: An array of CSS colour definition strings (hexadecimal format) which are used to give the user a feel for the theme. These colours are displayed in admin Users > Your Profile > Admin Color Scheme (if there are more than one available).
- `icons`: CSS colour definitions (hexadecimal format) used to colour any SVG icons.
  - `base`: SVG icon base colour, in hexadecimal format.
  - `focus`: SVG icon colour on focus, in hexadecimal format.
  - `current`: SVG icon colour of current admin menu link, in hexadecimal format.

Example:
```php
add_action( 'admin_init', 'add_colour_scheme' );
function add_colour_scheme() {
	wp_admin_css_color(
			'dusk',
			_x( 'Dusk', 'admin colour scheme' ),
			admin_url( "path/to/css/file/dusk.css" ),
			[ '#25282b', '#363b3f', '#69a8bb', '#e14d43' ],
			[
				'base'    => '#f1f2f3',
				'focus'   => '#fff',
				'current' => '#fff',
			]
	);
}
```
