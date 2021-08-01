
<?php

/*
 * Plugin Name: Piwigo Login admin page
 * Description: Adds a custom admin pages For the Piwigo login plugin.
 * Version: 1.0.0
 * Date: 29-07-2021
 * Author: Rob Visser
 * Text Domain: my-custom-admin-page
*/

function pwg_admin_menu() {
    add_menu_page(
        __( 'Piwigo login', 'my-textdomain' ),
        __( 'Piwigo menu', 'my-textdomain' ),
        'manage_options',
        'pwglogin-page',
        'pwg_admin_page_contents',
        'dashicons-schedule',
        90
    );
}
add_action( 'admin_menu', 'pwg_admin_menu' );

function pwg_admin_page_contents() {
    ?>
    <h1> <?php esc_html_e( 'Login to PIWIGO.', 'my-plugin-textdomain' ); ?> </h1>
    <form method="POST" action="options.php">
    <?php
    settings_fields( 'pwglogin-page' );
    do_settings_sections( 'pwglogin-page' );
    submit_button();
    ?>
    </form>
    <?php
}


add_action( 'admin_init', 'my_settings_init' );

function my_settings_init() {

    add_settings_section(
        'pwglogin_page_setting_section',
        __( 'Wordpress to Piwigo login settings', 'my-textdomain' ),
        'my_setting_section_callback_function',
        'pwglogin-page'
    );

		add_settings_field(
		   'pwg_url_field',
		   __( 'URL of the piwigo site', 'my-textdomain' ),
		   'pwg_url_markup',
		   'pwglogin-page',
		   'pwglogin_page_setting_section'
		);
		register_setting( 'pwglogin-page', 'pwg_url_field' );

		add_settings_field(
		   'pwg_admin_user_field',
		   __( 'admin user of the piwigo site', 'my-textdomain' ),
		   'pwg_admin_user_markup',
		   'pwglogin-page',
		   'pwglogin_page_setting_section'
		);
		register_setting( 'pwglogin-page', 'pwg_admin_user_field' );

		add_settings_field(
		   'pwg_admin_password_field',
		   __( 'admin password of the piwigo site', 'my-textdomain' ),
		   'pwg_admin_password_markup',
		   'pwglogin-page',
		   'pwglogin_page_setting_section'
		);
		register_setting( 'pwglogin-page', 'pwg_admin_password_field' );


		
}


function my_setting_section_callback_function() {
	echo '<p>Enter the required settings that enables passwordless login to PIWIGO</p>
              <p>Add the shortcode <b>PIWIGO_LOGIN</b> between square brackets on a page or in a text widget</p>';
}


function pwg_url_markup() {
    ?>
    <label for="my-input"><?php _e( 'URL    :' ); ?></label>
    <input type="text" id="pwg_url_field" name="pwg_url_field" size="80" value="<?php echo get_option( 'pwg_url_field' ); ?>">
    <?php
}


function pwg_admin_user_markup() {
    ?>
    <label for="my-input"><?php _e( 'name   :' ); ?></label>
    <input type="text" id="pwg_admin_user_field" name="pwg_admin_user_field" size="20" value="<?php echo get_option( 'pwg_admin_user_field' ); ?>">
    <?php
}


function pwg_admin_password_markup() {
    ?>
    <label for="my-input"><?php _e( 'passwrd:' ); ?></label>
    <input type="password" id="pwg_admin_password_field" name="pwg_admin_password_field" size="20" value="<?php echo get_option( 'pwg_admin_password_field' ); ?>">
    <input type="checkbox" onclick="myShowAdminPassword()">Show Admin Password
    <script>
    function myShowAdminPassword() {
      var x = document.getElementById("pwg_admin_password_field");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
    </script>
    <?php
}
/*
 * * 
 *get_option( 'pwg_url_field' );
 *
 */
