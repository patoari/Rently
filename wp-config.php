<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'Rently' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'DtrMZ<8x*FitTe>0xV]<9$-k;rW:-h&mb]>yB,r`unKMhNjTAd=*>3w=~fnT.^@T' );
define( 'SECURE_AUTH_KEY',  ' 5XUb5~cQXyJ|.8GZdT{a U82rei.].C&a`-7b:dBt#+&cXo&8~BJUkXL~Ug`7<E' );
define( 'LOGGED_IN_KEY',    'SY1SE3E5cUz&9#xZ~~SMxt+[BTK)7{6dy4bn?|`A]opH#921*cAs9)<$P%&(0*/n' );
define( 'NONCE_KEY',        'F>d6ZV(ucm9k7Eu0+o!)^p=idB7W+V{VCJsGU+>>#7th`ZE]fDA-%| o&{kG0HqG' );
define( 'AUTH_SALT',        '*i2 ug>Sixu1U%5WCGg3:]/6j1cPP!Mv0pda% q0>:FE]y@Qv:]i~l/ujJ [-sRI' );
define( 'SECURE_AUTH_SALT', '&^jri]WdJsJ(shdR@}$?-/%{4_* ##A8$P]>m8v Guj^3U.Y@19W(qd58G ziyQq' );
define( 'LOGGED_IN_SALT',   '2C2w#l3$EQLAyhY7IR-~X;V3&Y>jYMgb9e9bE;hupyj]p#Aq9R(taKRrk[!c+jeT' );
define( 'NONCE_SALT',       'YgT,wz]vKWJ><-/&&vMS|SGzX6rT4aY^:Y{vE5^XK=-:`pHcu/D@l$9# bQ0c8FT' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'ap_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
