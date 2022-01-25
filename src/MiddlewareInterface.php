<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware;

/**
 * Interface representing a middleware implementation.
 *
 * @template TParams of object
 * @template TResult of object
 */
interface MiddlewareInterface {

    /**
     * Process the parameters into the middleware.
     *
     * @param TParams $params
     *
     * @return \Generator<array-key, TParams, TResult, TResult>
     */
    public function process($params): \Generator;
}
