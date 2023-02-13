# XML RPC

XML RPC (Remote Procedure Call) is a type of API which uses XML to encode its calls and HTTP as a transport mechanism.

It is used to enable remote management of applications, for example via mobile or desktop apps.

By default XML RPC is enabled. If you wish to disable the processing of requests, set the `xmlrpc` configuration property to `false`

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

This change won't stop traffic from making requests to XML-RPC, it only disables the functionality in WordPress.

To block the traffic from reaching the application completely, you can use [custom nginx rules](docs://cloud/nginx-configuration/) in `nginx-additions.conf` to block all access:

```
# Block XMLRPC
location ~* xmlrpc.php {
    deny all;
    return 404;
}
```
