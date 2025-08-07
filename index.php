<?php
// secondwheel-engine/index.php
$config = require __DIR__ . '/config.php';
date_default_timezone_set($config['timezone']);
require_once __DIR__ . '/lib/render.php';

$posts = sw_discover_posts($config['posts_dir']);

$title = $config['site_title'];
include __DIR__ . '/templates/layout.php';
