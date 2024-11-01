<?php
/*
 * Plugin Name: WP Login Log
 * Plugin URI: http://www.github.com/nrosvall/wp-login-log
 * Description: Log all login attempts to a file
 * Version: 0.2
 * Author: Niko Rosvall
 * Author URI: http://www.byteptr.com
 * License: GPL3
 */

/*
 * Copyright 2015-2016 Niko Rosvall (niko@byteptr.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( ! class_exists( 'WP_Login_Log' ) ) :

class WP_Login_Log {
  public static $instance;

  public static function init() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new WP_Login_Log();
    }
    return self::$instance;
  }

  private function __construct() {
    $this->logfile = defined( 'WP_LOGIN_LOG' ) ?
      WP_LOGIN_LOG : implode( DIRECTORY_SEPARATOR, array( WP_CONTENT_DIR, 'wp-login.log' ) );

    add_action('wp_login', array( $this, 'on_login_success' ) );
    add_action('wp_login_failed', array( $this, 'on_login_failure') );
  }

  /*
   * Function writes a log to a file wp-content/wp-login.log.
   * Format of the log file is following:
   *
   *   <yyyy-MM-dd HH:mm:ss> <username> <status> <newline>
   *
   * <status> is either success or fail depending whether
   * login attempt was successful or not. Timestamp
   * is written in 24-hour format. Sections are separated
   * by a tab character.
   *
   */
  private function write_log_entry( $username, $status ) {
    $time_now = date('Y-m-d H:i:s');
    $message = "$time_now\t$username\t$status\n";

    error_log($message, 3, $this->logfile);
  }

  function on_login_success( $username ) {
    $this->write_log_entry( $username, 'success' );
  }

  function on_login_failure( $username ) {
    $this->write_log_entry( $username, 'fail' );
  }
}

endif;

// init the plugin
WP_Login_Log::init();
