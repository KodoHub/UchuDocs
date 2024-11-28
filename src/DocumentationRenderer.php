<?php

namespace Documentation;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class DocumentationRenderer
{
    private $docsDirectory;
    private $markdown;

    public function __construct(string $docsDirectory) {
        $this->docsDirectory = $docsDirectory;
        $this->markdown = new Markdown();
    }

    /**
     * Render a specific documentation page
     * 
     * @param string $pageName Name of the page to render
     * @return string HTML content of the page
     * @throws \Exception If page cannot be found
     */
    public function renderPage(string $pageName): string {
        // Look for the .md file in the directory or subdirectories
        $filePath = $this->findMarkdownFile($pageName);

        try {
            return $this->markdown->parseFile($filePath);
        } catch (\Exception $e) {
            throw new \Exception("Could not render page: " . $e->getMessage());
        }
    }

    /**
     * Get list of available documentation pages, including those in subdirectories
     * 
     * @return array List of available documentation pages
     */
    public function getAvailablePages(): array {
        $pages = [];
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            // Get the relative page name (including subdirectories)
            $pageName = str_replace($this->docsDirectory, '', $file);
            $pageName = trim($pageName, DIRECTORY_SEPARATOR);
            $pageName = basename($pageName, '.md');

            // Add page to the list with title
            $pages[$pageName] = $this->getPageTitle($file);
        }

        return $pages;
    }

    /**
     * Recursively find all Markdown files in the docs directory and subdirectories
     * 
     * @return array List of all .md file paths
     */
    private function getMarkdownFiles(): array {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->docsDirectory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $files = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'md') {
                $files[] = $file->getRealPath();
            }
        }

        return $files;
    }

    /**
     * Find the .md file corresponding to a given page name
     * 
     * @param string $pageName
     * @return string Path to the corresponding .md file
     * @throws \Exception
     */
    private function findMarkdownFile(string $pageName): string {
        // Search for the Markdown file in the docs directory and subdirectories
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            if (basename($file, '.md') === $pageName) {
                return $file;
            }
        }

        throw new \Exception("Page not found: " . $pageName);
    }

    /**
     * Extract the title from a Markdown file
     * 
     * @param string $filePath Path to the Markdown file
     * @return string Page title (first heading or filename)
     */
    private function getPageTitle(string $filePath): string {
        $content = file_get_contents($filePath);

        // Try to extract first heading
        if (preg_match('/^# (.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }

        // Fallback to filename
        return basename($filePath, '.md');
    }
}
