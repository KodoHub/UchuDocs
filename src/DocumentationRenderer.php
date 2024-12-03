<?php

namespace Documentation;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class DocumentationRenderer
{
    private $docsDirectory;
    private $markdown;
    private $pluginManager;

    public function __construct(string $docsDirectory, ?PluginManager $pluginManager = null)
    {
        // Normalize the directory path and allow relative paths
        $this->docsDirectory = realpath(rtrim($docsDirectory, DIRECTORY_SEPARATOR)) ?: $docsDirectory;
        $this->markdown = new Markdown();
        $this->pluginManager = $pluginManager ?? new PluginManager();
    }

    /**
     * Get a hierarchical list of available pages with unique identifiers
     *
     * @return array Hierarchical list of available documentation pages
     */
    public function getAvailablePages(): array
    {
        $pagesHierarchy = ['_children' => []];
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            // Get the relative path of the file within the docs directory
            $relativePath = str_replace($this->docsDirectory . DIRECTORY_SEPARATOR, '', $file);
            $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);

            // Remove the `.md` extension for the page URL
            $relativePathWithoutExtension = rtrim($relativePath, '.md');

            // Break the path into parts for subdirectories (separate directory and filename)
            $pathParts = explode(DIRECTORY_SEPARATOR, $relativePathWithoutExtension);
            $filename = array_pop($pathParts); // The last part is the file name
            $title = $this->getPageTitle($file);

            // Generate a unique identifier by including the full relative path
            $uniqueIdentifier = str_replace(DIRECTORY_SEPARATOR, '_', $relativePathWithoutExtension);

            $current = &$pagesHierarchy['_children'];

            // Traverse the path parts and build the hierarchical structure
            foreach ($pathParts as $dirPart) {
                if (!isset($current[$dirPart])) {
                    $current[$dirPart] = [
                        '_name' => $dirPart,
                        '_children' => []
                    ];
                }
                $current = &$current[$dirPart]['_children'];
            }

            // Encode the unique identifier to ensure URL safety
            $encodedUniqueIdentifier = urlencode($uniqueIdentifier);

            // Add the page with its unique identifier
            $current[$uniqueIdentifier] = [
                '_name' => $filename,
                '_title' => $title,
                '_path' => '/?page=' . $encodedUniqueIdentifier
            ];
        }

        return $pagesHierarchy;
    }

    /**
     * Render the hierarchical structure as an HTML menu
     *
     * @param array $hierarchy Hierarchical structure
     * @param int $depth Current depth level
     * @return string HTML content
     */
    public function renderPagesHierarchy(array $hierarchy, int $depth = 0): string
    {
        if (!isset($hierarchy['_children']) || empty($hierarchy['_children'])) {
            return '';
        }

        $html = '<ul class="documentation-menu" data-depth="' . $depth . '">';
        ksort($hierarchy['_children']);

        foreach ($hierarchy['_children'] as $key => $item) {
            if (isset($item['_children'])) {
                $html .= '<li class="menu-directory">';
                $html .= '<span class="directory-toggle directory-folder">' . htmlspecialchars($item['_name']) . '</span>';
                $html .= $this->renderPagesHierarchy($item, $depth + 1);
                $html .= '</li>';
            } elseif (isset($item['_title'])) {
                $fileClass = $depth === 0 ? 'menu-item top-level' : 'menu-item sub-directory';
                $html .= '<li class="' . $fileClass . '">';
                $html .= '<a href="' . $item['_path'] . '">' . htmlspecialchars($item['_title']) . '</a>';
                $html .= '</li>';
            }
        }

        $html .= '</ul>';
        return $html;
    }

    /**
     * Render a page by its unique identifier
     *
     * @param string $pageIdentifier Unique page identifier
     * @return string Rendered HTML content
     * @throws \Exception If the page is not found
     */
    public function renderPage(string $pageIdentifier): string
    {
        // Convert the unique identifier back to a file path
        $relativePath = str_replace('_', DIRECTORY_SEPARATOR, $pageIdentifier) . '.md';
        $filePath = $this->docsDirectory . DIRECTORY_SEPARATOR . $relativePath;

        // Verify the file exists
        if (!file_exists($filePath)) {
            throw new \Exception("Page not found: " . $pageIdentifier);
        }

        // Read the file content
        $content = file_get_contents($filePath);

        // Use the Markdown parser to convert the content to HTML
        return $this->markdown->parse($content);
    }

    /**
     * Get all Markdown files in the documentation directory and its subdirectories
     *
     * @return array List of Markdown file paths
     */
    public function getMarkdownFiles(): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->docsDirectory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $files = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'md') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Locate a Markdown file by its page name
     *
     * @param string $pageName Page name to locate
     * @return string Path to the Markdown file
     * @throws \Exception If the file is not found
     */
    public function findMarkdownFile(string $pageName): string
    {
        $files = $this->getMarkdownFiles();

        // Convert the page name back to a potential relative path
        $pagePath = str_replace('_', DIRECTORY_SEPARATOR, $pageName);

        // Try to match both file name and directory structure
        foreach ($files as $file) {
            // Compare the full relative path (directory + file) instead of just the base filename
            $relativePath = str_replace($this->docsDirectory . DIRECTORY_SEPARATOR, '', $file);
            $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);
            $filename = rtrim(basename($relativePath), '.md');

            if ($filename === $pagePath || $filename === $pageName) {
                return $file;
            }
        }

        throw new \Exception("Page not found: " . $pageName);
    }

    /**
     * Extract the title from a Markdown file
     *
     * @param string $filePath Path to the Markdown file
     * @return string Title of the page
     */
    public function getPageTitle(string $filePath): string
    {
        $content = file_get_contents($filePath);

        // Use the first Markdown heading as the title
        if (preg_match('/^# (.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }

        return basename($filePath, '.md');
    }

    /**
     * Search for documentation pages matching a query
     *
     * @param string $query Search term
     * @return array List of matching pages
     */
    public function searchPages(string $query): array
    {
        $matchingPages = [];
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            $title = $this->getPageTitle($file);
            
            // Get the unique identifier for this file
            $relativePath = str_replace($this->docsDirectory . DIRECTORY_SEPARATOR, '', $file);
            $relativePathWithoutExtension = rtrim($relativePath, '.md');
            $uniqueIdentifier = str_replace(DIRECTORY_SEPARATOR, '_', $relativePathWithoutExtension);

            if (stripos($title, $query) !== false) {
                $matchingPages[] = [
                    'title' => $title,
                    'path' => '?page=' . urlencode($uniqueIdentifier)
                ];
            }
        }

        return $matchingPages;
    }

    /**
     * Render the sidebar only (this should be static)
     *
     * @return string HTML content for the sidebar
     */
    public function renderSidebar(): string
    {
        $pagesHierarchy = $this->getAvailablePages();
        return $this->renderPagesHierarchy($pagesHierarchy);
    }
}
