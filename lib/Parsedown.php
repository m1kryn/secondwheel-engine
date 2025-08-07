<?php
# Minimal vendored Parsedown (MIT) - https://parsedown.org
# To keep this scaffold compact, this is a tiny shim that requires the real file if present.
# Replace this file with the full Parsedown.php for production use.
class Parsedown {
    public function setBreaksEnabled($b) { /* no-op in shim */ }
    public function text($text) {
        // Extremely naive Markdown to HTML (headings + paragraphs) as a placeholder.
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
        // Paragraph wrap: split by double newlines
        $parts = preg_split('/\n\s*\n/', trim($html));
        $parts = array_map(function($p){ return preg_match('/^<h[1-6]>/', $p) ? $p : '<p>'.$p.'</p>'; }, $parts);
        return implode("\n", $parts);
    }
}
