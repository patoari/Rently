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
define( 'DB_NAME', 'rently' );

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
define( 'AUTH_KEY',         'm[A*+EMELU0>6>UC7emp4xba|R>}?n{0$I*9U>8)YOnmQU|FFLi-aaFsYdq+#_9U' );
define( 'SECURE_AUTH_KEY',  'ZbU?o.thF%#ja;sO6>EfR:B-l-gpJL,;YGT)k   #JPUMu[20!G|WXo[kMH/5s4P' );
define( 'LOGGED_IN_KEY',    '`In`p40DTLR%Ti`93zc9XKA^xQ>Ke){!vS+Byt2Sl=TZv=tW8uY2Ivx)oNfv&1:s' );
define( 'NONCE_KEY',        '0terE<c>6/r*vp4gAm=m7_x5_:IG&o^kcaOdmi}.eBle@ogrrco]_hQv6h1;2jNw' );
define( 'AUTH_SALT',        'C:4>-^o2]1:(`p%I#XV+?[,SYa^Q#NIyf4jmB~H-RQ3IZ6-nY*+P5>{u?XVy1Akv' );
define( 'SECURE_AUTH_SALT', 'mceT72r=a+r/ORJT+31PQ=*0,8/Ls3Y:U=[);CSccZv#9}Q!t&0%elr,d6nC q9E' );
define( 'LOGGED_IN_SALT',   '[fp Lcf0xddQ%Mbi$k MQh/[T}aZUE4p7q-@Y4K3}-<`C>Aqj1(~,r*58a5>gdS)' );
define( 'NONCE_SALT',       'b+mcY-/AM1.5Y^i7^~8s>dpon?Ny-jpA}MsUPy>fm~{/1f~o]xwG(dJt2<KsnX1l' );

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
$table_prefix = 'rent_ly_';

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
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
