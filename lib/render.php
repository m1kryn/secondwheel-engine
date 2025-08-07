<?php
// secondwheel-engine/lib/render.php
// Lightweight helpers for reading posts and rendering Markdown.

require_once __DIR__ . '/Parsedown.php';

/**
 * Read a markdown file and return [meta, content].
 * Front matter is in simple YAML-like format between --- lines.
 */
function sw_read_markdown_with_front_matter(string $path): array {
    $raw = file_get_contents($path);
    if ($raw === false) {
        return [[], ''];
    }

    $meta = [];
    $content = $raw;

    if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $raw, $m)) {
        $meta_block = trim($m[1]);
        $content = $m[2];
        foreach (preg_split('/\r?\n/', $meta_block) as $line) {
            if (strpos($line, ':') !== false) {
                [$k, $v] = array_map('trim', explode(':', $line, 2));
                // Remove surrounding quotes if present
                $v = preg_replace('/^["\'](.*)["\']$/', '$1', $v);
                $meta[$k] = $v;
            }
        }
    }

    return [$meta, $content];
}

function sw_render_markdown(string $markdown): string {
    static $pd = null;
    if ($pd === null) {
        $pd = new Parsedown();
        $pd->setBreaksEnabled(true);
    }
    return $pd->text($markdown);
}

/**
 * Discover posts as an array of associative arrays:
 * [
 *   'slug' => 'my-post',
 *   'path' => '/abs/path/to/file.md',
 *   'meta' => ['title' => '...', 'date' => 'YYYY-MM-DD', ...],
 *   'mtime' => 1234567890
 * ]
 * Strategy: recursively find *.md and \*\/index.md under posts_dir.
 * Slug is the filename without extension or the parent dir name for index.md.
 */
function sw_discover_posts(string $posts_dir): array {
    $posts = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($posts_dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        $name = $file->getFilename();
        $path = $file->getPathname();
        $slug = null;
        if (preg_match('/\.md$/i', $name)) {
            if (strtolower($name) === 'index.md') {
                $slug = basename(dirname($path));
            } else {
                $slug = preg_replace('/\.md$/i', '', $name);
            }
        } else {
            continue;
        }

        [$meta, $content] = sw_read_markdown_with_front_matter($path);
        $mtime = filemtime($path);

        $posts[] = [
            'slug'   => $slug,
            'path'   => $path,
            'meta'   => $meta,
            'mtime'  => $mtime,
        ];
    }

    // Sort posts by meta.date (desc) then mtime (desc)
    usort($posts, function($a, $b) {
        $ad = $a['meta']['date'] ?? '';
        $bd = $b['meta']['date'] ?? '';
        if ($ad !== '' && $bd !== '') {
            if ($ad === $bd) {
                return $b['mtime'] <=> $a['mtime'];
            }
            return strcmp($bd, $ad); // reverse (desc)
        }
        if ($ad !== '') return -1;
        if ($bd !== '') return 1;
        return $b['mtime'] <=> $a['mtime'];
    });

    return $posts;
}

function sw_find_post_by_slug(string $posts_dir, string $slug): ?array {
    // Try flat file: posts/<slug>.md
    $flat = $posts_dir . '/' . $slug . '.md';
    if (is_file($flat)) {
        [$meta, $content] = sw_read_markdown_with_front_matter($flat);
        return [
            'slug' => $slug,
            'path' => realpath($flat),
            'meta' => $meta,
            'content' => $content,
        ];
    }
    // Try nested index: posts/**/<slug>/index.md
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($posts_dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getFilename()) === 'index.md') {
            $parent = basename(dirname($file->getPathname()));
            if ($parent === $slug) {
                [$meta, $content] = sw_read_markdown_with_front_matter($file->getPathname());
                return [
                    'slug' => $slug,
                    'path' => realpath($file->getPathname()),
                    'meta' => $meta,
                    'content' => $content,
                ];
            }
        }
    }
    return null;
}

function sw_e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sw_format_date(string $date): string {
    $dt = date_create($date);
    if (!$dt) return $date;
    return $dt->format('M j, Y');
}
