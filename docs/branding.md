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

## Custom admin colour scheme

To register a custom admin colour scheme add the following function on `admin_init` action.

```php
wp_admin_css_color( string $key, string $name, string $url, array $colors = [], array $icons = [] );
```

- `key`: The unique key for this theme.
- `name`: The name of the theme.
- `url`: The URL of the CSS file containing the color scheme.
- `colors`: An array of CSS color definition strings which are used to give the user a feel for the theme.
- `icons`: CSS color definitions used to color any SVG icons.
  - `base`: SVG icon base color.
  - `focus`: SVG icon color on focus.
  - `current`: SVG icon color of current admin menu link.
