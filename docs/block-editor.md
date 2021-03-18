# Block Editor

The block editor is the core editing experience of the CMS and provides a true WYSIWYG experience.

## Reusable Blocks

Within the block editor it's possible to reuse blocks of content across multiple posts or pages. On Altis these blocks can be managed from a single location and any updates will be reflected in every location the block is used.

In the CMS admin you can manage, search and categorise your reusable blocks from the "Blocks" menu item.

You can toggle these additional features on or off in your `composer.json` file by setting the `reusable-blocks` property for the CMS module to `true` or `false`.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cms": {
					"reusable-blocks": false
				}
			}
		}
	}
}
```
