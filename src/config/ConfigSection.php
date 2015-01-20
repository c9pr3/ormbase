<?php
/**
 * ConfigSection.php
 * @package    WPLIBS
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150117 14:07
 */

namespace wplibs\config;

use Packaged\Config\Provider\ConfigSection as PackagedConfigSection;
use wplibs\traits\CallTrait;
use wplibs\traits\GetTrait;
use wplibs\traits\NoCloneTrait;

/**
 * class ConfigSection
 * @package    WPLIBS
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150117 14:07
 */
class ConfigSection extends PackagedConfigSection {

    use GetTrait;
    use CallTrait;
    use NoCloneTrait;
}