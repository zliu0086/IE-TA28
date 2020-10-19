=== Plugin Name ===
Contributors: HotThemes
Donate link: https://hot-themes.com/
Tags: widget, random, image, images, responsive, thumbnail
Requires at least: 3.9
Tested up to: 5.5
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hot Random Image is a basic widget that shows a randomly picked image from a selected folder where images are stored.

== Description ==

Hot Random Image plugin by [HotThemes](http://hot-themes.com/ "HotThemes") is a basic widget that shows a randomly picked image from a selected folder where images are stored. You can define a folder and widget will show all images from this folder in random order. Also, it's possible to select only certain images from the folder that will be added in rotation. Each image can be linked. Alt text is optional. Image dimensions (width and height) can be defined in any format (pixels, percents, auto-mode...). Therefore, this widget is appropriate for all responsive websites.

== Installation ==

1. Install plugin as usually from WordPress.org. You can also manually download it, unpack and upload `plugin_hot_random_image` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Publish Hot Random Image widget into the available widget sidebars or use shortcode to include it in your posts.
4. To get some images displayed, you should enter a folder where your images are stored. This can be any folder from your site.
5. In the widget options, you can see other parameters that defines the widget's output. Experiment with these options to configure the widget for your site.
6. If you want to add shortcode inside your posts, use this format `{randomimage}images/random,100%,auto,Random image,https://hot-themes.com/{/randomimage}` where the first parameter is folder with images (relative path from WordPress root folder), the second parameter is image width (if not set, value 100% will be used), the third parameter is image height (if not set, value auto will be used), the fourth parameter is ALT text (optional) and the fifth parameter is link of the images (optional).

== Frequently Asked Questions ==

= How to use the plugin in a widget position? =

Go to Appearance > Widgets and drop Hot Random Image widget into any widget position. Then open it to see the widget options. Enter folder where your images are uploaded, relative to your WordPress installation. In example "wp-content/uploads/2014/12".

= How to use the plugin as shortcode? =

If you want to add shortcode inside your posts, use this format `{randomimage}images/random,100%,auto,Random image,https://hot-themes.com/{/randomimage}` where the first parameter is folder with images (relative path from WordPress root folder), the second parameter is image width (if not set, value 100% will be used), the third parameter is image height (if not set, value auto will be used), the fourth parameter is ALT text (optional) and the fifth parameter is link of the images (optional).

= The plugin doesn't show any images? =

Make sure you entered a valid folder where your images are uploaded. It should be relative to your WordPress root folder.

== Screenshots ==

1. Sample screen shot of the Hot Random Image widget.
2. Screen shot of the Hot Random Image widget parameters.

== Changelog ==

= 1.2 =
* Methods name bug fixes

= 1.1 =
* Various enhancements in plugin code

= 1.0 =
* The initial release

== Upgrade Notice ==

= 1.0 =
The initial release