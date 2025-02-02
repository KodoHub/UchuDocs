<?php

namespace Documentation\interfaces;

/**
 * Interface for plugins that modify page content
 */
interface ContentModifierPluginInterface extends PluginInterface
{
    /**
     * Modify the content of a documentation page before rendering
     * 
     * @param string $content Original page content
     * @param string $pageName Name of the page being rendered
     * @return string Modified content
     */
    public function modifyContent(string $content, string $pageName): string;
}
