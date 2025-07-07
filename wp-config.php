<?php
# Database Configuration
define( 'DB_NAME', 'wp_proclubswpestg' );
define( 'DB_USER', 'proclubswpestg' );
define( 'DB_PASSWORD', 'xSCIFjn4CxsNo54lw36V' );
define( 'DB_HOST', '127.0.0.1:3306' );
define( 'DB_HOST_SLAVE', '127.0.0.1:3306' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         '~fSdy-BK~,J;c|p0zF~D&.ZPch~R?11F&0w9W(}:|}[AtmSi{/<8Z=S4-oa_9=y)');
define('SECURE_AUTH_KEY',  'DF?#W?8nR<alOg7} w|m+4:Basu$pzHW;@3o+1WhfZ]UAi1n[+r7k^g)Bwv><$0+');
define('LOGGED_IN_KEY',    'b-Zw+nh+0/eb4B@KsL!gy^miHW2L|Ua~T}12=y,Dbo~Tm<A9/1d<?2qo8tPq,Dbn');
define('NONCE_KEY',        '<ig0fo3@#C:f1U6|ue&a>|lBr>P&[&yHwr?qfG6S8eKGFgU|(a}R8sw;Ga3pFH%a');
define('AUTH_SALT',        'RGN{d@jRHkfE|{AbTuJq~a;=6P?8f.J9s.pd6fCHuS=!C9[jbyOcXd)M|C>[a|+Q');
define('SECURE_AUTH_SALT', ';<EfCszv_niLiomt*ICxQ!/7;Ro/SDf-~%D5zw+?fN.!m;{C3Gw<t0>ECQ&MoOs*');
define('LOGGED_IN_SALT',   ')/Q/b(kOiYJJi@;,&1,:MY~W?PyAEY3{WtBFT}?yFWW-R4D*9/X2xZKVt={m15q ');
define('NONCE_SALT',       'v|dlJ+BdPRQ$|l+|lk.l1f&:LE=Dx,oNW(y0$`-q6_hA>!KBg1%rOm9edPFR!q6(');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'proclubswpestg' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'WPE_APIKEY', '6f9593da27ef304de24d44c033101a2464069d15' );

define( 'WPE_CLUSTER_ID', '400020' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_SFTP_ENDPOINT', '34.121.215.142' );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', false );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'proclubswpestg.wpengine.com', 1 => 'proclubswpestg.wpenginepowered.com', );

$wpe_varnish_servers=array ( 0 => '127.0.0.1', );

$wpe_special_ips=array ( 0 => '34.31.57.97', 1 => 'pod-400020-utility.pod-400020.svc.cluster.local', );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( 'default' =>  array ( 0 => 'unix:///tmp/memcached.sock', ), );
define('WPLANG', '');

# WP Engine ID


# WP Engine Settings
define( 'WP_MEMORY_LIMIT', '512M' );



# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', __DIR__ . '/');
require_once(ABSPATH . 'wp-settings.php');
