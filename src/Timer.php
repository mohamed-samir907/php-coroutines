<?php

namespace MoSamirzz\Coroutine;

use Generator;

abstract class Timer
{
    /**
     * Make delay for the resolve function by given time.
     *
     * @param  int $milliseconds
     * @param  mixed $resolve
     * @return Generator
     */
    public static function delay(int $milliseconds, $resolve = null) : Generator
    {
        $seconds = self::msToSeconds($milliseconds);

        yield from self::wait(microtime(true), $seconds);

        return Coroutine::runCoroutine($resolve);
    }

    /**
     * Convert milliseconds to seconds.
     *
     * @param  integer $time
     * @return float
     */
    private static function msToSeconds(int $time)
    {
        return $time / 1000;
    }

    /**
     * Wait for the given time.
     *
     * @param  float $timeFrom
     * @param  float $timeToWait
     * @return Generator
     */
    private static function wait(float $timeFrom, float $timeToWait) : Generator
    {
        $cancel = false;

        while ((microtime(true) - $timeFrom) < $timeToWait && $cancel == false) {
            usleep(1);

            $cancel = yield;
        }
    }
}
