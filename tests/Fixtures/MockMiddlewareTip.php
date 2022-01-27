<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware\Tests\Fixtures;

use Garden\Middleware\MiddlewareTipInterface;

/**
 * @implements MiddlewareTipInterface<MockMiddlewareData, MockMiddlewareData>
 */
final class MockMiddlewareTip implements MiddlewareTipInterface {

    /** @var string */
    private $result;

    /**
     * DI.
     *
     * @param string $result
     */
    public function __construct(string $result) {
        $this->result = $result;
    }


    /**
     * @param MockMiddlewareData $params
     * @return MockMiddlewareData
     */
    public function run($params): MockMiddlewareData {
        return new MockMiddlewareData($this->result);
    }
}
