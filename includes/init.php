<?php
/**
 * RxHub Bootstrap File
 * Include this file to load all necessary components
 */

// Prevent direct access
define('RXHUB_LOADED', true);

// Load core files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';
