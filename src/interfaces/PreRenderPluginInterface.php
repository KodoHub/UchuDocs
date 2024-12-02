<?php

namespace Documentation\interfaces;

/**
 * Interface for plugins that add preprocessing to page rendering
 */
interface PreRenderPluginInterface extends PluginInterface {
    /**
     * Perform actions before rendering a page
     * 
     * @param string $pageName Name of the page to be rendered
     * @return void
     */
    public function preRender(string $pageName): void;
}