<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware\Tests\Fixtures;

use Garden\Middleware\MiddlewareInterface;

/**
 * Mock middleware implementation that pushes a string into the params and result.
 *
 * @implements MiddlewareInterface<MockMiddlewareData, MockMiddlewareData>
 */
final class MockMiddleware implements MiddlewareInterface {

    /** @var string */
    private $toPush;

    /**
     * DI.
     *
     * @param string $toPush
     */
    public function __construct(string $toPush) {
        $this->toPush = $toPush;
    }

    /**
     * @param MockMiddlewareData $params
     * @return \Generator<array-key, MockMiddlewareData, MockMiddlewareData, MockMiddlewareData>
     */
    public function process($params): \Generator {
        // Modify params.
        $params->push($this->toPush);
        // Yield modified params and receive result.
        /** @var MockMiddlewareData $result */
        $result = yield $params;
        // Modify result.
        $result->push($this->toPush);
        // Return result.
        return $result;
    }
}
