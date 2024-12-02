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
        $this->docsDirectory = rtrim($docsDirectory, DIRECTORY_SEPARATOR);
        $this->markdown = new Markdown();
        $this->pluginManager = $pluginManager ?? new PluginManager();
    }

    /**
     * Render a specific documentation page
     *
     * @param string $pageName Name of the page to render
     * @return string HTML content of the page
     * @throws \Exception If page cannot be found
     */
    public function renderPage(string $pageName): string
    {
        // Execute pre-render plugins
        $this->pluginManager->executePreRenderPlugins($pageName);

        // Locate the corresponding Markdown file
        $filePath = $this->findMarkdownFile($pageName);

        try {
            // Read and process the file content
            $content = file_get_contents($filePath);
            $content = $this->pluginManager->modifyContent($content, $pageName);

            // Parse the Markdown content into HTML
            $renderedContent = $this->markdown->parse($content);

            // Execute post-render plugins
            $this->pluginManager->executePostRenderPlugins($pageName, $renderedContent);

            return $renderedContent;
        } catch (\Exception $e) {
            throw new \Exception("Could not render page: " . $e->getMessage());
        }
    }

    /**
     * Get a hierarchical list of available pages
     *
     * @return array Hierarchical list of available documentation pages
     */
    public function getAvailablePages(): array
    {
        $pagesHierarchy = ['_children' => []];
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            $relativePath = str_replace($this->docsDirectory . DIRECTORY_SEPARATOR, '', $file);
            $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);

            $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);
            $filename = basename($pathParts[count($pathParts) - 1], '.md');
            $title = $this->getPageTitle($file);

            $current = &$pagesHierarchy['_children'];

            for ($i = 0; $i < count($pathParts) - 1; $i++) {
                $dir = $pathParts[$i];
                if (!isset($current[$dir])) {
                    $current[$dir] = [
                        '_name' => $dir,
                        '_children' => []
                    ];
                }
                $current = &$current[$dir]['_children'];
            }

            // Generate a route that will dynamically render the page
            $current[$filename] = [
                '_name' => $filename,
                '_title' => $title,
                '_path' => '?page=' . urlencode($filename)
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
     * Get all Markdown files in the documentation directory
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
            if (stripos($title, $query) !== false) {
                $matchingPages[] = [
                    'title' => $title,
                    'path' => '?page=' . urlencode(basename($file, '.md'))
                ];
            }
        }

        return $matchingPages;
    }

    /**
     * Render the sidebar only (this should be static)
     */
    public function renderSidebar(): string
    {
        $pagesHierarchy = $this->getAvailablePages();
        return $this->renderPagesHierarchy($pagesHierarchy);
    }
}
