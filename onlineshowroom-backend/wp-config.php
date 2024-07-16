<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache





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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

//Using environment variables for memory limits
$wp_memory_limit = (getenv('WP_MEMORY_LIMIT') && preg_match("/^[0-9]+M$/", getenv('WP_MEMORY_LIMIT'))) ? getenv('WP_MEMORY_LIMIT') : '128M';
$wp_max_memory_limit = (getenv('WP_MAX_MEMORY_LIMIT') && preg_match("/^[0-9]+M$/", getenv('WP_MAX_MEMORY_LIMIT'))) ? getenv('WP_MAX_MEMORY_LIMIT') : '256M';

/** General WordPress memory limit for PHP scripts*/
define('WP_MEMORY_LIMIT', $wp_memory_limit );

/** WordPress memory limit for Admin panel scripts */
define('WP_MAX_MEMORY_LIMIT', $wp_max_memory_limit );


// URL parameters
define('WP_ENVIRONMENT_TYPE', 'production');
define('WP_BASE_URL_DEV', 'http://wordpress:8080/onlineshowroom-backend/');
define('WP_BASE_URL_PROD', 'https://skbt-main.digi-team.work/onlineshowroom-backend/');


//Using environment variables for DB connection information

// ** Database settings - You can get this info from your web host ** //
$connectstr_dbhost = getenv('DATABASE_HOST');
$connectstr_dbname = getenv('DATABASE_NAME');
$connectstr_dbusername = getenv('DATABASE_USERNAME');
$connectstr_dbpassword = getenv('DATABASE_PASSWORD');

/** The name of the database for WordPress */
define('DB_NAME', $connectstr_dbname);

/** MySQL database username */
define('DB_USER', $connectstr_dbusername);

/** MySQL database password */
define('DB_PASSWORD',$connectstr_dbpassword);

/** MySQL hostname */
define('DB_HOST', $connectstr_dbhost);

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Enabling support for connecting external MYSQL over SSL*/
$mysql_sslconnect = (getenv('DB_SSL_CONNECTION')) ? getenv('DB_SSL_CONNECTION') : 'true';
if (strtolower($mysql_sslconnect) != 'false' && !is_numeric(strpos($connectstr_dbhost, "127.0.0.1")) && !is_numeric(strpos(strtolower($connectstr_dbhost), "localhost"))) {
	define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'WX$6S[&+QmkB(N Y^r_q7_0&Fq}({r&p)FtkCDrOe7`gb]LupW)r|arC>0mDQ.g~' );
define( 'SECURE_AUTH_KEY',  '5i(V>+metJ*X3P?U&,D,0dR/r2SE&+gW8DEVg$k{+-983_j8/hDFUavgIMti[wNi' );
define( 'LOGGED_IN_KEY',    '!RWG*)<]<1U5,)0|H[|l[L:AO3#y$oB-{OF5U`$4u!R13jRfWC!yN;|L$l-@,5j^' );
define( 'NONCE_KEY',        'f2t>hgFL&Z~L?Mhz]AxC+{(H-ntT?/,ejEKE)XoZdyd$;|U!A{[!d2a4UN^HBb)Q' );
define( 'AUTH_SALT',        '^kcINtyl>BN,CV?2+w0jt;4,t1hp?g#1)CosPyXuz0V0_T,VPb!h?+Qs^.{nvNi%' );
define( 'SECURE_AUTH_SALT', 'f.pQq2}XL;:(DoN^8hXG;s;DVmgY[49Q=cy)%2BW_*MJfrI,*5~#hg4WWj!(2>zk' );
define( 'LOGGED_IN_SALT',   '7h[f9]kgS|{`arKI[`F4r{RK*G}O0@:vnejx}mU(3j%9vy.[}jCm&,^ugxuhH88#' );
define( 'NONCE_SALT',       'Np%,p=Dh(ZrffhL3/v7#J%/56Z)F2C0@iMLDPHDx#L!qc=X3UMV%w<Oka[i7qpl|' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy blogging. */
/**https://developer.wordpress.org/reference/functions/is_ssl/ */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
	$_SERVER['HTTPS'] = 'on';

$http_protocol='http://';
if (!preg_match("/^localhost(:[0-9])*/", $_SERVER['HTTP_HOST']) && !preg_match("/^127\.0\.0\.1(:[0-9])*/", $_SERVER['HTTP_HOST'])) {
	$http_protocol='https://';
}

//Relative URLs for swapping across app service deployment slots
#define('WP_HOME', $http_protocol . $_SERVER['HTTP_HOST']);
#define('WP_SITEURL', $http_protocol . $_SERVER['HTTP_HOST']);
#define('WP_CONTENT_URL', '/wp-content');
#define('DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST']);
$_SERVER['HTTP_HOST'] = getenv("SKBT_HTTP_HOST");
if(strstr(getenv("SKBT_HTTP_HOST"),".local")){
	$http_protocol='http://';
}
define('WP_HOME', $http_protocol.getenv("SKBT_HTTP_HOST").getenv("SKBT_SUBFOLDER"));
define('WP_SITEURL', WP_HOME);
define('WP_CONTENT_URL', WP_HOME.'/wp-content');
define('WP_PLUGIN_URL', WP_HOME.'/wp-content/plugins');
define('DOMAIN_CURRENT_SITE', getenv("SKBT_HTTP_HOST"));
define('COOKIE_DOMAIN', getenv("SKBT_HTTP_HOST"));
// define('WP_POST_REVISIONS', false);
define('NOBLOGREDIRECT', WP_HOME);

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
