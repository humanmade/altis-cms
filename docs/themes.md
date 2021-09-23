# Themes

Themes are used to control the visual style of your sites. Themes can be shared and reused across many sites in a project, and you can also create "child" themes which inherit parts of another theme.

Themes are a core concept of WordPress, the underlying implementation of the CMS module. For further documentation, consult the [WordPress theme documentation](https://developer.wordpress.org/themes/).

See the [first theme guide](docs://getting-started/first-theme.md) for more information on creating new themes.


## Default theme

When a [new site](docs://guides/multiple-sites.md) is created, a "default" theme is activated. Out of the box, this is set to the `base` starter theme included with Altis, but this can be changed by setting the `default-theme` option. This should be set to the directory name of the theme.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"default-theme": "twentynineteen"
				}
			}
		}
	}
}
```
