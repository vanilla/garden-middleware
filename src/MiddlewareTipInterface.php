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
interface MiddlewareTipInterface {

    /**
     * Process the parameters into the middleware.
     *
     * @psalm-param TParams $params
     *
     * @psalm-return TResult
     */
    public function run($params);
}
