<?php

namespace Documentation\interfaces;

/**
 * Interface for plugins that add post-processing to page rendering
 */
interface PostRenderPluginInterface extends PluginInterface
{
    /**
     * Perform actions after rendering a page
     *
     * @param string $pageName Name of the page rendered
     * @param string $renderedContent The rendered HTML content
     * @return void
     */
    public function postRender(string $pageName, string $renderedContent): void;
}
