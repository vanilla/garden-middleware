<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware;

use Garden\Middleware\Exception\InvalidMiddlewareException;
use Generator;

/**
 * Class to run a middleware stack without increasing the execution stack to the length of the middleware.
 *
 * @template TParams of object
 * @template TResult of object
 */
final class MiddlewareExecutor {

    /** @var MiddlewareTipInterface<TParams, TResult> */
    private $tip;

    /** @var Array<array-key, MiddlewareInterface<TParams, TResult>> */
    private $middlewares;

    /** @var string */
    private $order;

    /**
     * DI.
     *
     * @param MiddlewareTipInterface<TParams, TResult> $tip
     * @param Array<MiddlewareInterface<TParams, TResult>> $middlewares
     * @param string $order One of the ORDER_* constants.
     */
    public function __construct(MiddlewareTipInterface $tip, array $middlewares, string $order) {
        $this->tip = $tip;
        $this->middlewares = $middlewares;
        $this->order = $order;
    }

    /**
     * Run the middleware stack.
     *
     * @param TParams $params Params to execute with.
     *
     * @return TResult The result.
     * @throws InvalidMiddlewareException
     */
    public function run($params) {
        $middlewareStack = [];

        $originalType = get_class($params);
        // Apply all the params.
        foreach ($this->iterate($this->middlewares) as $middleware) {
            $middlewareClass = get_class($middleware);
            $generator = $middleware->process($params);
            if (!$generator->valid()) {
                $message = sprintf("Middleware did not yield: %s", $middlewareClass);
                throw new InvalidMiddlewareException($message);
            }

            $yielded = $generator->current();
            if ($yielded === null) {
                $message = sprintf("Middleware did not yield a value: %s", $middlewareClass);
                throw new InvalidMiddlewareException($message);
            }

            $newType = get_class($yielded);
            if (!is_a($newType, $originalType,true)) {
                $message = sprintf(
                    "Middleware did not yield the same type: %s.\nExpected a %s instead got a %s",
                    $middlewareClass,
                    $originalType,
                    $newType
                );
                throw new InvalidMiddlewareException($message);
            }

            $middlewareStack[] = [$middlewareClass, $generator];
        }

        // Run the seed.
        $result = $this->tip->process($params);
        $expectedResultClass = get_class($result);

        /** @var Generator<array-key, TParams, TResult, TResult> $middlewareGenerator */
        foreach ($this->iterateReverse($middlewareStack) as [$middlewareClass, $middlewareGenerator]) {
            $middlewareGenerator->send($result);
            if ($middlewareGenerator->valid()) {
                $message = sprintf("Middleware did not yield exactly once: %s", $middlewareClass);
                throw new InvalidMiddlewareException($message);
            }

            $result = $middlewareGenerator->getReturn();
            if ($result === null) {
                $message = sprintf("Middleware not return a result: %s", $middlewareClass);
                throw new InvalidMiddlewareException($message);
            }

            if (!is_a($result, $expectedResultClass, true)) {
                $message = sprintf(
                    "Middleware did not return the correct type: %s.\nExpected a %s instead got a %s",
                    $middlewareClass,
                    $expectedResultClass,
                    get_class($result)
                );
                throw new InvalidMiddlewareException($message);
            }
        }

        return $result;
    }

    /**
     * Iterate an array or other foreach-able without making a copy of it.
     *
     * @template T of array
     *
     * @param T $iterable
     *
     * @return T|Generator
     */
    private function iterate($iterable): iterable {
        if ($this->order === MiddlewareCollection::ORDER_LIFO) {
            // Last in first out just iterates in the order they were given.
            yield from $iterable;
        } else {
            // First in last out iterates in reverse order.
            yield from $this->iterateReverse($iterable);
        }
    }

    /**
     * Iterate an array or other foreach-able without making a copy of it.
     *
     * @template T of array
     *
     * @param T $iterable
     *
     * @return Generator
     */
    private function iterateReverse($iterable): Generator {
        for ($value = end($iterable); ($key = key($iterable)) !== null; $value = prev($iterable)) {
            yield $key => $value;
        }
    }
}
