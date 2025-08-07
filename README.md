# secondwheel-engine

Minimal PHP blog engine that reads Markdown files from `posts/` and renders
index + post pages. No database, no admin UI.

## Quick start

1) Place your posts in `posts/` as either:
   - `posts/your-slug.md`
   - `posts/YYYY/MM/your-slug/index.md`

Each file may start with front matter:

```yaml
---
title: My First Post
date: 2025-08-07
summary: Optional one-liner for the index.
---
```

2) Edit `config.php` and set `base_url` if deploying to a domain.

3) Serve `index.php` and `post.php` from your document root, or move everything
   under `public/` and adjust paths accordingly. For pretty URLs, use
   `public/.htaccess` and route `/post/<slug>` to `post.php?slug=<slug>`.

## Parsedown

This scaffold includes a tiny shim of Parsedown for demo purposes.
Replace `lib/Parsedown.php` with the real library from https://parsedown.org
for production-quality Markdown rendering.
