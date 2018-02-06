<?php
/**
 * Version:           1.0
 * @wordpress-plugin
 * Plugin Name:       Befive Plugin Activation Hook Example
 * Plugin URI:        http://play.befive.info/
 * Description:       This is a example plugin that you cannot activate. This was made to demonstrate how to use the plugin activation hook.
 * Author:            Shu Miyao
 * Author URI:        http://play.befive.info/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       befive-plugin-activation-hook-example
 */

 /* If this file is called directly, abort. */
if (!defined('WPINC')) {
    die;
}

/** Activation hook  to check up plugin compatibility */
	/**
	 * A hook to excute something when a plugin as specifed by the 1st parameter (file location) is activated.
	 * Second paramter is a function to be run.
	 *
	 * @uses       register_activation_hook()	https://codex.wordpress.org/Function_Reference/register_activation_hook
	 *
	 * @param		string		$file		A full path (__FILE__) is also accepted.
	 * @param		callback	$function	A name in string or function to run.
	 */
	register_activation_hook(__FILE__, function () {
		/**
		 * Get plugin data
		 *
		 * @see        get_plugin_data() 	https://codex.wordpress.org/Function_Reference/get_plugin_data
		 * @return     array
		 */
		$_plugin_data = get_plugin_data(__FILE__, FALSE, FALSE);
		/* *
		 * Get the plugin version from the data we got from above.
		 */
		$_plugin_version = $_plugin_data['Version'];
		/**
		 * Create transient data only if a condition is met.
		 * Here in this example the plugin version (1.0) is lower than 2.0,
		 * so the plugin cannot be activated.
		 */
		if (version_compare($_plugin_version,'2.0', '>=') === FALSE ){
			/**
			 * Use Transient API to pass over a value.
			 * Here we want to carry over an information that this plugin should not be activated.
			 * Keep reminded that the data can exisit only for the next 5 seconds (the 3rd paramter).
			 *
			 * @uses		set_transient()		https://codex.wordpress.org/Function_Reference/set_transient
			 *
			 * @param		string		$transient
			 * @param		mixed		$value
			 * @param		int  		$expiration		Time until expiration in second.
			 */
			set_transient('be-admin-notice-activation-example-plugin-cannot-be-activated', true, 5);
		};
	}, 9999);

	/**
	 * We want to show a box notice when we want to inform users.
	 * The admin_notices is the action hook to use when we want to interrupt the process to show messages in the top of admin page.
	 *
	 * @uses       admin_notices hook			https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 */
	add_action('admin_notices', function () {
		/**
		 * Check transient, if available display notice.
		 *
		 * @uses		set_transient()		https://codex.wordpress.org/Function_Reference/get_transient
		 */
		if (get_transient('be-admin-notice-activation-example-plugin-cannot-be-activated')):
			/**
			 * Refer to the following for more examples of how html output should be.
			 * @see 		admin_notices hook			https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
			 */
		?>
			<div class="error">
				<p><strong><?php echo esc_html__('Befive Plugin Activation Hook Example could not be activated.', 'befive-plugin-activation-hook-example'); ?></strong> <?php echo esc_html__('You can change the behavior by changing the version number of version_compare() in the code.', 'befive-plugin-activation-hook-example'); ?></p>
			</div>
		<?php
		/**
		 * Deactivate this plugin here. If you try to deactivate within the register_activation_hook(), the plugin will not be deactivated.
		 *
		 * @see        deactivate_plugins()		https://codex.wordpress.org/Function_Reference/deactivate_plugins
		 */
		deactivate_plugins(__FILE__);
		/**
		 * Making sure that Plugin Activated message will not be shown. If this is forgotten, "Plugin Activated." message will be shown.
		 * We do not really have to worry in case of 'activate-multi'.
		 *
		 * @see        https://github.com/WordPress/WordPress/blob/master/wp-admin/plugins.php#L525
		 */
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}
		endif;
	});
