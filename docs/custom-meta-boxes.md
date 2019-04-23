# Custom Meta Boxes

It's common practice to need to extend the CMS admin in a variety of ways, especially when creating custom post types or adding new admin pages.

HM Platform provides the [CMB2](https://github.com/cmb2/cmb2) framework to facilitate the rapid development of this functionality without reinventing the wheel.

The framework allows you to:

- Add custom meta boxes for post types
- Add custom meta boxes for taxonomy terms
- Add custom options pages
- Add custom user profile fields
- Add custom forms on the front end
- Register custom data for the REST API
- Create new custom field types

## Field Types

1. [`title`](#title) An arbitrary title field *
1. [`text`](#text)
1. [`text_small`](#text_small)
1. [`text_medium`](#text_medium)
1. [`text_email`](#text_email)
1. [`text_url`](#text_url)
1. [`text_money`](#text_money)
1. [`textarea`](#textarea)
1. [`textarea_small`](#textarea_small)
1. [`textarea_code`](#textarea_code)
1. [`text_time`](#text_time) Time picker
1. [`select_timezone`](#select_timezone) Time zone dropdown
1. [`text_date`](#text_date) Date Picker
1. [`text_date_timestamp`](#text_date_timestamp) Date Picker (UNIX timestamp)
1. [`text_datetime_timestamp`](#text_datetime_timestamp) Text Date/Time Picker Combo (UNIX timestamp)
1. [`text_datetime_timestamp_timezone`](#text_datetime_timestamp_timezone) Text Date/Time Picker/Time zone Combo (serialized DateTime object)
1. [`hidden`](#hidden) Hidden input type
1. [`colorpicker`](#colorpicker) Color picker
1. [`radio`](#radio) *
1. [`radio_inline`](#radio_inline) *
1. [`taxonomy_radio`](#taxonomy_radio) * Default Category/Tag/Taxonomy metaboxes replacement.
1. [`taxonomy_radio_inline`](#taxonomy_radio_inline) * Default Category/Tag/Taxonomy metaboxes replacement.
1. [`taxonomy_radio_hierarchical`](#taxonomy_radio_hierarchical) * Default Category/Tag/Taxonomy metaboxes replacement, displayed in a hierarchical fashion (indented).
1. [`select`](#select)
1. [`taxonomy_select`](#taxonomy_select) * Default Category/Tag/Taxonomy metaboxes replacement.
1. [`checkbox`](#checkbox) *
1. [`multicheck` and `multicheck_inline`](#multicheck-and-multicheck_inline)
1. [`taxonomy_multicheck`](#taxonomy_multicheck) * Default Category/Tag/Taxonomy metaboxes replacement.
1. [`taxonomy_multicheck_inline`](#taxonomy_multicheck_inline) Default Category/Tag/Taxonomy metaboxes replacement.
1. [`taxonomy_multicheck_hierarchical`](#taxonomy_multicheck_hierarchical) Default Category/Tag/Taxonomy metaboxes replacement, displayed in a hierarchical fashion (indented).
1. [`wysiwyg`](#wysiwyg) (TinyMCE) *
1. [`file`](#file) Image/File upload *†
1. [`file_list`](#file_list) Image Gallery/File list management
1. [`oembed`](#oembed) Converts oembed urls (instagram, twitter, youtube, etc. [oEmbed in the Codex](https://codex.wordpress.org/Embeds))
1. [`group`](#group) Hybrid field that supports adding other fields as a repeatable group. *

### More Info
* [Create your own field type](https://github.com/CMB2/CMB2/wiki/Adding-your-own-field-types)
* [Common field parameters shared by all fields](https://github.com/CMB2/CMB2/wiki/Field-Parameters)

\* Not available as a repeatable field
† Use `file_list` for repeatable
<br>
<br>
<br>

### `title`
____
A large title (useful for breaking up sections of fields in metabox). Example:
![Screenshot of title CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_title.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Title',
	'desc' => 'This is a title description',
	'type' => 'title',
	'id'   => 'wiki_test_title'
) );
```
#### CSS Field Class:
`cmb-type-title`
<br>
<br>
<br>

### `text`
____
Standard text field (large). Example:
![Screenshot of text CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test Text',
	'desc'    => 'field description (optional)',
	'default' => 'standard value (optional)',
	'id'      => 'wiki_test_text',
	'type'    => 'text',
) );
```

#### CSS Field Class:
`cmb-type-text`
<br>
<br>
<br>

### `text_small`
____
Small text field. Example:
![Screenshot of text_small CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_small.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test Text Small',
	'desc'    => 'field description (optional)',
	'default' => 'standard value (optional)',
	'id'      => 'wiki_test_textsmall',
	'type'    => 'text_small'
) );
```

#### CSS Field Class:
`cmb-type-text-small`
<br>
<br>
<br>

### `text_medium`
____
Medium text field. Example:
![Screenshot of text_small CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_medium.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test Text Medium',
	'desc'    => 'field description (optional)',
	'default' => 'standard value (optional)',
	'id'      => 'wiki_test_textmedium',
	'type'    => 'text_medium'
) );
```

#### CSS Field Class:
`cmb-type-text-medium`
<br>
<br>
<br>

### `text_email`
____
Standard text field which enforces an email address. Example:
![Screenshot of text_small CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_email.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Text Email',
	'id'   => 'wiki_test_email',
	'type' => 'text_email',
) );
```

#### CSS Field Class:
`cmb-type-text-email`
<br>
<br>
<br>

### `text_url`
____
Standard text field which enforces a url. Example:
![Screenshot of text_small CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_url.jpg)

```php
$cmb->add_field( array(
	'name' => __( 'Website URL', 'cmb2' ),
	'id'   => 'wiki_test_facebookurl',
	'type' => 'text_url',
	// 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
) );
```

#### CSS Field Class:
`cmb-type-text-url`
<br>
<br>
<br>

### `text_money`
____
Standard text field with dollar sign in front of it (useful to prevent users from adding a dollar sign to input). Example:
![Screenshot of text_small CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_money.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Money',
	'desc' => 'field description (optional)',
	'id' => 'wiki_test_textmoney',
	'type' => 'text_money',
	// 'before_field' => '£', // Replaces default '$'
) );
```

#### CSS Field Class:
`cmb-type-text-money`
<br>
<br>
<br>

### `textarea`
____
Standard textarea. Example:
![Screenshot of text area CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_textarea.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Text Area',
	'desc' => 'field description (optional)',
	'default' => 'standard value (optional)',
	'id' => 'wiki_test_textarea',
	'type' => 'textarea'
) );
```

#### CSS Field Class:
`cmb-type-textarea`
<br>
<br>
<br>

### `textarea_small`
____
Smaller textarea. Example:
![Screenshot of text area small CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_textarea_small.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Text Area Small',
	'desc' => 'field description (optional)',
	'default' => 'standard value (optional)',
	'id' => 'wiki_test_textareasmall',
	'type' => 'textarea_small'
) );
```

#### CSS Field Class:
`cmb-type-textarea-small`
<br>
<br>
<br>

### `textarea_code`
____
Code textarea. Example:
![Screenshot of text area code CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_textarea_code.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Text Area Code',
	'desc' => 'field description (optional)',
	'default' => 'standard value (optional)',
	'id' => 'wiki_test_textareacode',
	'type' => 'textarea_code'
) );
```

#### Notes

As of version 2.4.0, the `'textarea_code'` field type now uses CodeMirror that is [used by WordPress](https://make.wordpress.org/core/2017/10/22/code-editing-improvements-in-wordpress-4-9/) ([#1096](https://github.com/CMB2/CMB2/issues/1096)). A field can opt-out to return to the previous behavior by specifying an `'options'` parameter:  `'options' => array( 'disable_codemirror' => true )`

As with the other javascript-enabled fields, the code-editor defaults can be overridden via a `data-codeeditor` attribute. E.g:

```php
// Make code be read-only css
$cmb->add_field( array(
	// other field config ...
	'type' => 'textarea_code'
	'attributes' => array(
		'readonly' => 'readonly',
		'data-codeeditor' => json_encode( array(
			'codemirror' => array(
				'mode' => 'css',
				'readOnly' => 'nocursor',
			),
		) ),
	),
) );
```

The arguments are then passed to the WordPress `wp.codeEditor` method. The top-level parameters to use are: `'codemirror'`, `'csslint'`, `'jshint'`, `'htmlhint'`.

#### CSS Field Class:
`cmb-type-textarea-code`
<br>
<br>
<br>

### `text_time`
____
Time picker field. Example:
![Screenshot of text_small CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_time.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Time Picker',
	'id' => 'wiki_test_texttime',
	'type' => 'text_time'
	// Override default time-picker attributes:
	// 'attributes' => array(
	// 	'data-timepicker' => json_encode( array(
	// 		'timeOnlyTitle' => __( 'Choose your Time', 'cmb2' ),
	// 		'timeFormat' => 'HH:mm',
	// 		'stepMinute' => 1, // 1 minute increments instead of the default 5
	// 	) ),
	// ),
	// 'time_format' => 'h:i:s A',
) );
```

#### CSS Field Class:
`cmb-type-text-time`

#### Custom Field Attributes:

* `time_format`, defaults to 'h:i A'. See [php.net/manual/en/function.date.php](http://php.net/manual/en/function.date.php).

#### Notes
Like the other picker fields, the default attributes sent to the javascript picker widget can be overridden using a data attribute on the field. The above example has the attributes commented out, but you can see an example for how that works.

<br>
<br>
<br>

### `select_timezone`
____
Timezone field. Example:
![Screenshot of select_timezone CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_select_timezone.jpg)

```php
$cmb->add_field( array(
	'name' => 'Time zone',
	'id'   => 'wiki_test_timezone',
	'type' => 'select_timezone',
) );
```

#### CSS Field Class:
`cmb-type-select-timezone`

<br>
<br>
<br>

### `text_date`
____
Date field. Stored and displayed according to the `date_format`. Example:
![Screenshot of text_date CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_date.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Date Picker',
	'id'   => 'wiki_test_textdate_timestamp',
	'type' => 'text_date',
	// 'timezone_meta_key' => 'wiki_test_timezone',
	// 'date_format' => 'l jS \of F Y',
) );
```

#### CSS Field Class:
`cmb-type-text-date`

<a name="datepicker-additional-field-options"></a>
#### Additional Field Options:

The CMB2 date-pickers use the [jQuery UI Datepicker](https://jqueryui.com/datepicker/).

All of the default options in the [jQuery UI Datepicker API Documentation](http://api.jqueryui.com/datepicker/) are configurable within the CMB2 datepicker fields.

##### jQuery UI Option Example Usage:

```php
$cmb_demo->add_field( array(
	'name' => __( 'Test Date Picker', 'cmb2' ),
	'desc' => __( 'field description (optional)', 'cmb2' ),
	'id'   => '_yourprefix_demo_textdate',
	'type' => 'text_date',
	'attributes' => array(
		// CMB2 checks for datepicker override data here:
		'data-datepicker' => json_encode( array(
			// dayNames: http://api.jqueryui.com/datepicker/#option-dayNames
			'dayNames' => array( 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi' ),
			// monthNamesShort: http://api.jqueryui.com/datepicker/#option-monthNamesShort
			'monthNamesShort' => explode( ',', 'En,Feb,Mar,Abr,May,Jun,Jul,Ago,Sep,Oct,Nov,Dic' ),
			// yearRange: http://api.jqueryui.com/datepicker/#option-yearRange
			// and http://stackoverflow.com/a/13865256/1883421
			'yearRange' => '-100:+0',
			// Get 1990 through 10 years from now.
			// 'yearRange' => '1990:'. ( date( 'Y' ) + 10 ),
		) ),
	),
) );
```
<br>
<br>
<br>

### `text_date_timestamp`
____
Date field, stored as UNIX timestamp. Useful if you plan to query based on it (ex: [events listing](http://www.billerickson.net/code/event-query/) ). Example:
![Screenshot of text_date_timestamp CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_date_timestamp.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Date Picker (UNIX timestamp)',
	'id'   => 'wiki_test_textdate_timestamp',
	'type' => 'text_date_timestamp',
	// 'timezone_meta_key' => 'wiki_test_timezone',
	// 'date_format' => 'l jS \of F Y',
) );
```

#### CSS Field Class:
`cmb-type-text-date-timestamp`

#### Custom Field Attributes:

* `timezone_meta_key`, Optionally make this field honor the timezone selected in the [`select_timezone`](/CMB2/CMB2/wiki/Field-Types#select_timezone) field specified above.
* `date_format`, defaults to 'm/d/Y'. See [php.net/manual/en/function.date.php](http://php.net/manual/en/function.date.php). PHP's strtotime is used internally to parse the date, [this is not compatible with UK/European date formats](https://stackoverflow.com/a/4163696/1868365).
<br>
<br>
<br>

### `text_datetime_timestamp`
____
Date and time field, stored as UNIX timestamp. Example:
![Screenshot of text_datetime_timestamp CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_datetime_timestamp.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Date/Time Picker Combo (UNIX timestamp)',
	'id'   => 'wiki_test_datetime_timestamp',
	'type' => 'text_datetime_timestamp',
) );
```

#### CSS Field Class:
`cmb-type-text-datetime-timestamp`
<br>
<br>
<br>

### `text_datetime_timestamp_timezone`
____
Date, time and timezone field, stored as serialized DateTime object. Example:
![Screenshot of text_datetime_timestamp_timezone CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_text_datetime_timestamp_timezone.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Date/Time Picker/Time zone Combo (serialized DateTime object)',
	'id'   => 'wiki_test_datetime_timestamp_timezone',
	'type' => 'text_datetime_timestamp_timezone',
) );
```

#### CSS Field Class:
`cmb-type-datetime-timestamp-timezone`
<br>
<br>
<br>

### `hidden`
____
Adds a `hidden` input type to the bottom of the CMB2 output. Example:

```php
$cmb->add_field( array(
	'id'   => 'wiki_test_hidden',
	'type' => 'hidden',
) );
```

#### CSS Field Class:
not applicable to this field type.
<br>
<br>
<br>

### `colorpicker`
____
A colorpicker field. Example:
![Screenshot of color picker CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_colorpicker.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test Color Picker',
	'id'      => 'wiki_test_colorpicker',
	'type'    => 'colorpicker',
	'default' => '#ffffff',
	// 'options' => array(
	// 	'alpha' => true, // Make this a rgba color picker.
	// ),
) );
```

<a name="colorpicker-additional-field-options"></a>
#### Additional Field Options:

Enable transparency (rgba) control by adding `'options' => array( 'alpha' => true )` to your field config array.

The CMB2 colorpicker uses the built in WordPress colorpicker, Iris [automattic.github.io/Iris/] (http://automattic.github.io/Iris/)

All of the default options in Iris are configurable within the CMB2 colorpicker field.

[Default Iris Options] (http://automattic.github.io/Iris/#options):

```js
options = {
    color: false,
    mode: 'hsl',
    controls: {
        horiz: 's', // horizontal defaults to saturation
        vert: 'l', // vertical defaults to lightness
        strip: 'h' // right strip defaults to hue
    },
    hide: true, // hide the color picker by default
    border: true, // draw a border around the collection of UI elements
    target: false, // a DOM element / jQuery selector that the element will be appended within. Only used when called on an input.
    width: 200, // the width of the collection of UI elements
    palettes: false // show a palette of basic colors beneath the square.
}
```

#### Iris Option Example Usage:

```php
$cmb->add_field( array(
    'name'    => 'Test Color Picker',
    'id'      => 'wiki_test_colorpicker',
    'type'    => 'colorpicker',
    'default' => '#ffffff',
    'attributes' => array(
        'data-colorpicker' => json_encode( array(
            // Iris Options set here as values in the 'data-colorpicker' array
            'palettes' => array( '#3dd0cc', '#ff834c', '#4fa2c0', '#0bc991', ),
        ) ),
    ),
) );
```

#### CSS Field Class:
`cmb-type-colorpicker`
<br>
<br>
<br>

### `checkbox`
____
Standard checkbox. Example:
![Screenshot of checkbox CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_checkbox.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test Checkbox',
	'desc' => 'field description (optional)',
	'id'   => 'wiki_test_checkbox',
	'type' => 'checkbox',
) );
```

#### To use in your template (in the loop):
```php
<?php if ( get_post_meta( get_the_ID(), 'wiki_test_checkbox', 1 ) ) : ?>
	<div>Some HTML</div>
<?php endif; ?>
```

#### CSS Field Class:
`cmb-type-checkbox`
<br>
<br>
<br>

### `multicheck` and `multicheck_inline`
____
A field with multiple checkboxes (and multiple can be selected). Example:
![Screenshot of multi checkbox CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_multicheck.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test Multi Checkbox',
	'desc'    => 'field description (optional)',
	'id'      => 'wiki_test_multicheckbox',
	'type'    => 'multicheck',
	'options' => array(
		'check1' => 'Check One',
		'check2' => 'Check Two',
		'check3' => 'Check Three',
	),
) );
```

#### CSS Field Class:
`cmb-type-multicheck`

#### Custom Field Attributes:

* `'select_all_button' => false`, Setting to false disables the 'Select All' button
<br>
<br>
<br>

### `radio`
____
Standard radio buttons. Example:
![Screenshot of standard radio button CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_radio.jpg)

```php
$cmb->add_field( array(
	'name'             => 'Test Radio',
	'id'               => 'wiki_test_radio',
	'type'             => 'radio',
	'show_option_none' => true,
	'options'          => array(
		'standard' => __( 'Option One', 'cmb2' ),
		'custom'   => __( 'Option Two', 'cmb2' ),
		'none'     => __( 'Option Three', 'cmb2' ),
	),
) );
```
Set the optional paremter, `show_option_none`, to `true` to use the default text, 'None', or specify another value, i.e. 'No selection'. By default `show_option_none` is false.

#### CSS Field Class:
`cmb-type-radio`
<br>
<br>
<br>

### `radio_inline`
____
Inline radio buttons. Example:
![Screenshot of radio button inline CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_radio_inline.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test Radio inline',
	'id'      => 'wiki_test_radio_inline',
	'type'    => 'radio_inline',
	'options' => array(
		'standard' => __( 'Option One', 'cmb2' ),
		'custom'   => __( 'Option Two', 'cmb2' ),
		'none'     => __( 'Option Three', 'cmb2' ),
	),
	'default' => 'standard',
) );
```

#### CSS Field Class:
`cmb-type-radio-inline`
<br>
<br>
<br>

### `select`
____
Standard select dropdown. Example:
![Screenshot of select dropdown CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_select.jpg)

```php
$cmb->add_field( array(
	'name'             => 'Test Select',
	'desc'             => 'Select an option',
	'id'               => 'wiki_test_select',
	'type'             => 'select',
	'show_option_none' => true,
	'default'          => 'custom',
	'options'          => array(
		'standard' => __( 'Option One', 'cmb2' ),
		'custom'   => __( 'Option Two', 'cmb2' ),
		'none'     => __( 'Option Three', 'cmb2' ),
	),
) );
```
Set the optional paremeter, `show_option_none`, to `true` to use the default text, 'None', or specify another value, i.e. 'No selection'. By default `show_option_none` is false.

#### CSS Field Class:
`cmb-type-select`

#### Optional:

* All the types that take an `'options'` parameter can be replaced with an `'options_cb'` parameter that allows you to specify a callback. This callback will receive the field object which you can use to check the object ID (`$field->object_id`). This can be handy if you need to build options based on the current post or context. The callback should return an array of options in the format displayed in these examples.

**Example:**
```php
	// in the field array..
	'options_cb' => 'show_cat_or_dog_options',
```
```php
// Callback function
function show_cat_or_dog_options( $field ) {

	if ( has_tag( 'cats', $field->object_id ) ) {
		return array(
			'tabby'   => __( 'Tabby', 'cmb2' ),
			'siamese' => __( 'Siamese', 'cmb2' ),
			'calico'  => __( 'Calico', 'cmb2' ),
		);
	} else {
		return array(
			'german-shepherd' => __( 'German Shepherd', 'cmb2' ),
			'bulldog'         => __( 'Bulldog', 'cmb2' ),
			'poodle'          => __( 'Poodle', 'cmb2' ),
		);
	}
}
```

#### Notes

If you need the label value wherever you are using the select field's value (vs just the value), you can define your options in a function, and get the label by comparing the value against the array given by the function. [Example here](http://wordpress.stackexchange.com/a/220703/45740).

You can write conditional blocks in your code depending on the value of the selected option with something like this:
```php
$select_value = get_post_meta( get_the_ID(), 'wiki_test_select', true ) );

// If (Option 2 True)
if ( 'custom' === $select_value ) {
	// some PHP code
} else {
	// some other PHP code
}
```
<br>
<br>
<br>

### `taxonomy_radio`
____
Radio buttons pre-populated with taxonomy terms. Example:
![Screenshot of taxonomy radio button CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_taxonomy_radio.jpg)

```php
$cmb->add_field( array(
	'name'           => 'Test Taxonomy Radio',
	'desc'           => 'Description Goes Here',
	'id'             => 'wiki_test_taxonomy_radio',
	'taxonomy'       => '', // Enter Taxonomy Slug
	'type'           => 'taxonomy_radio',
	// Optional :
	'text'           => array(
		'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
	),
	'remove_default' => 'true' // Removes the default metabox provided by WP core.
	// Optionally override the args sent to the WordPress get_terms function.
	'query_args' => array(
		// 'orderby' => 'slug',
		// 'hide_empty' => true,
	),
) );
```

#### CSS Field Class:
`cmb-type-taxonomy-radio`
<br>
<br>
<br>

### `taxonomy_radio_inline`
____
Inline radio buttons pre-populated with taxonomy terms. Example:
![Screenshot of taxonomy radio button inline CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_taxonomy_radio_inline.jpg)

#### CSS Field Class:
`cmb-type-taxonomy-radio-inline`
<br>
<br>
<br>

### `taxonomy_radio_hierarchical`
____
Radio buttons pre-populated with taxonomy terms, displayed in a hierarchical fashion (indented). Example:
![Screenshot of taxonomy radio hierarchical CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_taxonomy_radio_hierarchical-field-type.png)
#### CSS Field Class:
`cmb-type-taxonomy-radio-hierarchical`
<br>
<br>
<br>

### `taxonomy_select`
____
A select field pre-populated with taxonomy terms. Example:
![Screenshot of taxonomy select dropdown CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_taxonomy_select.jpg)

```php
$cmb->add_field( array(
	'name'           => 'Test Taxonomy Select',
	'desc'           => 'Description Goes Here',
	'id'             => 'wiki_test_taxonomy_select',
	'taxonomy'       => 'category', //Enter Taxonomy Slug
	'type'           => 'taxonomy_select',
	'remove_default' => 'true' // Removes the default metabox provided by WP core.
	// Optionally override the args sent to the WordPress get_terms function.
	'query_args' => array(
		// 'orderby' => 'slug',
		// 'hide_empty' => true,
	),
) );
```

#### CSS Field Class:
`cmb-type-taxonomy-select`
<br>
<br>
<br>

### `taxonomy_multicheck`
____
A field with checkboxes with taxonomy terms, and multiple terms can be selected. Example:
![Screenshot of taxonomy multiple checkbox CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_taxonomy_multiselect.jpg)

```php
$cmb->add_field( array(
	'name'           => 'Test Taxonomy Multicheck',
	'desc'           => 'Description Goes Here',
	'id'             => 'wiki_test_taxonomy_multicheck',
	'taxonomy'       => '', //Enter Taxonomy Slug
	'type'           => 'taxonomy_multicheck',
	// Optional :
	'text'           => array(
		'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
	),
	'remove_default' => 'true' // Removes the default metabox provided by WP core.
	// Optionally override the args sent to the WordPress get_terms function.
	'query_args' => array(
		// 'orderby' => 'slug',
		// 'hide_empty' => true,
	),
) );
```

#### CSS Field Class:
`cmb-type-taxonomy-multicheck`

#### Custom Field Attributes:

* `'select_all_button' => false`, Setting to false disables the 'Select All' button
<br>
<br>
<br>

### `taxonomy_multicheck_inline`
____
Inline checkboxes with taxonomy terms. Example:
![Screenshot of taxonomy multiple checkbox inline CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_taxonomy_multiselect_inline.jpg)
#### CSS Field Class:
`cmb-type-taxonomy-multicheck-inline`

#### Custom Field Attributes:

* `'select_all_button' => false`, Setting to false disables the 'Select All' button

#### Notes
To retrieve the values from the taxonomy fields, use `get_the_terms`, not `get_post_meta`, etc. The taxonomy fields are not intended to provide an arbitrary list of terms to pick from, but are intended to be a replacement for the default taxonomy meta-boxes. I.e. they are meant to set the taxonomy terms on an object, and will not save as a meta value. Any other use of these types will be hacky and/or buggy. Suggest looking at building a custom field type instead - [Example](https://github.com/CMB2/CMB2/wiki/Tips-&-Tricks#a-dropdown-for-taxonomy-terms-which-does-not-set-the-term-on-the-post).

<br>
<br>
<br>

### `taxonomy_multicheck_hierarchical`
____
A field with checkboxes with taxonomy terms, and multiple terms can be selected. Displayed in a hierarchical fashion (indented). Example:
![Screenshot of taxonomy multicheck hierarchical CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_taxonomy_multicheck_hierarchical-field-type.png)
#### CSS Field Class:
`cmb-type-taxonomy-multicheck-hierarchical`
<br>
<br>
<br>

### `wysiwyg`
____
A metabox with TinyMCE editor (same as WordPress' visual editor). Example:
![Screenshot of wysiwyg CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_wysiwyg.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test wysiwyg',
	'desc'    => 'field description (optional)',
	'id'      => 'wiki_test_wysiwyg',
	'type'    => 'wysiwyg',
	'options' => array(),
) );
```

#### CSS Field Class:
`cmb-type-wysiwyg`

#### Notes
Text added in a wysiwyg field will not have paragraph tags automatically added, the same is true of standard WordPress post content editing with the WYSIWYG. When outputting formatted text, wrap your get_post_meta() call with wpautop to generate the paragraph tags.

```php
<?php echo wpautop( get_post_meta( get_the_ID(), 'wiki_test_wysiwyg', true ) ); ?>
```
If you want oembed filters to apply to the wysiwyg content, add this helper function to your theme or plugin:

```php
function yourprefix_get_wysiwyg_output( $meta_key, $post_id = 0 ) {
	global $wp_embed;

	$post_id = $post_id ? $post_id : get_the_id();

	$content = get_post_meta( $post_id, $meta_key, 1 );
	$content = $wp_embed->autoembed( $content );
	$content = $wp_embed->run_shortcode( $content );
	$content = wpautop( $content );
	$content = do_shortcode( $content );

	return $content;
}

...

echo yourprefix_get_wysiwyg_output( 'wiki_test_wysiwyg', get_the_ID() );
```

The options array allows you to customize the settings of the wysiwyg. Here's an example with all the options:

```php
array(
	'name'    => 'Test wysiwyg',
	'desc'    => 'field description (optional)',
	'id'      => 'wiki_test_wysiwyg',
	'type'    => 'wysiwyg',
	'options' => array(
	    'wpautop' => true, // use wpautop?
	    'media_buttons' => true, // show insert/upload button(s)
	    'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
	    'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
	    'tabindex' => '',
	    'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
	    'editor_class' => '', // add extra class(es) to the editor textarea
	    'teeny' => false, // output the minimal editor config used in Press This
	    'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
	    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
	    'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
	),
),
```

The `'id'` should not be set to 'content' as the standard editor has this id and it will result in a non working editor.
<br>
<br>
<br>

### `file`
____
A file uploader. By default it will store the file url and allow either attachments or URLs. This field type will also store the attachment ID (useful for getting different image sizes). It will store it in `$id . '_id'`, so if your field id is `wiki_test_image` the ID is stored in `wiki_test_image_id`. You can also limit it to only allowing attachments (can't manually type in a URL), which is also useful if you plan to use the attachment ID. The example shows its default values, with possible values commented inline. Example:
![Screenshot of file/image upload CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_file.jpg)

```php
$cmb->add_field( array(
	'name'    => 'Test File',
	'desc'    => 'Upload an image or enter an URL.',
	'id'      => 'wiki_test_image',
	'type'    => 'file',
	// Optional:
	'options' => array(
		'url' => false, // Hide the text input for the url
	),
	'text'    => array(
		'add_upload_file_text' => 'Add File' // Change upload button text. Default: "Add or Upload File"
	),
	// query_args are passed to wp.media's library query.
	'query_args' => array(
		'type' => 'application/pdf', // Make library only display PDFs.
		// Or only allow gif, jpg, or png images
		// 'type' => array(
		// 	'image/gif',
		// 	'image/jpeg',
		// 	'image/png',
		// ),
	),
	'preview_size' => 'large', // Image size to use when previewing in the admin.
) );
```

#### CSS Field Class:
`cmb-type-file`

Example using the `wiki_test_image_id` meta key to retrieve a medium image:
```php
$image = wp_get_attachment_image( get_post_meta( get_the_ID(), 'wiki_test_image_id', 1 ), 'medium' );
```
<br>
<br>
<br>

### `file_list`
____
A file uploader that allows you to add as many files as you want. Once added, files can be dragged and dropped to reorder.

This is a repeatable field, and will store its data in an array, with the attachment ID as the array key and the attachment url as the value. Example:
![Screenshot of multiple file/image upload CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_file_list.jpg)

```php
$cmb->add_field( array(
	'name' => 'Test File List',
	'desc' => '',
	'id'   => 'wiki_test_file_list',
	'type' => 'file_list',
	// 'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
	// 'query_args' => array( 'type' => 'image' ), // Only images attachment
	// Optional, override default text strings
	'text' => array(
		'add_upload_files_text' => 'Replacement', // default: "Add or Upload Files"
		'remove_image_text' => 'Replacement', // default: "Remove Image"
		'file_text' => 'Replacement', // default: "File:"
		'file_download_text' => 'Replacement', // default: "Download"
		'remove_text' => 'Replacement', // default: "Remove"
	),
) );
```

#### CSS Field Class:
`cmb-type-file-list`

#### Custom Field Attributes:

* `preview_size` Changes the size of the preview images in the field. Default: array( 50, 50 ).

#### Sample function for getting and outputting `file_list` images

```php
/**
 * Sample template tag function for outputting a cmb2 file_list
 *
 * @param  string  $file_list_meta_key The field meta key. ('wiki_test_file_list')
 * @param  string  $img_size           Size of image to show
 */
function cmb2_output_file_list( $file_list_meta_key, $img_size = 'medium' ) {

	// Get the list of files
	$files = get_post_meta( get_the_ID(), $file_list_meta_key, 1 );

	echo '<div class="file-list-wrap">';
	// Loop through them and output an image
	foreach ( (array) $files as $attachment_id => $attachment_url ) {
		echo '<div class="file-list-image">';
		echo wp_get_attachment_image( $attachment_id, $img_size );
		echo '</div>';
	}
	echo '</div>';
}
```
#### To use in your template (in the loop):
```php
<?php cmb2_output_file_list( 'wiki_test_file_list', 'small' ); ?>
```
<br>
<br>

### `oembed`
____
Displays embedded media inline using WordPress' built-in oEmbed support. See [codex.wordpress.org/Embeds](http://codex.wordpress.org/Embeds) for more info and for a list of embed services supported. (added in 0.9.1). Example:
![Screenshot of oembed CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_oembed.jpg)

```php
$cmb->add_field( array(
	'name' => 'oEmbed',
	'desc' => 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.',
	'id'   => 'wiki_test_embed',
	'type' => 'oembed',
) );
```

#### CSS Field Class:
`cmb-type-oembed`

#### Notes
Text added in a `oembed` field will not automatically display the embed in your theme. To generate the embed in your theme, this is a method you could use:

```php
$url = esc_url( get_post_meta( get_the_ID(), 'wiki_test_embed', 1 ) );
echo wp_oembed_get( $url );
```
<br>
<br>
<br>

### `group`
____
Hybrid field that supports adding other fields as a repeatable group. Example:
![Screenshot of group / repeatable CMB field type](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/screenshot_group.jpg)

```php
$group_field_id = $cmb->add_field( array(
	'id'          => 'wiki_test_repeat_group',
	'type'        => 'group',
	'description' => __( 'Generates reusable form entries', 'cmb2' ),
	// 'repeatable'  => false, // use false if you want non-repeatable group
	'options'     => array(
		'group_title'       => __( 'Entry {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
		'add_button'        => __( 'Add Another Entry', 'cmb2' ),
		'remove_button'     => __( 'Remove Entry', 'cmb2' ),
		'sortable'          => true,
		// 'closed'         => true, // true to have the groups closed by default
		// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
	),
) );

// Id's for group's fields only need to be unique for the group. Prefix is not needed.
$cmb->add_group_field( $group_field_id, array(
	'name' => 'Entry Title',
	'id'   => 'title',
	'type' => 'text',
	// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
) );

$cmb->add_group_field( $group_field_id, array(
	'name' => 'Description',
	'description' => 'Write a short description for this entry',
	'id'   => 'description',
	'type' => 'textarea_small',
) );

$cmb->add_group_field( $group_field_id, array(
	'name' => 'Entry Image',
	'id'   => 'image',
	'type' => 'file',
) );

$cmb->add_group_field( $group_field_id, array(
	'name' => 'Image Caption',
	'id'   => 'image_caption',
	'type' => 'text',
) );
```

#### CSS Field Class:
`cmb-field-list`

All repeatable group entries will be saved as an array to that meta-key. Example usage to pull data back:

```php
$entries = get_post_meta( get_the_ID(), 'wiki_test_repeat_group', true );

foreach ( (array) $entries as $key => $entry ) {

	$img = $title = $desc = $caption = '';

	if ( isset( $entry['title'] ) ) {
		$title = esc_html( $entry['title'] );
	}

	if ( isset( $entry['description'] ) ) {
		$desc = wpautop( $entry['description'] );
	}

	if ( isset( $entry['image_id'] ) ) {
		$img = wp_get_attachment_image( $entry['image_id'], 'share-pick', null, array(
			'class' => 'thumb',
		) );
	}

	$caption = isset( $entry['image_caption'] ) ? wpautop( $entry['image_caption'] ) : '';

	// Do something with the data
}

// Or get just the title from the first group.
$first_entry_title = '';
if ( isset( $entries[0]['title'] ) ) {
	$first_entry_title = esc_html( $entries[0]['title'] );
}`
```
#### Custom Field Attributes:

The `group` field type supports several a few extra parameters.

The following are documented on the [Field Parameters page](https://github.com/CMB2/CMB2/wiki/Field-Parameters#before_group-after_group-before_group_row-after_group_row).

* `'before_group'`
* `'after_group'`
* `'before_group_row'`
* `'after_group_row'`

The field also supports the following:

* `'group_title'` - Defines the title for each group section. Can use the `{#}` placeholder to output the group number.
* `'add_button'` - Defines the text for the group add button. Defaults to "Add Group".
* `'remove_button'` - Defines the text for the group remove button. Defaults to "Remove Group".
* `'remove_confirm'` - Defines the text used to confirm if group should be removed. By default, it is empty, so there is no confirmation when removing a group.
* `'sortable'` - Whether groups are sortable. Defaults to false.
* `'closed'` - Whether groups are displayed closed (collapsed). Defaults to false.

### Custom Field Types
You can [define your own field types](https://github.com/CMB2/CMB2/wiki/Adding-your-own-field-types) as well.

There is also a [large eco-system of custom field types](https://github.com/CMB2/CMB2#custom-field-types) available as well.

### Common Field Parameters
Common field parameters are available for all field types. [Full documentation for these can be found on the CMB2 wiki](https://github.com/CMB2/CMB2/wiki/Field-Parameters).

* `name` _string_: Human readable field name.
* `desc` _string_: Field description.
* `id` _string_: The field id / name HTML attribute.
* `type` _string_: Field type (see above list)
* `repeatable` _bool_: Whether the field should be repeatable.
* `default` _mixed_: The default value for the field. Can be any scalar value.
* `show_names` _bool_: Whether to show the field label.
* `options` _array_: Associative array of 'value' => 'label' pairs for multiple choice fields.
* `before`, `after`, `before_row`, `after_row`, `before_field`, `after_field` _callback_:
* `classes` _string|array|callback_: A string, array or callback that returns either type of class names to apply to the field.
* `on_front` _bool_: If the meta box is to be used on the front end individual fields can be hidden by setting this to false.
* `attributes` _array_: Name and value pairs of custom attributes to add to the field.
* `show_on_cb` _callback_: Callback for whether to show the field.
* `options_cb` _callback_: Callback variant of the `options` parameter.
* `escape_cb` _callback_: Callback used to escape the field value.
* `sanitization_cb` _callback_: Callback for sanitising the field value before saving.
* `render_row_cb` _callback_: Callback for overriding the field row HTML.

## Creating a basic post metabox

At it's most basic adding a custom meta field starts on the `cmb2_init` or `cmb2_admin_init` hook. The following example adds a field for an alternative short headline to posts.

```php
add_action( 'cmb2_init', function () {

	// Create the meta box.
	$meta_box = new_cmb2_box( [
		'id' => 'editorial-options',
		'title' => esc_html__( 'Editorial options' ),
		'object_types'  => [ 'post' ], // Post types
		'show_in_rest' => WP_REST_Server::ALLMETHODS,
	] );

	// Add a text field to the meta box.
	$meta_box->add_field( [
		'id' => 'short-title',
		'name' => esc_html__( 'Short headline' ),
		'desc' => esc_html__( 'Alternative short headline for use on article listings and social media' ),
		'type' => 'text',
	] );

} );
```

The metadata can then be retrieved using `get_post_meta()`.

```php
$short_title = get_post_meta( get_the_ID(), 'short-title', true );
```

You can [find complete documentation for CMB2 here](https://cmb2.io/) and [full code examples for creating each type of form in the `example-functions.php` file of the CMB2 plugin](https://github.com/CMB2/CMB2/blob/develop/example-functions.php).

[![video overview of what you will get with the `example-functions.php` file](https://raw.githubusercontent.com/wiki/CMB2/CMB2/images/example-functions-video-screenshot.png)](https://www.youtube.com/watch?v=QP3N8_q75Ik)

## Disabling the framework

You can toggle loading of the framework via the config if you need to by setting the `custom-meta-boxes` property to true or false.

```json
{
	"extra": {
		"platform": {
			"modules": {
				"cms": {
					"custom-meta-boxes": false
				}
			}
		}
	}
}
```
