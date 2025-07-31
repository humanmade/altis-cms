# RSS Feeds

RSS Feeds ("Rich Site Summary" or "Really Simple Syndication"), Atom or RDF are type of web feeds that provides users and
applications with regular updates from a given website. The RSS feed is a structured XML code document.

By default the RSS Feeds are enabled.

For more information on RSS feeds, please see
the [WordPress RSS documentation](https://wordpress.org/support/article/wordpress-feeds/).

## Disabling RSS Feeds
RSS feeds can be disabled by setting the `feeds` configuration property to `false`

```json
{
    "extra": {
        "altis": {
            "modules": {
                "cms": {
                    "feeds": false
                }
            }
        }
    }
}
```
