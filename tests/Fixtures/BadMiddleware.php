<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Middleware\Tests\Fixtures;

use Garden\Middleware\MiddlewareInterface;

class BadMiddleware implements MiddlewareInterface {

    public const MODE_NO_RETURN = "noreturn";
    public const MODE_NO_YIELD = "noyield";
    public const MODE_EXTRA_YIELD = "extrayield";
    public const MODE_WRONG_RETURN = "wrongreturn";
    public const MODE_WRONG_YIELD = "wrongyield";

    /** @var string */
    private $mode;

    /**
     * Constructor.
     *
     * @param string $mode
     */
    public function __construct(string $mode) {
        $this->mode = $mode;
    }


    public function process($params): \Generator {
        $params->push("bad");

        if ($this->mode !== self::MODE_NO_YIELD) {
            if ($this->mode === self::MODE_WRONG_YIELD) {
                $result = yield new \stdClass();
            } else {
                $result = yield $params;
                $result->push("bad");
            }

            if ($this->mode === self::MODE_EXTRA_YIELD) {
                yield $params;
            }
        }

        if ($this->mode === self::MODE_WRONG_RETURN) {
            return new \stdClass();
        } elseif ($this->mode === self::MODE_NO_RETURN) {
            return null;
        }

        // Return result.
        return new MockMiddlewareData("bad");
    }
}
