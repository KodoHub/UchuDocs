<?php

namespace Documentation;

use Documentation\interfaces\{PluginInterface, ContentModifierPluginInterface, PreRenderPluginInterface, PostRenderPluginInterface};

/**
 * Plugin Manager for documentation system
 * Manages loading, registering, and executing plugins
 */
class PluginManager
{
    /**
     * @var PluginInterface[] Registered plugins
     */
    private $plugins = [];

    /**
     * Register a plugin
     * 
     * @param PluginInterface $plugin Plugin to register
     * @return self
     */
    public function registerPlugin(PluginInterface $plugin): self
    {
        $this->plugins[] = $plugin;
        
        // Sort plugins by priority
        usort($this->plugins, function($a, $b) {
            return $a->getPriority() <=> $b->getPriority();
        });

        return $this;
    }

    /**
     * Get all registered plugins of a specific interface type
     * 
     * @param string $interfaceType Fully qualified interface name
     * @return PluginInterface[]
     */
    public function getPluginsByType(string $interfaceType): array
    {
        return array_filter($this->plugins, function($plugin) use ($interfaceType) {
            return $plugin instanceof $interfaceType;
        });
    }

    /**
     * Apply content modifier plugins
     * 
     * @param string $content Original content
     * @param string $pageName Name of the page
     * @return string Modified content
     */
    public function modifyContent(string $content, string $pageName): string
    {
        /** @var ContentModifierPluginInterface $plugin */
        foreach ($this->getPluginsByType(ContentModifierPluginInterface::class) as $plugin) {
            $content = $plugin->modifyContent($content, $pageName);
        }
        return $content;
    }

    /**
     * Execute pre-render plugins
     * 
     * @param string $pageName Name of the page to render
     * @return void
     */
    public function executePreRenderPlugins(string $pageName): void
    {
        /** @var PreRenderPluginInterface $plugin */
        foreach ($this->getPluginsByType(PreRenderPluginInterface::class) as $plugin) {
            $plugin->preRender($pageName);
        }
    }

    /**
     * Execute post-render plugins
     * 
     * @param string $pageName Name of the rendered page
     * @param string $renderedContent Rendered HTML content
     * @return void
     */
    public function executePostRenderPlugins(string $pageName, string $renderedContent): void
    {
        /** @var PostRenderPluginInterface $plugin */
        foreach ($this->getPluginsByType(PostRenderPluginInterface::class) as $plugin) {
            $plugin->postRender($pageName, $renderedContent);
        }
    }
}
