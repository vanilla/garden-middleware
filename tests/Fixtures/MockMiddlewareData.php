<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware\Tests\Fixtures;

/**
 * Mock class for storing a stack of data.
 */
final class MockMiddlewareData {

    /** @var array */
    private $items;

    /**
     * DI.
     *
     * @param array $items
     */
    public function __construct(...$items) {
        $this->items = $items;
    }

    /**
     * Add some data.
     *
     * @param string $item
     */
    public function push(string $item) {
        $this->items[] = $item;
    }

    /**
     * @return array
     */
    public function getItems(): array {
        return $this->items;
    }
}
