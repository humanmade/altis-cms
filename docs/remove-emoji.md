# Remove Emoji Image Replacement

Remove all the emoji scripts that are included with WordPress. By default, this module is active. To allow emojis, toggle the `remove-emoji` module in the Altis config.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"remove-emoji": false
				}
			}
		}
	}
}
```

## About emoji
Emoji are the ideograms or smileys 😃 used in electronic messages and Web pages. Originating in Japan on mobile devices, they are now commonly available on devices worldwide, ranging from mobile to desktop computers.

Different operating systems have distinct methods of accessing emoji. Note that these methods work in most applications, not just WordPress.

## Why remove emoji?
Besides not wanting emoji to automatically be automatically converted from text that is equivalent to smileys (see [Using Smilies](https://wordpress.org/support/article/using-smilies/)), leaving emoji active loads JavaScript files (`wp-emoji.js` and `twemoji.js`) in the background which may not be desired for all sites. For this reason, Remove Emoji is active by default.

For more information, see [Emoji](https://wordpress.org/support/article/emoji/).
