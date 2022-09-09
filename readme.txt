=== Sold Out Badge for WooCommerce ===
Contributors: charlieetienne
Tags: woocommerce, sold out, out of stock, badge, wcsob
Stable tag: 4.3.5
Requires at least: 5.2
Tested up to: 6.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate Link: https://paypal.me/webnancy

Display a "Sold Out!" badge on out-of-stock products.
Show the text and colors you want. Perfect for artists, artisans, real estate professionals...

== Description ==

Display a "Sold Out!" badge on out-of-stock products.
When a WooCommerce product becomes out of stock, this plugin will show a badge on thumbnail with the text you will have defined.

This plugin was initially created to help people and companies selling *unique* products or services, like artists, artisans, real estate professionals, etc. It is often beneficial for them to keep showing sold out (out of stock) products on their websites, while displaying a message indicating that the product can't be sold anymore.

However, this plugin can be used by **anyone** wanting to display **any text** in a badge when a product is out of stock. 

It is also possible to display a badge on backorder products.

== Usage & Documentation ==

You can customize options in ***Settings > Sold Out Badge for WooCommerce***

= What can I customize in this plugin ? =

* **Badge text** (you can replace "*Sold Out*" with any text you want, like "*Out of stock*" or "*Coming Soon*")
* **Badge background color**
* **Badge text color**
* **Badge font size**
* **Badge width**
* **Badge height**
* ...
* An option to **hide "*Sale*" badge** if you get an overlap (checked by default)

= Resources =

* **WordPress Plugin:** [https://wordpress.org/plugins/sold-out-badge-for-woocommerce](https://wordpress.org/plugins/sold-out-badge-for-woocommerce)
* **GitHub Repository:** [https://github.com/CharlieEtienne/sold-out-badge-for-woocommerce](https://github.com/CharlieEtienne/sold-out-badge-for-woocommerce)
* **Support:** [https://github.com/CharlieEtienne/sold-out-badge-for-woocommerce/issues](https://github.com/CharlieEtienne/sold-out-badge-for-woocommerce/issues)

== Installation ==

1. Install this plugin either via the WordPress.org plugin directory, or by uploading the files to your server.
2. Activate the plugin.
3. That's it. You're ready to go! Please, refer to the Usage & Documentation section for examples and how-to information.

== Frequently Asked Questions ==

= Is this plugin completely free? =
Yes.

= Can I use this plugin for commercial purposes? =
Sure, go ahead! It is completely open source.

= Can I change the badge text? =
Yes. Go to *Settings > Sold Out Badge for WooCommerce*, you'll find the setting you want.

== Screenshots ==

1. Single product page
2. Store and categories pages
3. Admin settings

== Changelog ==

= 4.3.5 =
* Fix missing hook in Elementor page builder

= 4.3.4 =
* Fix related and upsells products in some themes like Divi

= 4.3.1 =
* Adds compat with Elementor Archive posts widget

= 4.3.0 =
* Allows displaying badge on backorder products instead of/in addition to out of stock products

= 4.2.0 =
* Adds WPML compatibility

= 4.1.0 = 
* Fixes Text Domain Path
* Fixes single product position settings being ignored

= 4.0.0 =
* Major code refactor (nothing is supposed to change unless you made custom dev using this plugin hooks or classes )
* Added compatibility with Lay Theme

= 3.2.2 =
* Minor fixes

= 3.2.1 =
* Fixes and improves alternative method
* Improves compatibility with Divi Builder on single product pages

= 3.2.0 =
* Adds an alternative method (pure CSS). Useful for some themes like Divi.

= 3.1.0 =
* Adds badge in search results
* Ability to turn off the badge on a per-product basis

= 3.0.1 =
* Fix missing "position: absolute" CSS rule

= 3.0.0 =
* **Potential breaking change (in other words, do a backup before upgrading)** : Adds settings options to control appearance (width, height, border-radius, etc.)

= 2.2.0 =
   * Move to Singleton pattern to let other developpers unhook actions and filters.
     If you want to unhook something, use it like this, for example: `remove_filter( 'woocommerce_get_stock_html', [ WCSOB::get_instance(), 'replace_out_of_stock_text' ], 10, 2 );`