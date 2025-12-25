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
// Support Docker environment variables with fallback to local development values
/** The name of the database for WordPress */
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') ?: getenv('MYSQL_DATABASE') ?: 'local' );

/** Database username */
define( 'DB_USER', getenv('WORDPRESS_DB_USER') ?: getenv('MYSQL_USER') ?: 'root' );

/** Database password */
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: getenv('MYSQL_PASSWORD') ?: 'root' );

/** Database hostname */
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'localhost' );

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
define( 'AUTH_KEY',          '`Qy9;Oz[l[],g6ZeuHVG5EE7 ,8dL&f F/ipg+*uS^9gynyfimkz]D8qk<te}mO]' );
define( 'SECURE_AUTH_KEY',   'B;_){h:Rc8dV*a.mz#KRL;pi<tlknz~hEY4qF-Dp-HSgB_6FKds<{EIX,,@`.)^F' );
define( 'LOGGED_IN_KEY',     'Wj5usu6p3P Di&a{=$OpPit[pq!;Sn>2QV0W&QB<p2]0B={@/|sC?df@v8#G!-B+' );
define( 'NONCE_KEY',         'S=K0iS@:8?{V+![`;!_h/|heHu[-jLvWrjR`w3Z_WeR<KdjB`C`-$]N]|R=O{dQ<' );
define( 'AUTH_SALT',         'V3+!Bpng(4hT|TBWN:T&n4L,v8z,gGF:tx,mF$3oYC+p4#Vws|v6g7Vun$4{.{c{' );
define( 'SECURE_AUTH_SALT',  ')AEwZE5>/}0Rag%GFYb_JVRX!sP|9D[Oqb(4a/5hD XP@KO_A_!oJx&U^u]& pU:' );
define( 'LOGGED_IN_SALT',    'x16eP&YBk&sr3#&XY9;]&K+ L])QWcT@P[bvbk2/wZz:{kGFmc^I78p)k%i?sc<?' );
define( 'NONCE_SALT',        'p~e>UQm!D%rctLf!5PO-D:Dk~2)?vetU~EV(0Dt[`nvl_L#~/jNJ~<Dh!jq%KKjO' );
define( 'WP_CACHE_KEY_SALT', '):$%G7A%v0GD@+Vr~?Lj1{c27Wg1iZ] )b&9rMM}lyO,MsP CJcAW|L8R3mGnMkA' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv('WORDPRESS_TABLE_PREFIX') ?: 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
// Use environment variable WP_DEBUG or WORDPRESS_DEBUG, default to false for production
$wp_debug = getenv('WP_DEBUG') !== false ? filter_var(getenv('WP_DEBUG'), FILTER_VALIDATE_BOOLEAN) : (getenv('WORDPRESS_DEBUG') !== false ? filter_var(getenv('WORDPRESS_DEBUG'), FILTER_VALIDATE_BOOLEAN) : false);
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', $wp_debug );
}
define( 'WP_DEBUG_LOG', $wp_debug );
define( 'WP_DEBUG_DISPLAY', false );

// Set environment type based on WP_DEBUG or explicit WP_ENVIRONMENT_TYPE
$env_type = getenv('WP_ENVIRONMENT_TYPE') ?: ($wp_debug ? 'local' : 'production');
define( 'WP_ENVIRONMENT_TYPE', $env_type );

// RSS Smart Importer Configuration
// Set API host from environment variable or fallback to default
if (!defined('RSI_API_HOST')) {
  $rsi_api_host = getenv('RSI_API_HOST');
  if ($rsi_api_host) {
    define('RSI_API_HOST', $rsi_api_host);
  } else {
    // Fallback to local development value - change this for production
    define('RSI_API_HOST', 'https://context-gourmet-deliver-salon.trycloudflare.com');
  }
}
// Set Bearer token from environment variable or fallback to default
if (!defined('RSI_API_BEARER_TOKEN')) {
  $rsi_bearer_token = getenv('RSI_API_BEARER_TOKEN');
  if ($rsi_bearer_token) {
    define('RSI_API_BEARER_TOKEN', $rsi_bearer_token);
  } else {
    // Fallback to local development value - change this for production
    define('RSI_API_BEARER_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiYWRtaW4iLCJpYXQiOjE3NjYyNTMzOTQsImV4cCI6MTc2NjMzOTc5NH0.d3eVhFX3lzlNxvYmcveVPmoMKVUNHt6ZKYFkzzvUay4');
  }
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
