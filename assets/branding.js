/* global wp, altisPostPreview */

wp.hooks.addFilter( 'editor.PostPreview.interstitialMarkup', 'altis/branding', function () {
	return altisPostPreview.markup;
} );
