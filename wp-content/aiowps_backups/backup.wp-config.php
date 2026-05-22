<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'v1034_wordpress_8' );
/** Database username */
define( 'DB_USER', 'v1034_wordpres_5' );
/** Database password */
define( 'DB_PASSWORD', '!2r4VyPoG7' );
/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );
/** Database charset to use in creating database tables. */
define( '', 'utf8' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/*20251027 追加*/
define( 'WP_MEMORY_LIMIT', '512M' );
/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '_xpEe|3fo5h(DAWPC4A7GUj7m@!u4v2p8KceUJJ5s3!]%i#!53t+1;]+3_q5E~cD');
define('SECURE_AUTH_KEY', 'F0]OSq|g+9:(F:]_C2_#[8_E2U#3(9-G!aUE6*9q55@dEZBkMiT1)_&c+3#H1wrn');
define('LOGGED_IN_KEY', 'MUV1+FFC77n#8E768846I&~c|5[2FF-~~a[E2]UYIU]Qyd/YALz5IV&]0nu4z8O0');
define('NONCE_KEY', 'U_K7iw5H(9;DE)/%D21DPuci&/!R4uL-0bRs-pOZ)/S6L-_5+b6AZy#E~1Yolrft');
define('AUTH_SALT', 'ZdKW8)1dm6766&O+YH:9v#y7xYU;4*Am[i_Cf5W[euD*E9++!t+7hy)g+yKl-X2+');
define('SECURE_AUTH_SALT', '/ku_t*b07F5N13;q3v:G]OPEF0oF0ZDu2*nge7/:WN:/~+~vuC1d%G4FNf5s-b~8');
define('LOGGED_IN_SALT', '|PCg63O]%Qm7G5Ggw8vnI&2[Wt4;L|149!W%6BZ+063d_5Bev%j@870bhp102yW[');
define('NONCE_SALT', '8#(wAyd:Bbe6%3VcKm/O(dxons58M6mFN#3;H/C6~oO81bJa6H6;6b5(SD8_4e]8');
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wOtckM_';
/* Add any custom values between this line and the "stop editing" line. */
define('WP_ALLOW_MULTISITE', true);
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}
define( 'DISALLOW_FILE_EDIT', false );
define( 'CONCATENATE_SCRIPTS', false );
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** ↓↓↓ 2025/6/5 マルチサイト化のため設定を追加 ↓↓↓ **/
/*define('WP_ALLOW_MULTISITE', true);*/
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'tokokai.or.jp' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
/** ↑↑↑ 2025/6/5 マルチサイト化のため設定を追加 ↑↑↑ **/
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';