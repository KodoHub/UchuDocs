<?php

namespace Documentation\interfaces;

class Markdown implements MarkdownInterface {
    public function parseFile(string $filePath): string {
        $content = file_get_contents($filePath);
        return $this->parseContent($content);
    }

    public function parseContent(string $content): string {
        // Basic Markdown parsing 
        // In a real implementation, you'd use a robust Markdown library like league/commonmark
        $html = htmlspecialchars($content);
        
        // Convert headings
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        
        // Convert bold
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        
        // Convert italic
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
        
        // Convert links
        $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $html);
        
        // Convert code blocks
        $html = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $html);
        
        return $html;
    }
}