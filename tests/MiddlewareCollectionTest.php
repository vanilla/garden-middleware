<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware\Tests;

use Garden\Middleware\MiddlewareCollection;
use Garden\Middleware\Tests\Fixtures\BadMiddleware;
use Garden\Middleware\Tests\Fixtures\MockMiddleware;
use Garden\Middleware\Tests\Fixtures\MockMiddlewareData;
use Garden\Middleware\Tests\Fixtures\MockMiddlewareTip;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the middleware collection.
 */
class MiddlewareCollectionTest extends TestCase {

    /** @var MiddlewareCollection<MockMiddlewareData, MockMiddlewareData> */
    private $collection;

    /**
     * Prepare the collection.
     */
    protected function setUp(): void {
        parent::setUp();
        /** @var MiddlewareCollection<MockMiddlewareData, MockMiddlewareData> $collection */
        $collection = new MiddlewareCollection();
        $this->collection = $collection;
        $this->collection
            ->seedCollection(new MockMiddlewareTip('result'))
            ->addMiddleware(new MockMiddleware('middle1'))
            ->addMiddleware(new MockMiddleware('middle2'))
            ->addMiddleware(new MockMiddleware('middle3'))
            ->addMiddleware(new MockMiddleware('middle4'))
        ;
    }

    /**
     * Test the First in Last out method of execution.
     */
    public function testFifo() {
        $params = new MockMiddlewareData('initial');
        $result = $this->collection->run($params);
        $this->assertEquals(['initial', 'middle4', 'middle3', 'middle2', 'middle1'], $params->getItems());
        $this->assertEquals(['result', 'middle1', 'middle2', 'middle3', 'middle4'], $result->getItems());
    }

    /**
     * Test the First in Last out method of execution.
     */
    public function testLifo() {
        $params = new MockMiddlewareData('initial');
        $this->collection->setOrder(MiddlewareCollection::ORDER_LIFO);
        $result = $this->collection->run($params);
        $this->assertEquals(['initial', 'middle1', 'middle2', 'middle3', 'middle4'], $params->getItems());
        $this->assertEquals(['result', 'middle4', 'middle3', 'middle2', 'middle1'], $result->getItems());
    }

    /**
     * Test that we validate param types.
     */
    public function testErrorDifferentParams() {
        $params = new MockMiddlewareData('initial');
        $this->collection->addMiddleware(new BadMiddleware(BadMiddleware::MODE_WRONG_YIELD));
        $this->expectExceptionMessage("Middleware did not yield the same type: Garden\Middleware\Tests\Fixtures\BadMiddleware");
        $result = $this->collection->run($params);
    }

    /**
     * Test that we validate result types.
     */
    public function testErrorDifferentResult() {
        $params = new MockMiddlewareData('initial');
        $this->collection->addMiddleware(new BadMiddleware(BadMiddleware::MODE_WRONG_RETURN));
        $this->expectExceptionMessage("Expected a Garden\Middleware\Tests\Fixtures\MockMiddlewareData instead got a stdClass");
        $result = $this->collection->run($params);
    }

    /**
     * Test that we validate there is a yields.
     */
    public function testErrorNoYield() {
        $params = new MockMiddlewareData('initial');
        $this->collection->addMiddleware(new BadMiddleware(BadMiddleware::MODE_NO_YIELD));
        $this->expectExceptionMessage("Middleware did not yield: Garden\Middleware\Tests\Fixtures\BadMiddleware");
        $result = $this->collection->run($params);

    }

    /**
     * Test that we validate there is a return.
     */
    public function testErrorNoReturn() {
        $params = new MockMiddlewareData('initial');
        $this->collection->addMiddleware(new BadMiddleware(BadMiddleware::MODE_NO_RETURN));
        $this->expectExceptionMessage("Middleware not return a result: Garden\Middleware\Tests\Fixtures\BadMiddleware");
        $result = $this->collection->run($params);

    }

    /**
     * Test that we validate there is just one yield.
     */
    public function testErrorTooManyYields() {
        $params = new MockMiddlewareData('initial');
        $this->collection->addMiddleware(new BadMiddleware(BadMiddleware::MODE_EXTRA_YIELD));
        $this->expectExceptionMessage("Middleware did not yield exactly once: Garden\Middleware\Tests\Fixtures\BadMiddleware");
        $result = $this->collection->run($params);
    }
}
