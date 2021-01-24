<?php

namespace MoSamirzz\Coroutine;

use Generator;
use MoSamirzz\Coroutine\Timer;

abstract class Coroutine
{

    /**
     * The generators that we want to resolve.
     * 
     * @var array
     */
    protected static array $generators = [];

    /**
     * Make delay for the given callback by given time.
     *
     * @param  int $milliseconds
     * @param  mixed $resolve
     * @return Generator
     */
    public static function delay($ms, callable $callback) : Generator
    {
        $generator = Timer::delay($ms, $callback);

        self::$generators[] = $generator;

        return $generator;
    }

    /**
     * Run coroutine function.
     *
     * @param  callable $callback
     * @return void
     */
    public static function go(callable $callback) : Generator
    {
        return self::delay(0, $callback);
    }

    /**
     * Create coroutine executer.
     *
     * @param  callable $callback
     * @return mixed
     */
    public static function create(callable $callback)
    {
        $callback();

        return self::runCoroutine(
            self::all(self::$generators)
        );
    }

    /**
     * Run the coroutine.
     *
     * @param  mixed $value
     * @return mixed
     */
    public static function runCoroutine($value)
    {
        if ($value instanceof Generator) {
            return self::handleGenerator($value);
        }

        if (is_callable($value)) {
            return self::runCoroutine($value());
        }

        if (is_array($value)) {
            return array_map(function($generator) {
                return self::runCoroutine($generator);
            }, $value);
        }

        return $value;
    }

    /**
     * Handle generator value.
     *
     * @param  Generator $value
     * @return mixed
     */
    private static function handleGenerator($value)
    {
        $value->current();

        while ($value->valid()) {
            $value->next();
        }

        return self::runCoroutine($value->getReturn());
    }

    /**
     * Resolve all generators.
     *
     * @param  array $generators
     * @return Generator
     */
    protected static function all(array $generators) : Generator
    {
        $resolver = new Resolver($generators);

        return $resolver->resolve();
    }
}
