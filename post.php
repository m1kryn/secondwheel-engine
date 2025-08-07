<?php
// secondwheel-engine/post.php
$config = require __DIR__ . '/config.php';
date_default_timezone_set($config['timezone']);
require_once __DIR__ . '/lib/render.php';

$slug = $_GET['slug'] ?? '';
if ($slug === '') {
    http_response_code(404);
    echo "Not found";
    exit;
}

$post = sw_find_post_by_slug($config['posts_dir'], $slug);
if (!$post) {
    http_response_code(404);
    echo "Not found";
    exit;
}

$title = isset($post['meta']['title']) ? $post['meta']['title'] . ' – ' . $config['site_title'] : $config['site_title'];
include __DIR__ . '/templates/layout.php';
