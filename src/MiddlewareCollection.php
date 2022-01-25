<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware;

use Garden\Middleware\Exception\InvalidMiddlewareException;

/**
 * @template TParams of object
 * @template TResult of object
 */
final class MiddlewareCollection {

    public const ORDER_FIFO = "fifo";
    public const ORDER_LIFO = "lifo";

    /** @var MiddlewareTipInterface<TParams, TResult> */
    private $seed;

    /** @var Array<array-key, MiddlewareInterface<TParams, TResult>> */
    protected $middlewares = [];

    /** @var string */
    private $order;

    /**
     * DI.
     *
     * @param string $order
     */
    public function __construct(string $order = self::ORDER_FIFO) {
        $this->order = $order;
    }

    /**
     * @param string $order
     */
    public function setOrder(string $order): void {
        $this->order = $order;
    }

    /**
     * Seed the middleware stack for initial result creation.
     *
     * For example in a web middleware the seed would be the request handler that generates a response.
     * In a database middleware the seed would be the method that looks up the item in the database.
     *
     * @param MiddlewareTipInterface<TParams, TResult> $seed
     * @return MiddlewareCollection<TParams, TResult>
     */
    public function seedCollection(MiddlewareTipInterface $seed): MiddlewareCollection {
        $this->seed = $seed;
        return $this;
    }

    /**
     * Add a middleware to the collection.
     *
     * @param MiddlewareInterface<TParams, TResult> $middleware
     *
     * @return MiddlewareCollection<TParams, TResult>
     */
    public function addMiddleware(MiddlewareInterface $middleware): MiddlewareCollection {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Run the middleware.
     *
     * @param TParams $params
     *
     * @return TResult
     * @throws InvalidMiddlewareException
     */
    public function run($params) {
        if ($this->seed === null) {
            throw new InvalidMiddlewareException('Cannot run middleware without seeding it.');
        }

        $executor = new MiddlewareExecutor($this->seed, $this->middlewares, $this->order);
        return $executor->run($params);
    }
}
