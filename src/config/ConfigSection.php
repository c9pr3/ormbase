<?php
/**
 * ConfigSection.php
 * @package    ecsco\ormbase
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150117 14:07
 */

namespace ecsco\ormbase\config;

use Packaged\Config\Provider\ConfigSection as PackagedConfigSection;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;

/**
 * class ConfigSection
 * @package    ecsco\ormbase
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150117 14:07
 */
class ConfigSection extends PackagedConfigSection {

    use GetTrait;
    use CallTrait;
    use NoCloneTrait;
}
