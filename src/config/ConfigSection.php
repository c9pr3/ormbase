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
use wplibs\traits\tCall;
use wplibs\traits\tGet;
use wplibs\traits\tNoClone;

/**
 * class ConfigSection
 * @package    WPLIBS
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150117 14:07
 */
class ConfigSection extends PackagedConfigSection {

    use tGet;
    use tCall;
    use tNoClone;
}