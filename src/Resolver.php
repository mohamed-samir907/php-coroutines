<?php

namespace MoSamirzz\Coroutine;

use Generator;

class Resolver
{
    /**
     * The generators that we want to handle it.
     * 
     * @var array
     */
    private array $generators;

    /**
     * The generators that was handled.
     * 
     * @var array
     */
    private array $results = [];

    /**
     * The generators that was started to being handled.
     * 
     * @var array
     */
    private array $started = [];

    /**
     * Create new Resolver.
     *
     * @param array $generators
     */
    public function __construct(array $generators = [])
    {
        $this->generators = $generators;
    }

    /**
     * Resolve the gived generators.
     *
     * @return Generator
     */
    public function resolve() : Generator
    {
        while (count($this->generators) > 0) {
            $generator  = current($this->generators);
            $key        = key($this->generators);

            if ($generator instanceof Generator) {
                $this->resolveGenerator($key, $generator);
            } else {
                unset($this->generators[$key]);
                $this->results[$key] = $generator;
            }

            $this->rewind($this->generators);
        }

        yield;
    }

    /**
     * Resolve the given generator.
     *
     * @param  mixed $key
     * @param  Generator $generator
     * @return void
     */
    private function resolveGenerator($key, $generator)
    {
        if (in_array($generator, $this->started, true)) {
            $generator->next();
        } else {
            $generator->current();
            $this->started[] = $generator;
        }

        if ($generator->valid() === false) {
            unset($this->generators[$key]);

            $this->results[$key] = Runtime::await($generator->getReturn());
        }
    }

    /**
     * Reset the generators array if there is no next generator to resolve.
     *
     * @param  array $generators
     * @return bool
     */
    private function rewind(array &$generators)
    {
        $reset = next($generators);

        if ($reset === false) {
            $reset = reset($generators);
        }

        return $reset;
    }
}
