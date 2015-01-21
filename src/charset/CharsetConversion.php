<?php
/**
 * class.CharsetConversion.php
 * @package    ecsco\ormbase
 * @subpackage CHARSET
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */

namespace ecsco\ormbase\charset;

use ecsco\ormbase\exception\CharsetConversionException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;

/**
 * CharsetConversion
 * @package    ecsco\ormbase
 * @subpackage CHARSET
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */
class CharsetConversion {

    use GetTrait;
    use CallTrait;
    use NoCloneTrait;

    /**
     * Private constructor
     * @return CharsetConversion
     */
    private function __construct() {
    }

    /**
     * Convert a string from ISO to UTF8
     * do a conversion loop on the text until
     * there no further changes
     *
     * @param      $text
     * @param bool $useIconvIfExists
     *
     * @return int|string
     * @throws \ecsco\ormbase\exception\CharsetConversionException
     */
    public static function toUTF8( $text, $useIconvIfExists = false ) {

        if ( is_bool( $text ) ) {
            $text = (int)$text;
        }
        if ( is_numeric( $text ) ) {
            $text = (string)$text;
        }
        if ( !is_string( $text ) && !is_null( $text ) ) {
            throw new CharsetConversionException( "ToUTF8 expected string but got " . var_export( $text, true ) );
        }
        $text = trim( $text );
        $converted = '';
        $runcnt = 0;
        while ( $converted != $text ) {
            if ( $converted != '' ) {
                $text = $converted;
            }
            $converted = self::toUTF8Convert( trim( $text ), $useIconvIfExists );
            if ( $runcnt > 10 ) {
                throw new CharsetConversionException( 'loop detected during conversion' );
            }
            $runcnt++;
        }

        return $text;
    }

    /**
     * convert a string toUTF8
     *
     * @param      $text
     * @param bool $useIconvIfExists
     *
     * @return string
     * @throws \ecsco\ormbase\exception\CharsetConversionException
     */
    private static function toUTF8Convert( $text, $useIconvIfExists = false ) {

        if ( $useIconvIfExists === true && function_exists( 'iconv' ) ) {
            if ( !self::detectUTF8( $text ) ) {
                $text = iconv( 'ISO-8859-1', 'UTF-8', $text );
            }
        }
        elseif ( $useIconvIfExists === true && !function_exists( 'iconv' ) ) {
            throw new CharsetConversionException( 'toUTF8Convert: missing iconv' );
        }
        else {
            if ( mb_detect_encoding( ' ' . $text . ' ', 'UTF-8, ISO-8859-1' ) == 'ISO-8859-1' ) {
                $text = utf8_encode( $text );
            }
        }

        return ( $text );
    }

    /**
     * Detect UTF8 charset in a string
     *
     * @param $string
     *
     * @return int
     */
    private static function detectUTF8( $string ) {

        return preg_match( '%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs',
                           $string
        );
    }
}
