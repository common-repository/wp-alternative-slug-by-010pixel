=== WP Alternative Slug by 010Pixel ===
Contributors: 010pixel
Donate link: https://donorbox.org/building-helpful-google-chrome-extensions
Tags: metabox, slug, custom post types, post url, alternative slug, secondary slug, double url, alternative url, url, plugin, 010pixel
Requires at least: 3.1
Tested up to: 4.9
Stable tag: 1.3.1
License: GNU General Public License, version 3 (GPL-3.0)

Create alternative slug (url) for each page, post or custom post type which will redirect to same main page.

== Description ==
A new way to create multiple URLs for the same post. Very useful when creating url for multiple languages. e.g. your main url can be in english but your alternative url can be in your own language. You can share your own language url on social media.

WP Alternative Slug allows you to choose posts, pages and custom post types to have secondary url.

When you access secondary URL, it will bring you to the main article.

**Made by [010Pixel](http://www.010pixel.com/)**


== Installation ==
1. Unpack the download-package
2. Upload folder include all files to the `/wp-content/plugins/` directory. The final directory tree should look like `/wp-content/plugins/010pixel-wp-alternative-slug/010pixel-wp-alternative-slug.php`
3. Activate the plugin through the `Plugins` menu in WordPress
4. Administrator can go to `Plugins` > `WP Alternative Slug by 010Pixel` menu and configure the plugin (Type of Posts/Custom Posts where you want to show Alternative Slug input Metabox)

* or use the automatic install via backend of WordPress


== Frequently Asked Questions ==
= How to choose which Custom Post Types can have the alternative slug metabox? =
Go to `Plugins` > `WP Alternative Slug by 010Pixel`. You will be able to see all the custom post types including `Post`.
Tick the custom post type for which you want to show alternative slug metabox and click `Submit`. You are done!

= How to set alternative slug for post? =
Enable Alternative Slug Metabox by following the method shown above and you will be able to see template list metabox when you create a post.
Just enter the slug which you want to use as alternative slug for a particular post and the post will have that alternative slug attached to it.

= What if I don't want to have alternative slug for a post? =
Set value for Alternative Slug metabox to empty and save the post.

= My alternative slug is redirecting to wrong page. =
Please check if multiple pages have same slug used. For now there is not checking for unique slug so the latest article with the slug will be loaded.


== Screenshots ==
1. Settings in WordPress to choose Custom Post Types
2. Sample Alternative Slug Metabox


== Use ==
1. Go to `Plugins` > `WP Alternative Slug by 010Pixel`. You will be able to see all the custom post types including `Post`.
2. Tick all the checkbox for the post types for which you want to enable alternative slug metabox.
3. Create a post and enter the alternative slug you would like to use.
4. If you do not want to user alternative slug then select keep the 'Alternative Slug' input empty.

For any query, you can contact me at [010 Pixel](http://www.010pixel.com/)


== Changelog ==
= v1.0.0 =
* 2016-01-06
* Initial release.

= v1.1.0 =
* 2016-01-11
* Removed bug of page being redirected even when the post type is not selected

= v1.2.0 =
* 2016-01-12
* Added function to create proper slug by converting string to lowercase and replacing spaces with underscore
* Removed bug of not redirecting page when not-english languages are used as url slug

= v1.3.0 =
* 2016-11-22
* Added support for space in URL. The plugin will automatically replace spaces with hyphen and redirect to related post.

= v1.3.1 =
* 2018-08-03
* Tested with latest version