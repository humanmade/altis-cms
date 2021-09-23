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

## Network Enabled Themes

By default all themes are network enabled, this means that every theme in your application is available to all sites on the network.

Individual themes can still be disabled for the network, and enabled for specific sites by editing a site in the network admin.

If you prefer to use the standard WordPress behaviour where themes are disabled for the network by default you can set the `network-enable-themes` config option to `false`:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"network-enable-themes": false
				}
			}
		}
	}
}
```
