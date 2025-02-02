<?php

namespace Documentation\interfaces;

/**
 * Interface for documentation plugins
 * Allows different types of plugins to extend documentation rendering
 */
interface PluginInterface
{
    /**
     * Get the unique identifier for this plugin
     *
     * @return string Plugin identifier
     */
    public function getId(): string;

    /**
     * Get the priority of the plugin (used for ordering plugin execution)
     * Lower numbers execute first
     *
     * @return int Plugin priority
     */
    public function getPriority(): int;
}
