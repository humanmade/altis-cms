# XML RPC

WordPress XML RPC is a remote procedure call which uses XML to encode its calls and HTTP as a transport mechanism.

It is used by certain applications to enable remote management of WordPress sites, for example certain mobile apps for moderating comments, or the Jetpack plugin.

By default the XML RPC is enabled. If you need to disable it, set the `xmlrpc` configuration property to `false`

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"xmlrpc": false
				}
			}
		}
	}
}
```
