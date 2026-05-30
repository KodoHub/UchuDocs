<?php

namespace Documentation;

use Exception;
use ParsedownExtra;
use JBZoo\MermaidPHP\Render;

class Markdown
{
    private $parsedown;
    private $cacheDirectory;
    private $cacheTTL;
    private $logger;
    private $useMetadata = false;
    private $mermaidEnabled = true;
    private $mermaidClass = 'mermaid';

    public function __construct(string $cacheDirectory = null, int $cacheTTL = 3600, bool $useMetadata = false)
    {
        $this->parsedown = new ParsedownExtra();
        $this->parsedown->setBreaksEnabled(true);
        $this->parsedown->setUrlsLinked(true);

        $this->parsedown->setMarkupEscaped(false);
        $this->parsedown->setSafeMode(false);

        $this->cacheDirectory = $cacheDirectory ?? __DIR__ . '/../cache/';
        $this->cacheTTL = $cacheTTL;
        $this->useMetadata = $useMetadata;

        $this->logger = new \Psr\Log\NullLogger();
    }

    /**
     * Parse Markdown content to HTML
     * 
     * @param string $markdownContent Raw Markdown text
     * @return string Parsed HTML content
     */
    public function parse(string $markdownContent): string
    {
        if ($this->mermaidEnabled) {
            $markdownContent = $this->processMermaidDiagrams($markdownContent);
        }

        $previousErrorReporting = error_reporting();

        // Suppress PHP 8.x warnings coming from ParsedownExtra
        error_reporting(
            $previousErrorReporting
            & ~E_DEPRECATED
            & ~E_WARNING
            & ~E_NOTICE
        );

        try {
            $html = $this->parsedown->text($markdownContent);
        } finally {
            error_reporting($previousErrorReporting);
        }

        $html = preg_replace_callback(
            '/<pre><code>(.*?)<\/code><\/pre>/s',
            function ($matches) {
                $code = htmlspecialchars_decode($matches[1]);

                return '<pre><code class="language-php">'
                    . htmlspecialchars($code)
                    . '</code></pre>';
            },
            $html
        );

        return $html;
    }

    /**
     * Process Mermaid diagram code blocks and convert them to appropriate HTML
     * 
     * @param string $content Markdown content
     * @return string Processed content with Mermaid diagrams rendered
     */
    private function processMermaidDiagrams(string $content): string
    {
        $pattern = '/```\s*mermaid\n(.*?)\n```/s';

        return preg_replace_callback($pattern, function ($matches) {
            $diagramContent = trim($matches[1]);

            try {
                return sprintf(
                    '<div class="%s">%s</div>',
                    htmlspecialchars($this->mermaidClass),
                    htmlspecialchars($diagramContent)
                );
            } catch (Exception $e) {
                $this->logger->error("Mermaid rendering failed: " . $e->getMessage());

                return sprintf(
                    '<pre class="%s">%s</pre>',
                    htmlspecialchars($this->mermaidClass),
                    htmlspecialchars($diagramContent)
                );
            }
        }, $content);
    }

    /**
     * Enable or disable Mermaid diagram processing
     * 
     * @param bool $enabled Whether to enable Mermaid processing
     * @return void
     */
    public function setMermaidEnabled(bool $enabled): void
    {
        $this->mermaidEnabled = $enabled;
    }

    /**
     * Set the CSS class used for Mermaid diagram containers
     * 
     * @param string $class CSS class name
     * @return void
     */
    public function setMermaidClass(string $class): void
    {
        $this->mermaidClass = $class;
    }

    /**
     * Read a Markdown file and parse its contents
     * 
     * @param string $filePath Path to the Markdown file
     * @return string Parsed HTML content
     * @throws Exception If file cannot be read
     */
    public function parseFile(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new Exception("Document not found: " . basename($filePath));
        }

        $cacheKey = md5($filePath);
        $cachedContent = $this->getCache($cacheKey);

        if ($cachedContent) {
            $this->logger->info("Cache hit for file: " . basename($filePath));
            return $cachedContent;
        }

        try {
            $content = file_get_contents($filePath);

            if ($this->useMetadata) {
                $metadata = $this->extractMetadata($content);
                $content = $this->removeMetadata($content);
            }

            $parsedContent = $this->parse($content);

            $this->setCache($cacheKey, $parsedContent);

            return $parsedContent;
        } catch (Exception $e) {
            $this->logger->error("Could not parse file: " . $e->getMessage());
            throw new Exception("Could not parse file: " . $e->getMessage());
        }
    }

    /**
     * Enable additional Markdown extensions or features
     */
    private function enableMarkdownExtensions(): void
    {
        $this->parsedown->setBreaksEnabled(true);
        $this->parsedown->setUrlsLinked(true);
    }

    /**
     * Extract metadata from the markdown content (e.g., front matter)
     * 
     * @param string $content Markdown content
     * @return array Metadata
     */
    private function extractMetadata(string $content): array
    {
        $metadata = [];

        if (preg_match('/^---\s*\n(.*?)\n---/s', $content, $matches)) {
            $yamlContent = $matches[1];
            $metadata['raw'] = $yamlContent;
        }

        return $metadata;
    }

    /**
     * Remove metadata from the markdown content
     * 
     * @param string $content Markdown content
     * @return string Markdown content without metadata
     */
    private function removeMetadata(string $content): string
    {
        return preg_replace('/^---\s*\n(.*?)\n---/s', '', $content);
    }

    /**
     * Get cached content by key
     * 
     * @param string $cacheKey Cache key
     * @return string|null Cached content or null if not found
     */
    private function getCache(string $cacheKey): ?string
    {
        $cacheFile = $this->cacheDirectory . $cacheKey . '.html';

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $this->cacheTTL) {
            return file_get_contents($cacheFile);
        }

        return null;
    }

    /**
     * Set cached content by key
     * 
     * @param string $cacheKey Cache key
     * @param string $content Content to cache
     */
    private function setCache(string $cacheKey, string $content): void
    {
        if (!is_dir($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0777, true);
        }

        $cacheFile = $this->cacheDirectory . $cacheKey . '.html';
        file_put_contents($cacheFile, $content);
    }

    /**
     * Enable logging for debugging and tracking
     */
    public function enableLogging(\Psr\Log\LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
