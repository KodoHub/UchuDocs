<?php

namespace Documentation;

use Exception;
use ReflectionClass;

class Container
{
    private static $instances = [];
    private static $bindings = [];

    /**
     * Register a class in the container with an alias or a factory
     *
     * @param string $abstract Class or interface name
     * @param string|callable $concrete Concrete class or factory method
     */
    public static function bind($abstract, $concrete) {
        self::$bindings[$abstract] = $concrete;
    }

    /**
     * Get an instance of a class from the container
     *
     * @param string $abstract Class or interface name
     * @return object Instance of the requested class
     * @throws Exception
     */
    public static function get($abstract) {
        // Check if we already have an instance
        if (isset(self::$instances[$abstract])) {
            return self::$instances[$abstract];
        }

        // Check if it's registered as a binding
        if (isset(self::$bindings[$abstract])) {
            $concrete = self::$bindings[$abstract];
            $object = self::resolve($concrete);
        } else {
            // Default case: directly resolve the class
            $object = self::resolve($abstract);
        }

        // Store and return the instance
        self::$instances[$abstract] = $object;
        return $object;
    }

    /**
     * Resolve a class and its dependencies
     *
     * @param string $class
     * @return object
     * @throws Exception
     */
    private static function resolve($class) {
        // Use Reflection to inspect the class constructor and its dependencies
        $reflection = new ReflectionClass($class);

        // Check if the class has a constructor
        if (!$reflection->getConstructor()) {
            return new $class();
        }

        // Get the constructor parameters
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Resolve the parameters (inject dependencies)
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType()->getName();

            // Recursively resolve dependencies
            $dependencies[] = self::get($dependency);
        }

        // Return an instance with resolved dependencies
        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Register a singleton in the container
     *
     * @param string $abstract Class or interface name
     * @param string|callable $concrete Concrete class or factory method
     */
    public static function singleton($abstract, $concrete) {
        self::$bindings[$abstract] = $concrete;
    }
}
