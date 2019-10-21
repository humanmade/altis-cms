# XML RPC

XML RPC (Remote Procedure Call) is a type of API which uses XML to encode its calls and HTTP as a transport mechanism.

It is used to enable remote management of applications, for example via mobile or desktop apps.

By default XML RPC is enabled. If you wish to disable it, set the `xmlrpc` configuration property to `false`

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
