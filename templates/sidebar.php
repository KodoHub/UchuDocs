<?php

$documentationRenderer = new Documentation\DocumentationRenderer(__DIR__ . '/../docs/');
$pages = $documentationRenderer->getAvailablePages();

?>
<div class="sidebar">
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search documentation...">
        <div id="search-results"></div>
    </div>
    <h2>Documentation</h2>
    <nav>
        <ul>
            <?php foreach ($pages as $pageSlug => $pageTitle): ?>
                <li>
                    <a href="?page=<?= htmlspecialchars($pageSlug) ?>">
                        <?= htmlspecialchars($pageTitle) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>
<div class="content">

<script src="/templates/static/js/search.js"></script>
