<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Uch큰 Docs</title>
        <link rel="icon" href="/img/32x32.png" type="image/x-icon">
        <style>
            :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --background-light: #f4f7f6;
            --text-color: #333;
            --code-background: #f8f9fa;
            --border-color: #e0e4e7;
            --highlight-color: #ecf0f1;
            }
            * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
            }
            body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-light);
            display: flex;
            min-height: 100vh;
            margin: 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            }
            /* Sidebar */
            .sidebar {
            width: 400px;
            padding: 20px;
            background-color: #f4f4f4;
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 2;
            transition: transform 0.3s ease;
            }
            .sidebar.hidden {
            transform: translateX(-100%);
            }
            .sidebar h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
            }
            .search-container {
            margin-bottom: 20px;
            }
            .search-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            outline: none;
            font-size: 16px;
            transition: border-color 0.3s ease;
            }
            .search-container input:focus {
            border-color: var(--secondary-color);
            }
            .sidebar nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            }
            .sidebar nav ul li {
            margin-bottom: 10px;
            }
            .sidebar nav ul li a {
            text-decoration: none;
            color: var(--primary-color);
            transition: color 0.3s ease;
            display: block;
            padding: 8px 15px;
            border-radius: 5px;
            }
            .sidebar nav ul li a:hover,
            .sidebar nav ul li a.active {
            background-color: var(--secondary-color);
            color: white;
            }
            /* Content */
            .content {
            width: 100%;
            flex-grow: 1;
            padding: 20px;
            margin-left: 500px;
            padding-bottom: 50px;
            overflow-y: auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            }
            .content .search-container {
            margin-bottom: 20px;
            max-width: 500px;
            }
            .content .search-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
            }
            /* Hamburger Menu */
            #menu-toggle {
            display: none;
            position: fixed;
            top: 30px;
            left: 20px;
            z-index: 3;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            background-color: #000;
            opacity: 1.0;
            }
            #menu-toggle span {
            display: block;
            width: 25px;
            height: 3px;
            background-color: #fff;
            margin: 5px 0;
            transition: all 0.3s ease;
            }
            #menu-toggle.open span:nth-child(1) {
            transform: rotate(45deg);
            position: relative;
            top: 8px;
            }
            #menu-toggle.open span:nth-child(2) {
            opacity: 0;
            }
            #menu-toggle.open span:nth-child(3) {
            transform: rotate(-45deg);
            position: relative;
            top: -8px;
            }
            .menu-item.top-level a {
            font-weight: bold;
            }
            .menu-item.sub-directory a {
            color: #666;
            padding-left: 20px;
            }
            .directory-toggle.directory-folder {
            font-weight: bold;
            text-transform: uppercase;
            }
            /* Footer */
            footer {
            background-color: var(--background-light);
            padding: 15px 0;
            text-align: center;
            font-size: 14px;
            color: var(--primary-color);
            border-top: 1px solid var(--border-color);
            width: 100%;
            position: relative;
            margin-top: auto;
            }
            footer p {
            margin: 0;
            }
            /* Responsive Styles */
            @media (max-width: 1024px) {
            body {
            flex-direction: column;
            }
            .sidebar {
            width: 100%;
            position: fixed;
            height: 100%;
            top: 0;
            left: -100%;
            z-index: 2;
            }
            .content {
            margin-left: 0;
            padding: 20px;
            }
            #menu-toggle {
            display: block;
            opacity: 0.2,
            }
            .sidebar.hidden {
            transform: translateX(0);
            left: 0;
            }
            }
            @media print {
            body {
            max-width: none;
            }
            .sidebar {
            display: none;
            }
            }
        </style>
    </head>
    <body>
        <?php
            $documentationRenderer = new Documentation\DocumentationRenderer(__DIR__ . '/../docs/');
            $pagesHierarchy = $documentationRenderer->getAvailablePages();
            ?>
        <div class="sidebar">
            <h2>Documentation</h2>
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Search documentation...">
            </div>
            <nav>
                <?= $documentationRenderer->renderPagesHierarchy($pagesHierarchy); ?>
            </nav>
        </div>
        <div class="content" id="content">
            <?php
                $pageName = $_GET['page'] ?? 'getting-started'; 
                try {
                    echo $documentationRenderer->renderPage($pageName); 
                } catch (Exception $e) {
                    echo "<h1>Error</h1>";
                    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                }
                ?>
        </div>
        <div id="menu-toggle" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <footer>
            <p>&copy; 2024 Uch큰 Documentation</p>
        </footer>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const menuToggle = document.getElementById('menu-toggle');
                const sidebar = document.querySelector('.sidebar');
                const sidebarSearchInput = document.getElementById('search-input');
                const links = document.querySelectorAll('.sidebar nav ul li a');
            
                menuToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('hidden');
                    menuToggle.classList.toggle('open');
                });
            
                // Improve search functionality
                sidebarSearchInput.addEventListener('input', (e) => {
                    const searchTerm = e.target.value.toLowerCase().trim();
                    links.forEach(link => {
                        const text = link.textContent.toLowerCase();
                        const isVisible = searchTerm === '' || text.includes(searchTerm);
                        link.style.display = isVisible ? 'block' : 'none';
                        link.closest('li').style.display = isVisible ? 'block' : 'none';
                    });
                });
            
                // Add keyboard accessibility
                menuToggle.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        sidebar.classList.toggle('hidden');
                        menuToggle.classList.toggle('open');
                    }
                });
            
                // Highlight active page
                const currentPage = new URLSearchParams(window.location.search).get('page') || 'getting-started';
                links.forEach(link => {
                    if (link.getAttribute('href').includes(currentPage)) {
                        link.classList.add('active');
                    }
                });
            });
        </script>
    </body>
</html>
