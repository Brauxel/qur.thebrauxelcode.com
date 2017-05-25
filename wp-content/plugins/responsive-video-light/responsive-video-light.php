<?php
/*
 * Plugin Name: Responsive Video Light
 * Plugin URI: http://wordpress.org/extend/plugins/responsive-video-light/
 * Description: A plugin to add responsive videos to pages and posts
 * Version: 1.5.2
 * Author: Bill Knechtel
 * Author URI: http://bitpusher.tk
 * License: GPLv2
 *
 * Copyright 2013-2015 William Knechtel
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

$base_path = plugin_dir_path(__FILE__);

/**
 * Register and queue admin-facing CSS
 */
function rvl_admin_css()
{
    //That's right - I namespaced Bootstrap so my admin page would be responsive ;-)
    wp_register_style(
        'tbm-bootstrap-3',
        plugins_url('/css/bootstrap.min.css', __FILE__),
        array(),
        '2014123001',
        'all'
    );
    wp_enqueue_style('tbm-bootstrap-3');

    wp_register_style(
        'rvl-admin',
        plugins_url('/css/rvl-admin.css', __FILE__),
        array(),
        '2014122301',
        'all'
    );
    wp_enqueue_style('rvl-admin');
}

add_action('admin_enqueue_scripts', 'rvl_admin_css');

/**
 * Register and queue our user-facing CSS
 */
function rvl_css()
{
    // Register the css styling to make the video responsive:
    wp_register_style(
        'responsive-video-light',
        plugins_url('/css/responsive-videos.css', __FILE__),
        array(),
        '20130111',
        'all'
    );
    wp_enqueue_style('responsive-video-light');
}

add_action('wp_enqueue_scripts', 'rvl_css');

// ----------------------------------------------------------------------------
// Create the admin settings page
// ----------------------------------------------------------------------------

function register_rvl_settings()
{ // whitelist options
    register_setting('rvl_options', 'rvl_options_field');
}

function rvl_menu()
{
    add_options_page(
        'Responsive Video Light Options',
        'Responsive Video Light',
        'activate_plugins',
        'rvl_options',
        'rvl_plugin_options'
    );

    add_action('admin_init', 'register_rvl_settings');
}

add_action('admin_menu', 'rvl_menu');

function rvl_plugin_action_links($links, $file)
{
    static $this_plugin;

    if (! $this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
    if ($file == $this_plugin) {
        $settings_link =
            '<a href="'
            . get_bloginfo('wpurl')
            . '/wp-admin/admin.php?page=rvl_options">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'rvl_plugin_action_links', 10, 2);

// ----------------------------------------------------------------------------
// Admin page plugin options
// ----------------------------------------------------------------------------

function rvl_plugin_options()
{
    global $base_path;
    $options = get_option('rvl_options_field', array());

    // Plugin options
    include $base_path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'rvl_plugin_options_head.php';
    wp_nonce_field('update-options');
    settings_fields('rvl_options');
    include $base_path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'rvl_plugin_options.php';
}

// ----------------------------------------------------------------------------
// Create the YouTube shortcode
// ----------------------------------------------------------------------------
function responsive_youtube_shortcode($attributes, $content = null)
{
    $options = get_option('rvl_options_field', array());

    $query_string = array();

    // Prep our query string based on defaults
    foreach ($options as $option => $option_value) {
        switch ($option) {
            case 'disable_youtube_related_videos':
                $query_string['rel'] = '0';
                break;
            case 'disable_youtube_showinfo':
                $query_string['showinfo'] = '0';
                break;
            case 'enable_youtube_autoplay':
                $query_string['autoplay'] = '1';
                break;
            case 'enable_modest_branding':
                $query_string['modestbranding'] = '1';
                break;
            case 'youtube_wmode':
                switch ($option_value) {
                    case "transparent":
                        $query_string['wmode'] = "transparent";
                        break;
                    case "opaque":
                        $query_string['wmode'] = "opaque";
                        break;
                }
                break;
            case 'youtube_theme':
                if ($option_value == 'light') {
                    $query_string['theme'] = 'light';
                } else {
                    $query_string['theme'] = 'dark';
                }
                break;
        }
    }

    $video_id = null;

    // Determine what options were passed in. These can potentially override
    // The defaults we've just set up.
    foreach ($attributes as $attribute) {
        switch ($attribute) {
            case 'rel':
                $query_string['rel'] = '1';
                break;
            case 'norel':
                $query_string['rel'] = '0';
                break;
            case 'showinfo':
                $query_string['showinfo'] = '1';
                break;
            case 'noshowinfo':
                $query_string['showinfo'] = '0';
                break;
            case 'wmode_opaque':
                $query_string['wmode'] = 'opaque';
                break;
            case 'wmode_transparent':
                $query_string['wmode'] = 'transparent';
                break;
            case 'autoplay':
                $query_string['autoplay'] = '1';
                break;
            case 'noautoplay':
                $query_string['autoplay'] = '0';
                break;
            case 'modestbranding':
                $query_string['modestbranding'] = '1';
                break;
            case 'nomodestbranding':
                $query_string['modestbranding'] = '0';
                break;
            case 'dark_theme':
                $query_string['theme'] = 'dark';
                break;
            case 'light_theme':
                $query_string['theme'] = 'light';
                break;
            default:
                // Fairly primitive extraction - might want to beef this up
                if (preg_match('/^http[s]?:\/\/.*(v=([-0-9a-zA-Z_]*)).*$/', $attribute, $matches)) {
                    $video_id = $matches[2];
                } elseif (preg_match('/^http[s]?:\/\/youtu.be\/([-0-9a-zA-Z_]*)/', $attribute, $matches)) {
                    $video_id = $matches[1];
                } elseif (preg_match('/^http[s]?:\/\/www.youtube.com\/embed\/([-0-9a-zA-Z_]*)/', $attribute, $matches)) {
                    $video_id = $matches[1];
                } elseif (preg_match('/^[-0-9a-zA-Z_]*$/', $attribute)) {
                    $video_id = $attribute;
                }
                break;
        }
    }

    // Convert $query_string from an array into a usable query string
    $formatted_query_string = '';

    foreach ($query_string as $parameter => $value) {
        $formatted_query_string .= '&' . $parameter . '=' . $value;
    }
    $formatted_query_string = substr($formatted_query_string, 1);

    // Format and return the content replacement for the shortcode
    if ($video_id) {
        $content = '
      <div class="video-wrapper">
        <div class="video-container">
          <iframe src="//www.youtube.com/embed/' . $video_id . '?' . $formatted_query_string . '" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
    ';
    } else {
        $content = "[OH DEAR: responsive_youtube has some malformed syntax.]";
    }
    return $content;
}

add_shortcode('responsive_youtube', 'responsive_youtube_shortcode');

// ----------------------------------------------------------------------------
// Create the Vimeo shortcode
// ----------------------------------------------------------------------------
function responsive_vimeo_shortcode($attributes, $content = null)
{
    $options = get_option('rvl_options_field', array());
    $query_string = array();

    foreach ($options as $option => $option_value) {
        switch ($option) {
            case 'disable_vimeo_title_display':
                $query_string['title'] = '0';
                break;
            case 'disable_vimeo_byline_display':
                $query_string['byline'] = '0';
                break;
            case 'disable_vimeo_portrait_display':
                $query_string['portrait'] = '0';
                break;
            case 'enable_vimeo_autoplay':
                $query_string['autoplay'] = '1';
                break;
            case 'enable_vimeo_loop':
                $query_string['loop'] = '1';
                break;
        }
    }

    $video_id = null;

    // Determine what options were passed in (ignore anything that doesn't look
    // like an id)
    foreach ($attributes as $attribute) {
        switch ($attribute) {
            case "title":
                $query_string['title'] = '1';
                break;
            case "notitle":
                $query_string['title'] = '0';
                break;
            case "byline":
                $query_string['byline'] = '1';
                break;
            case "nobyline":
                $query_string['byline'] = '0';
                break;
            case "portrait":
                $query_string['portrait'] = '1';
                break;
            case "noportrait":
                $query_string['portrait'] = '0';
                break;
            case "notab":
                $query_string['portrait'] = '0';
                $query_string['byline'] = '0';
                $query_string['title'] = '0';
                break;
            case "autoplay":
                $query_string['autoplay'] = '1';
                break;
            case "noautoplay":
                $query_string['autoplay'] = '0';
                break;
            case "loop":
                $query_string['loop'] = '1';
                break;
            case "noloop":
                $query_string['loop'] = '0';
                break;
            default:
                // Fairly primitive extraction - might want to beef this up
                if (preg_match('/^https?:\/\/.*\/(\d*)$/', $attribute, $matches)) {
                    $video_id = $matches[1];
                } elseif (preg_match('/^\d*$/', $attribute)) {
                    $video_id = $attribute;
                }
                break;
        }
    }

    // Convert $query_string from an array into a usable query string
    $formatted_query_string = '';

    foreach ($query_string as $parameter => $value) {
        $formatted_query_string .= '&' . $parameter . '=' . $value;
    }
    $formatted_query_string = substr($formatted_query_string, 1);

    // Format and return the content replacement for the shortcode
    if ($video_id) {
        $content = '
      <div class="video-wrapper">
        <div class="video-container">
        <iframe src="//player.vimeo.com/video/' . $video_id . '?' . $formatted_query_string . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        </div>
      </div>
    ';
    } else {
        $content = "[OH DEAR: responsive_vimeo has some malformed syntax.]";
    }
    return $content;
}
add_shortcode('responsive_vimeo', 'responsive_vimeo_shortcode');

?>
