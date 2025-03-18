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
define( 'DB_NAME', 'v1034_wp_jrkps' );

/** Database username */
define( 'DB_USER', 'v1034_wp_aojzz' );

/** Database password */
define( 'DB_PASSWORD', 'slNS^%dx8u!4ru8~' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define('AUTH_KEY', 'q:95g9979Mi9@Q(r2G))*32f3D;5R3;K@eL#1/-3H[@3ra06ST-q#G|jxo[3qETH');
define('SECURE_AUTH_KEY', 'H8C]F&_803&z887Ish0)%x8n1[5N]rRVNSe%(!88cw[L63p633%2%P+557A68)0&');
define('LOGGED_IN_KEY', ';I4*s5)G3B#//ES2))Q]@BE-d/|E]uzK0khks*#B/G6%C*w9Rjpq6_29S62eoJL(');
define('NONCE_KEY', '0A[%70y-%#3i+3QkWl2|(li7507e9P#v7N6W89RK+ZXOCOY0TUS0I8tT];]0F!dt');
define('AUTH_SALT', 'lmN#aJcvp5IYcZdf22c#9V[9%8j0!1%B4ebS6R_9My6|D!A8H(790![f7j4u9c#N');
define('SECURE_AUTH_SALT', '&B2OD9h3R0zPBMn22U96%!+SD/81])-v6ty;ZL3JFe3C&J]0N07B/+P)o0y]57]-');
define('LOGGED_IN_SALT', 'k_p:&*t~P9(M3:+]a;%E0Yo|C]O;:gC1qv#F&)1E)SxynZ10p66(A31dsVI8yUu2');
define('NONCE_SALT', '!8M*%-7c;DKp2tAl8:1T5c_HP5b4Q4U*TUW23@(Z89C0d)y7L-DZb5)*w8yc|lR(');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = '5QxkzDyA_';


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
