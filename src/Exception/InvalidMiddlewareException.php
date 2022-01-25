<?php
/**
 * @author Adam Charron <adam.c@vanillaforums.com>
 * @copyright 2009-2022 Vanilla Forums Inc.
 * @license MIT
 */

declare(strict_types=1);

namespace Garden\Middleware\Exception;

/**
 * Exception thrown if a middleware isn't declared properly.
 */
class InvalidMiddlewareException extends \Exception {}
