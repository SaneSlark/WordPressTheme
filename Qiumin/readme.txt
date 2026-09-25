Qiumin WordPress Theme
=====================

Version: 2.6.1

Qiumin is a classic two-column WordPress theme for personal blogs and software-sharing sites. It includes a homepage slider, sidebar modules, archive/search templates, comment smilies, post view counts, and a lightweight image-host uploader.

Compatibility
-------------

- Requires WordPress 6.8 or later.
- Tested up to WordPress 7.0.
- Requires PHP 7.4 or later.

Notes
-----

- Static theme assets live in `pic/`, sidebar ad images live in `i/`, scripts live in `js/`, and comment smilies live in `smilies/`.
- The image-host feature uploads supported image files to `wp-content/image`.
- Thumbnail lookup order is custom field `thumb`, featured image, first image in post content, then the theme default image.

Changelog
---------

### 2.6.1
- Unified sidebar module text colors with the first sidebar module.
- Reduced homepage slider image cropping so slide images feel less zoomed in.
- Updated the theme version metadata to 2.6.1.

### 2.6.0
- Updated the theme version metadata to 2.6.0.
- Refreshed the README to document the 2.6.0 release.
- Removed unused and duplicated CSS rules from earlier layout/style fixes.
- Increased the header and footer heights for a roomier layout.

### 2.5.6
- Fixed theme asset references for `pic/` and `i/` directories.
- Namespaced internal callbacks to reduce plugin/theme function conflicts.
- Fixed the Gravatar mirror filter so it actually runs.
- Fixed the Bing login background transient key.
- Tightened a few template escaping points.

### 2.5.5
- Refreshed layout, slider, sidebar, pagination, and image-host behavior for newer WordPress versions.

Credits
-------

Author: HeHua
License: GNU General Public License v2.0
