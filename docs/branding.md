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
