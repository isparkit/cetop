<?php
define('DB_NAME', 'wordpress');
define('DB_USER', 'wordpress');
define('DB_PASSWORD', 'wordpress');
define('DB_HOST', 'db');

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('WP_ENVIRONMENT_TYPE', 'staging');

define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

define('FS_METHOD', 'direct');

$table_prefix = 'wp_';

if ( ! defined('ABSPATH') ) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
