=== WP-EasyDigg ===
Contributors: yzk0370
Tags: post, digg, ajax
Requires at least: 2.5
Tested up to: 2.7 beta 2
Stable tag: 1.1.3

Give your readers a button to digg your post.

== Description ==

The plugin will add a digg button to every post, so your readers could digg the post if they really like it.

**Features:**

* Support for WordPress 2.5
* AJAX digging support
* AJAX paging support
* Widget support
* Prevent repeated digging
* Auto/Manual insert digg button
* Digg on different types of post
* Filter by period
* Display on excerpt
* English/Chinese langauge supported

**Supported Languages:**

* English (default, 1.0 or higher)
* Chinese Simplified (1.1 or higher)

**Demo:**

<a href="http://www.gekimoe.org/">http://www.gekimoe.org/</a>

**Notice**

If you are now using a version under 1.0.4, after you update to above 1.0.4, please go to the options page
to update the settings.

For more information please check changelog in 'Other Notes'.

== Installation ==

1. Unzip archive to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make some changes in the 'setting' => 'WP Easy Digg' to meet your preferences

== Widget usage ==

1. Go to 'Design->Widgets', and add the 'WP EasyDigg' to your blog.
2. In your 'sidebar.php' file add the following lines:
****

    <h3>Most Digged</h3>
    <?php $GLOBALS['edg_instance']->get_list(); ?>

**Arguments:**

    NAME    TYPE       DESCRIPTION                   DEFAULT  VERSIONS
    order   enum       Could be ASC/DESC/RAND, must  DESC     1.0.0 or higher
                       be uppercase.
    offset  integer    (Deprecated)the length will   0        1.0.0 to 1.0.8
                       skip from result.
    limit   integer    The length of the result.     10       1.0.0 or higher
    period  enum       Could be ALL/YEAR/MONTH/WEEK/ ALL      1.0.9 or higher
                       DAY/MANUAL, must be uppercase
                       Option MANUAL is available
                       since 1.0.12.
    days    integer    use when 'period = MANUAL'    30       1.0.12 or higher

== Template tag usage ==
    
1. Go to 'Setting->WP EasyDigg', and check 'auto insert into post/excerpt' options.
2. In your 'index.php' or 'single.php' or anywhere you would like the following lines;
****

    <?php $GLOBALS['edg_instance']->get_button(); ?>
    
**Custom CSS:**

* WP Esay Digg will load edg.css from this plugin's directory.
* You may change the appearance with your custom css code. But please DON'T remove or change the style class name

== Screenshots ==

1. WP Esay Digg
2. WP Esay Digg Options
3. WP Esay Digg Widget options

== Changelog ==

****

    VERSION DATE       TYPE    CHANGES
    1.1.3   2008/11/08 MODIFY  Removed 0 digg items from list.
    1.1.2   2008/10/23 MODIFY  Optimized cookie storage.
    1.1.1   2008/10/22 FIX     Fixed bug of 'Display digg button on xxx' option.
    1.1.0   2008/10/08 FIX     Removed digg text from RSS.
                       ADD     Add Chinese Simplified language support.
    1.0.13  2008/09/27 FIX     Fixed manual period setting doesn't work.
    1.0.12  2008/09/24 MODIFY  Refactor some code using PHP4 syntax.
                       ADD     Added manual input days option.
    1.0.11  2008/09/19 FIX     Fixed bug of widget option doesn't work.
                       FIX     Fixed 'period' option issue.
    1.0.10  2008/09/14 FIX     Fixed error issue of ajax paging when manual add
                               'get_list()' tag.
                       ADD     Added auto/manual add digg button options.
    1.0.9   2008/09/13 FIX     Fixed error issue when using 404 redirect for
                               permalink on IIS server.
                       FIX     Fixed error issue when using custom parameters.
                       ADD     Added period filter support.
                       MODIFY  Refactored code structure.
    1.0.8   2008/09/12 MODIFY  Enhanced ajax paging.
    1.0.7   2008/09/11 FIX     Fixed digg on excerpt issue.
                       MODIFY  Use full php tag instead of short ones to avoid
                               blank text display on option page.
    1.0.6   2008/09/10 ADD     Added ajax paging support.
    1.0.5   2008/09/10 ADD     Added options for display the digg button on
                               excerpt.
    1.0.4   2008/09/09 FIX     Fixed miss initialization bug.
                       ADD     Added post type selector.
    1.0.3   2008/09/09 FIX     Fixed widget list order issue.
    1.0.2   2008/09/09 FIX     Use full php tag instead of short ones to avoid
                               fatal error for some server.
    1.0.1   2008/09/08 FIX     Fixed digg button navigate to top of page issue.
                               Fixed cross-domain bug when the blog is using 
                               two or more domain names
                       MODIFY  Use 'GET' method instead of 'POST'.