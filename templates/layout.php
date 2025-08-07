<?php
// secondwheel-engine/templates/layout.php
$base = $config['base_url'];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= sw_e($title) ?></title>
  <style>
    :root { --fg:#111; --bg:#fff; --muted:#666; --link:#0645ad; }
    @media (prefers-color-scheme: dark) {
      :root { --fg:#eaeaea; --bg:#111; --muted:#aaa; --link:#9ec1ff; }
    }
    body { margin: 2rem auto; max-width: 720px; font: 16px/1.6 system-ui, -apple-system, Segoe UI, Roboto, sans-serif; color: var(--fg); background: var(--bg); }
    header { margin-bottom: 2rem; }
    a { color: var(--link); text-decoration: none; }
    a:hover { text-decoration: underline; }
    .muted { color: var(--muted); }
    .post-list article { margin: 1.25rem 0; }
    .post-list h2 { margin: 0 0 .25rem 0; font-size: 1.3rem; }
    .post-view h1 { margin: 0 0 .5rem 0; }
  </style>
</head>
<body>
<header>
  <h1 style="margin:0;"><a href="<?= sw_e($base ?: '/') ?>"><?= sw_e($config['site_title']) ?></a></h1>
</header>
<main>
<?php if (isset($posts)): ?>
  <section class="post-list">
    <?php foreach ($posts as $p): 
      $meta = $p['meta'];
      $slug = $p['slug'];
      $title = $meta['title'] ?? $slug;
      $date  = $meta['date'] ?? '';
    ?>
      <article>
        <h2><a href="<?= sw_e(($base ?: '') . '/post.php?slug=' . urlencode($slug)) ?>"><?= sw_e($title) ?></a></h2>
        <?php if ($date): ?><div class="muted"><?= sw_e(sw_format_date($date)) ?></div><?php endif; ?>
        <?php if (!empty($meta['summary'])): ?><p><?= sw_e($meta['summary']) ?></p><?php endif; ?>
      </article>
    <?php endforeach; ?>
    <?php if (empty($posts)): ?>
      <p class="muted">No posts yet.</p>
    <?php endif; ?>
  </section>
<?php else: /* single post view */ ?>
  <?php
    $meta = $post['meta'] ?? [];
    $title = $meta['title'] ?? $post['slug'];
    $date  = $meta['date'] ?? '';
    $html  = sw_render_markdown($post['content'] ?? '');
  ?>
  <article class="post-view">
    <h1><?= sw_e($title) ?></h1>
    <?php if ($date): ?><div class="muted"><?= sw_e(sw_format_date($date)) ?></div><?php endif; ?>
    <div><?= $html ?></div>
  </article>
<?php endif; ?>
</main>
</body>
</html>
