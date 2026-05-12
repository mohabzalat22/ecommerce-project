<?php

declare(strict_types=1);

namespace Framework;

class ServiceContainer
{
    protected array $bindings = [];
    protected array $singletons = [];
    protected array $instances = [];
    protected array $resolving = [];

    // bind service
    public function bind(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    // singleton
    public function singleton(string $abstract, $concrete): void
    {
        $this->singletons[$abstract] = $concrete;
    }

    // resolve service
    public function make(string $abstract)
    {
        // circular dependency fix
        if (in_array($abstract, $this->resolving)) {
            throw new \Exception("Circular dependency detected: {$abstract}");
        }

        // singleton
        if (isset($this->singletons[$abstract])) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $this->build($abstract);
            }

            return $this->instances[$abstract];
        }

        $this->resolving[] = $abstract;

        try {
            $object = $this->build($abstract);
        } finally {
            array_pop($this->resolving);
        }

        if (isset($this->singletons[$abstract])) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /*
    |--------------------------------
    | Build object automatically
    |--------------------------------
    */
    public function build(string $abstract)
    {
        $concrete = $this->bindings[$abstract] ?? $this->singletons[$abstract] ?? null;

        if (null !== $concrete) {
            if ($concrete instanceof \Closure) {
                return $concrete($this);
            }
            $abstract = $concrete;
        }

        $reflector = new \ReflectionClass($abstract);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("CLASS {$abstract} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            return new $abstract();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if (!$type) {
                throw new \Exception('Cannot Resolve dependency');
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
