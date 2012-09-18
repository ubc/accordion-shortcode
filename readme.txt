=== Accordion Shortcode ===
Contributors: enej, ctlt-dev, ubcdev
Tags: shortcode, accordion
Requires at least: 3.3
Tested up to: 3.3
Stable tag: 1.1

Lets you easy add accordions into your post and pages using the accordion shortcode

== Description ==

Lets you add accordion to your post and pages.

By using the following shortcodes.

`[accordions]
[accordion title="title1"] tab content [/accordion]
[accordion title="title2"] another content tab [/accordion]
[/accordions]`


Additional attributes that you could pass into the shortcode
`[accordions autoHeight='true'  disabled='false' active=0  clearStyle=false collapsible=false fillSpace=false ]
[accordion title='title1' class='new-class']
 Some Text
[/accordion]
[accordion title='title2' class='new-class']
 Some Text
[/accordion]
[/accordions]`

== Sample CSS ==

Here is some sample css to get you started. 
Another place to look for it would be the http://jqueryui.com/themeroller/, The shortcode used the jQuery UI to generate the accordion. 


`
.ui-accordion-header{
	margin:5px 0 0;
}
.ui-accordion-header a{
	padding:5px 12px;
	background: #CCC; 
	color:#FFF;
	display:block;
}
.ui-accordion-header.ui-state-active a,
.ui-accordion-header a:hover{
	background-color: #DDD;
}
.ui-accordion-content{
	padding-top:10px;
}

`

== Installation ==

1. Extract the zip file into wp-content/plugins/ in your WordPress installation
1. Go to plugins page to activate
1. Add styles to make the accordion look the way you want. 


== Changelog ==
= 1.1 = 
* improved js loading. js only gets loaded when it is needed
* fixed bugs


= 1.0 =
* Initial release