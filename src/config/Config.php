<?php
/**
 * class.Config.php
 * @package    wplibs
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */

namespace wplibs\config;

use Packaged\Config\Provider\AbstractConfigProvider;
use wplibs\traits\tGetInstance;

/**
 * class Config
 * @package    wplibs
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */
class Config extends AbstractConfigProvider {

    use tGetInstance;
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
