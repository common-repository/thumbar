<?php
/*
Plugin Name: Thumbar
Plugin URI: https://www.thumbar.com
Description: Rates your posts and mirrors them and their ratings in real time for free at Thumbar.com to maximize their exposure. Replaces search engines and SEO for generating traffic.
Version: 3.000
Author: Thumbar, Inc.
Author URI: https://www.thumbar.com
*/

add_filter('the_excerpt', 'thumbar');
add_filter('the_content', 'thumbar');
add_action('init', 'register_tb_script');
add_action('wp_footer', 'print_tb_script');

add_action('admin_init', 'tb_options_init');
add_action('admin_menu', 'tb_options_add_page');

// Init plugin options to white list our options
function tb_options_init(){
  register_setting('wp_tb_options', 'tb_option', 'tb_options_validate');
}

// Add menu page
function tb_options_add_page() {
  add_options_page('Thumbar Settings', 'Thumbar', 'manage_options', 'tb_options', 'tb_options_do_page');
}

// Draw the menu page
function tb_options_do_page() {
    ?>
    <div class="wrap">
      <h2>Thumbar Settings</h2>
    <form method="post" action="options.php">
      <?php settings_fields('wp_tb_options'); ?>
      <?php $options = get_option('tb_option'); ?>
      <table class="form-table">
        <tr valign="top"><th scope="row">Thumbar User ID</th>
          <td><input type="text" name="tb_option[uid]" value="<?php echo $options['uid'] ? $options['uid'] : 'x'; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">Up Bar Color</th>
          <td><input type="text" name="tb_option[uc]" value="<?php echo $options['uc'] ? $options['uc'] : '99cc99'; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">Down Bar Color</th>
          <td><input type="text" name="tb_option[dc]" value="<?php echo $options['dc'] ? $options['dc'] : 'ff9999'; ?>" /></td>
        </tr>
      </table>
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
    </form>
  </div>
  <?php  
}

// Sanitize input array and return sanitized array
function tb_options_validate($input) {
  // Our first value is either 0 or 1
  $input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
    
  // Say our second option must be safe text with no HTML tags
  $input['sometext'] =  wp_filter_nohtml_kses($input['sometext']);
  
  return $input;
}

function thumbar($content) {
  global $add_tb_script;
  global $wp_query;

  $options = get_option('tb_option');

  $uid = $options['uid'];
  $uc  = $options['uc'];
  $dc  = $options['dc'];
  $post = $wp_query->post;
  $id = $post->ID;
  $shim = is_singular() ? '' : '<br>';
  
  $thumbar_add_on = '<style type="text/css"> .thumbar {margin:0px;border:0px;padding:0px;width:150px;height:25px;}</style>'.$shim.'<div class="thumbar" uid='.$uid.' uc="'.$uc.'" dc="'.$dc.'" id="tb-post-'.$id.'"></div><br>';
  $theContent = $content.$thumbar_add_on;
  $add_tb_script = true;

  return ($theContent);
}

function register_tb_script() {
  wp_register_script('tb-script', 'https://www.thumbar.com/js/wp_link.js', array('jquery'), '1.0', true);
}

function print_tb_script() {
  global $add_tb_script;

  while (!$add_tb_script)
    return;

  wp_print_scripts('tb-script');
}
?>