<?php
define('WP_AUTO_UPDATE_CORE', 'minor');// この設定は WordPress Toolkit で WordPress アップデートを適切に管理するために必要です。今後、この WordPress ウェブサイトを WordPress Toolkit で管理しない場合、この行を削除してください。
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'v1034_wordpress_8');

/** MySQL database username */
define('DB_USER', 'v1034_wordpres_5');

/** MySQL database password */
define('DB_PASSWORD', '!2r4VyPoG7');

/** MySQL hostname */
define('DB_HOST', 'localhost:3306');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'PZ)^N39a2D9MLFTs8&lBji0rGKs^EdaRn6f)Kobby&8)FH*zfzuWkzjW*@MS7cj#');
define('SECURE_AUTH_KEY',  'EjsU!oIe^xFRJEJ6@op5!eP5gwn51kngKTs55BsVPflQfW3B5x^tYrqgPFyYah3R');
define('LOGGED_IN_KEY',    'h%)mesoVlF#!6A!MVJeI6)%KjlzATB(Ww!wy1F%hv@y9Pk6CDoEU0hEYt*Y&Ma^8');
define('NONCE_KEY',        'iyQk*abAcA%v#lLHAg3!D%ZwW99FIpTes#(yqIpqt!1uO@ETKRpRynW*nRE@zGNM');
define('AUTH_SALT',        'bI7EcaLkvGvJX#4!HVWJ1Ud%Yx!oVCAhsFzvb4NO)qRVnm3ZFE!tKFdvm^Zi3(72');
define('SECURE_AUTH_SALT', 'n^Xl)Iv(i8(2O56BE4bn5%HH@&adX%rPmcfyMu&&5Tih!VwrtO&9*JWn9Zt#ayBd');
define('LOGGED_IN_SALT',   'V3Yu7Wh0bQGRaF&(t9K64uhVB8YKwuVSDjXi0(MK%G4q^1q*9Xhk8TbRdqzFtkzk');
define('NONCE_SALT',       'qB9oR&o4ThDdVV7BfhOaH%ctc%PhGIWaha0EDn)(mRQWsVWAfs8Q@tGxz!O!dJFr');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define( 'WP_ALLOW_MULTISITE', true );

define ('FS_METHOD', 'direct');
?>