<?php
/*
Plugin Name: JinX - The Javascript Includer
Plugin URI: http://www.jqueryin.com/projects/jinx-javascript-includer-wordpress-plugin/
Description: JinX gives you the ability to separate any javascript you may have from your blog posts and pages.  It provides a separate textarea for adding javascript code which will not be stripped or sanitized.
Version: 1.1.2
Author: Corey Ballou
Author URI: http://www.jqueryin.com/
*/

#
#  Copyright (c) 2010 Corey Ballou (email: webmaster@jqueryin.com)
#
#  This file is part of JinX
#
#  JinX is free software; you can redistribute it and/or modify it under
#  the terms of the GNU General Public License as published by the Free
#  Software Foundation; either version 2 of the License, or (at your option)
#  any later version.
#
#  JinX is distributed in the hope that it will be useful, but WITHOUT ANY
#  WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
#  FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
#  details.
#
#  You should have received a copy of the GNU General Public License along
#  with JinX; if not, write to the Free Software Foundation, Inc.,
#  51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA
#

class Jinx {

    protected $pluginurl;
	protected $defaultOptionVals;

    function __construct()
    {
    	$this->pluginurl = WP_PLUGIN_URL . '/jinx-the-javascript-includer/';
    }

	public function init()
	{
		// restrict access to admin section and selected user roles
		if (is_admin() && $this->hasPluginAccess()) {
			// add an admin options menu
			add_action('admin_menu', array(&$this, 'admin_menu'));

			// register markitup
			add_action('admin_init', array(&$this, 'jinx_admin_init'));

			// add javascript to admin
			add_action('admin_head', array(&$this, 'load_headers'), 1000);

			// add custom box to admin
			add_action('admin_menu', array(&$this, 'add_custom_box'), 1000);

			// watch for post submisions
			add_action('edit_post', array(&$this, 'submit_meta_tag'));
			add_action('publish_post', array(&$this, 'submit_meta_tag'));
			add_action('save_post', array(&$this, 'submit_meta_tag'));
			add_action('edit_page_form', array(&$this, 'submit_meta_tag'));
		}

		// add javascript to page and blog posts
		add_filter('the_content', array(&$this, 'add_javascript_to_post'), 9999);
	}

	public function hasPluginAccess()
	{
		global $user_ID;

		// specify the default roles which have access to the plugin
		$this->defaultOptionVals = array('administrator');

		// get all current option values and override defaults
		$options = get_option('jinx_roles');
		if (!empty($options)) {
			$this->defaultOptionVals = array_merge($this->defaultOptionVals, $options, array('administrator'));
		}

		// ensure we have a logged in user
		if (!empty($user_ID)) {
			$user = new WP_User($user_ID);
			if (!is_array($user->roles)) $user->roles = array($user->roles);
			foreach ($user->roles as $role) {
				if (in_array($role, $this->defaultOptionVals)) {
					return true;
				}
			}
		}

		return false;
	}

   /**
    * Admin menu entry.
    *
    * @access	public
    */
   public function admin_menu()
   {
		if (function_exists('add_options_page')) {
			$id = add_options_page('JinX Options', 'JinX Options', 10, basename(__FILE__), array(&$this, 'admin_options'));
		}
   }

   /**
    * Options page.
    *
    * @access	public
    */
   public function admin_options()
   {
		// contains the array of all user roles
		$roles = new WP_Roles();
		$roles = array_keys($roles->role_names);

		// watch for form submission
		if (!empty($_POST['jinx_roles'])) {

		   // validate the referer
		   check_admin_referer('jinx_options_valid');

			if (empty($_POST['jinx_roles'])) {
			  echo '<div id="message" class="updated fade"><p><strong>' . __('You must select at least one role for this application to be properly enabled.') . '</strong></p></div>';
			  return false;
			}

			// update the new value
			$this->defaultOptionVals['roles'] = $_POST['jinx_roles'];

		   // update options settings
		   update_option('jinx_roles', $this->defaultOptionVals);

		   // show success
		   echo '<div id="message" class="updated fade"><p><strong>' . __('Your configuration settings have been saved.') . '</strong></p></div>';

		}

		// display the admin options page
?>

<div style="width: 620px; padding: 10px">
	<h2><?php _e('Me Likey Options'); ?></h2>
	<form action="" method="post" id="me_likey_form" accept-charset="utf-8" style="position:relative">
		<?php wp_nonce_field('jinx_options_valid'); ?>
		<input type="hidden" name="action" value="update" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">User Role Restriction*</th>
				<td>
					<select name="jinx_roles[]" id="jinx_roles" multiple="multiple" size="10">
						<?php
						if (!empty($roles)):
							foreach ($roles as $role):
								echo '<option value="' . $role . '"' . (in_array($role, $this->defaultOptionVals['roles']) ? ' selected="selected"' : '') . '>' . $role . '</option>';
							endforeach;
						endif;
						?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td>Please select all user roles from the multi-select that you wish to allow access to this plugin.</td>
			</tr>
			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td>
				   <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
				</td>
			</tr>
		</table>
	</form>
</div>

<?php
   }

    /**
     * Registers the MarkItUp jQuery plugin
     */
    function jinx_admin_init() {
        wp_register_script('markItUp', $this->pluginurl . 'js/jquery.markitup.pack.js');
    }

    /**
     * Adds markItUp CSS to the admin header.
     */
    function load_headers() {
		echo '<link type="text/css" rel="stylesheet" href="' . $this->pluginurl . 'css/markitup.css"></link>' . "\n";
        echo '<script type="text/javascript" src="' . $this->pluginurl . 'js/jquery.markitup.pack.js"></script>' . "\n";
    }

    /**
     * Stores the javascript code related to the post.
     */
    function submit_meta_tag($post_id) {
		if (!wp_verify_nonce($_POST['jinx_nonce'], plugin_basename(__FILE__)))
			return $post_id;
	    if ((isset($_POST['jinx_editmode']) && $_POST['jinx_editmode'] == 1)) {
            delete_post_meta($post_id, 'jinx_code');
            if (!empty($_POST['jinx_code'])) {
                add_post_meta($post_id, 'jinx_code', $_POST['jinx_code']);
            }
        }
    }

	/**
	 * Function to add custom advanced meta box to the admin pages.
	 */
	function add_custom_box() {
		add_meta_box('jinx', __('JinX - The Javascript Includer'), array(&$this, 'add_textarea'), 'page', 'advanced');
		add_meta_box('jinx', __('JinX - The Javascript Includer'), array(&$this, 'add_textarea'), 'post', 'advanced');
	}

    /**
     * Add textarea to the add/edit page and blog pages.
     */
    function add_textarea() {
        global $post;
	    if (is_object($post)) $post_id = $post->ID;
        else $post_id = $post;
        // load markitup editor
		wp_enqueue_script('jquery');
        wp_enqueue_script('markItUp');
        // get javascript
        $js = get_post_meta($post_id, 'jinx_code', true);
    ?>
		<a href="http://www.jqueryin.com/projects/jinx-javascript-includer-wordpress-plugin/" style="float:right; text-decoration:none" target="_blank"><?php _e('Click Here For Documentation') ?></a>
		<div style="clear:both">
			<input type="hidden" name="jinx_editmode" id="jinx_editmode" value="1" />
			<input type="hidden" name="jinx_nonce" id="jinx_nonce" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
			<textarea name="jinx_code" id="jinx_code" rows="10" cols="60" style="width: 100%"><?php echo $js; ?></textarea>
		</div>
        <script type="text/javascript">
        var markupSettings = {
            nameSpace:      "js",
			resizeHandle:	true,
            onShiftEnter:   {keepDefault:false, replaceWith:'<br />\n'},
            onCtrlEnter:    {keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
            onTab:          {keepDefault:false, openWith:'    '},
            markupSet: []
        };
        jQuery('#jinx_code').markItUp(markupSettings);
        </script>

    <?php
    }

    /**
     * Adds any javascript to the bottom of the page content.
     *
     * @access  public
     * @param   string  $html
     * @return  string
     */
    public function add_javascript_to_post($html = '') {
        global $post;
	    if (is_object($post)) $post_id = $post->ID;
        else $post_id = $post;
        $js = get_post_meta($post_id, 'jinx_code', true);
        if (!empty($js)) {
            $html .= "\n" . $js;
        }
        return $html;
    }

}

// load the class
$jinx = new Jinx();

// load the initializer method following Wordpress initialization
add_action('init', array(&$jinx, 'init'));
?>
