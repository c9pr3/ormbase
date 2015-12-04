<?php
/**
 * CachableInterface.php
 * @package    ecsco\ormbase
 * @subpackage CACHE
 */

declare(strict_types=1);

namespace ecsco\ormbase;

/**
 * CachableInterface
 * @package    ecsco\ormbase
 * @subpackage CACHE
 */
interface CachableInterface {

    /**
     * Get cache identifier
     * @return string
     */
    public static function getCacheIdentifier();
}
