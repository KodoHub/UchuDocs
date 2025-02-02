<?php

namespace Documentation;

use Parsedown;
use Exception;

class Markdown
{
    private $parsedown;
    private $cacheDirectory;
    private $cacheTTL;
    private $logger;
    private $useMetadata = false;

    public function __construct(string $cacheDirectory = null, int $cacheTTL = 3600, bool $useMetadata = false)
    {
        $this->parsedown = new Parsedown();

        $this->parsedown->setMarkupEscaped(true);
        $this->parsedown->setSafeMode(true);

        // Enable any additional markdown features if needed
        // $this->enableMarkdownExtensions();

        // Caching settings
        $this->cacheDirectory = $cacheDirectory ?? __DIR__ . '/../cache/';
        $this->cacheTTL = $cacheTTL;
        $this->useMetadata = $useMetadata;

        // Optional: Logger setup (if needed)
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
        return $this->parsedown->text($markdownContent);
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

        // Check if the content is cached
        $cacheKey = md5($filePath);
        $cachedContent = $this->getCache($cacheKey);

        if ($cachedContent) {
            $this->logger->info("Cache hit for file: " . basename($filePath));
            return $cachedContent;
        }

        try {
            // Read and preprocess file content
            $content = file_get_contents($filePath);

            if ($this->useMetadata) {
                // Extract metadata if needed (e.g., front matter)
                $metadata = $this->extractMetadata($content);
                $content = $this->removeMetadata($content);
            }

            $parsedContent = $this->parse($content);

            // Cache the parsed content
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
        // Example of enabling more advanced features
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
        // Example: Simple front matter extraction (YAML or custom header)
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

        // Check if cache file exists and is still valid
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
