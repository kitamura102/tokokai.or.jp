<?php
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
define('DB_NAME', 'v1034_wordpress_e');

/** MySQL database username */
define('DB_USER', 'v1034_wordpres_7');

/** MySQL database password */
define('DB_PASSWORD', '30IdSn!a8U');

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
define('AUTH_KEY',         'H1%7nb1dqtcnw&h(0PII@qkr*@LSI4PhzV^aco@xjAod9E&QTSaBRrx*9vo6OrML');
define('SECURE_AUTH_KEY',  'NOIG0*!ZQ0*)0TJ6W!nf^QFyerkSVwb8DKG5m1Dc@XlmS)(@kgPFSmlEj338QbU9');
define('LOGGED_IN_KEY',    '40D*1)w)Xs3uxfK1Uo8*N1Qg9biqVzlJFJ8mWQ(O%jD80PabonB0XsKH@wtXChyA');
define('NONCE_KEY',        'Y8Ly9CTy)HRc7se0eXRp*oUtR7gkdKoY^J&eXYlsxEXlc7PqvggncIP5F5uvqUa(');
define('AUTH_SALT',        'N5DC!aR8Zab^)H4Ur6FrZbb2JEYWL#3hhM9AZS6WR9cwJ7)*ip6ADXZ8aqUbKpJ1');
define('SECURE_AUTH_SALT', 'xSMzU2&jG&L@bn6704QgMYiQW2y8Lm4)r4#Rfaohn@lGj)6v8qI&ZEh^iPfRhi(N');
define('LOGGED_IN_SALT',   'wXwe&W6uo)jid^gacE2&u&6pK74IPA(g2@0FZB1Hhe%SreYZC*NDeMhJclarTUFG');
define('NONCE_SALT',       '9QZ6cVPnGH*lmi^b4Lr&hB)XD0bEI(1K8Fga^OVx8Wi35M(kRmv9st5eQBdvflXw');
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