<?php

// Ensure proper error reporting during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload classes
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/DocumentationRenderer.php';

use Documentation\Markdown;
use Documentation\DocumentationRenderer;
use Documentation\Container;

// Main application logic
class DocumentationApp {
    private $renderer;
    private $docsDirectory;

    public function __construct() {
        $this->docsDirectory = __DIR__ . '/docs/';
        $this->renderer = new DocumentationRenderer($this->docsDirectory);
    }

    public function run() {
        // Get the requested page
        $page = $_GET['page'] ?? 'getting-started';
        
        // Sanitize page input
        $page = preg_replace('/[^a-zA-Z0-9\-_]/', '', $page);
        
        try {
            $content = $this->renderer->renderPage($page);
            $this->renderTemplate($content);
        } catch (Exception $e) {
            $this->renderErrorPage($e->getMessage());
        }
    }

    private function renderTemplate($pageContent) {
        // Pass variables to the template
        $docsDirectory = $this->docsDirectory;
        
        // Use a single template file
        include __DIR__ . '/templates/template.php';
    }

    private function renderErrorPage($message) {
        http_response_code(404);
        echo "<h1>Page Not Found</h1>";
        echo "<p>" . htmlspecialchars($message) . "</p>";
    }
}

// Run the application
$app = new DocumentationApp();
$app->run();
