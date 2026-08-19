=== Opa! Suite - Webchat ===
Contributors: ronye011
Tags: webchat, chat, opasuite, header script, customer support
Requires at least: 5.6
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Injects the OpaSuite Webchat integration script into the <head> of specific WordPress pages with configurable parameters.

== Description ==

**Opa! Suite - Webchat** is a lightweight WordPress plugin that seamlessly integrates the OpaSuite Webchat widget into your site. Configure your domain, token, and API parameters directly from the WordPress admin panel, and choose exactly which pages the script should load on.

**Features:**

* Inject the OpaSuite script into the `<head>` of any page or post.
* Admin settings panel with tabbed interface for easy configuration.
* Configure domain, token, and API endpoint parameters.
* Select individual pages and posts where the script should be active.
* Clean, minimal footprint — no bloat, no external dependencies.

== Installation ==

1. Upload the `opasuite-header-script` folder to the `/wp-content/plugins/` directory, or install directly through the WordPress plugin screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Navigate to **Settings > Opa! Suite** to configure your domain, token, and API parameters.
4. Select the pages where you want the Webchat widget to appear and save.

== Frequently Asked Questions ==

= Do I need an OpaSuite account to use this plugin? =

Yes. You need a valid OpaSuite account with a configured domain and token to use this plugin. Visit [opasuite.com.br](https://opasuite.com.br) for more information.

= Can I limit the script to specific pages only? =

Yes. The plugin provides a page selection interface in the settings panel so you can control exactly which pages load the Webchat widget.

= Is this plugin compatible with caching plugins? =

Yes. The script is injected server-side via WordPress hooks (`wp_head`), so it works correctly with most caching plugins.

== Screenshots ==

1. Admin settings panel — configure your OpaSuite domain, token, and API parameters.
2. Page selection interface — choose exactly which pages display the Webchat widget.

== Changelog ==

= 1.0.0 =
* Initial release.
* Admin settings panel with domain, token, and API configuration.
* Page-specific script injection.
* Tabbed admin interface.

== Upgrade Notice ==

= 1.0.0 =
Initial release. No upgrade steps required.
