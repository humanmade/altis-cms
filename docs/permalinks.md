# Permalink

Altis introduces a fix to a bug where the main site on a network is hardcoded to contain `/blog/` prefix for posts by default. The side-effect for this is that you cannot use `/blog/` prefix on the main site unless you disable the fix, which is by unhooking the filters associated with it, eg:

```php
remove_filter( 'sanitize_option_permalink_structure', '\Altis\CMS\Permalinks\remove_blog_prefix' );
remove_filter( 'option_permalink_structure', '\Altis\CMS\Permalinks\remove_blog_prefix' );
```

Note that the prefix will always be used by the main site now, regardless of what permalink structure you choose in settings, in other terms, you don't need to add the `/blog/` prefix to the custom permalink structure, or you'll have it duplicated in the URL.