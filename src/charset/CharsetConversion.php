<?php
/**
 * class.CharsetConversion.php
 * @package    WPLIBS
 * @subpackage CHARSET
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */

namespace wplibs\charset;

use wplibs\exception\CharsetConversionException;

/**
 * CharsetConversion
 * @package    WPLIBS
 * @subpackage CHARSET
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */
class CharsetConversion {

    const CC_INTERNAL_ENCODING = 1;
    const CC_INVALID_ENCODING  = 2;

    static $conversionmap = [
        'ä' => 'ae',
        'ü' => 'ue',
        'ö' => 'oe',
        'Ä' => 'Ae',
        'Ü' => 'Ue',
        'Ö' => 'Oe',
        'ß' => 'ss',
        'é' => 'e'
    ];

    /**
     * Private constructor
     * @return CharsetConversion
     */
    private function __construct() {
    }

    /**
     * Converts all $chars with help of the $conversionmap
     * Every char in $in and $chars will be checked.
     * If any of those are not ASCII or UTF8 the method throws
     * an exception
     *
     * @param string
     * @param string
     *
     * @return string
     * public static function autoConvert( $in, $chars = 'äüöÄÜÖßé' ) {
     * self::checkInternalEncoding ();
     * self::checkUTF8 ( $in );
     * self::checkUTF8 ( $chars );
     * self::$conversionmap[ self::unichr ( 0xC3A9 ) ] = 'e';
     * self::$conversionmap[ '´' ] = "'";
     * $charLen = mb_strlen ( $chars );
     * for ( $i = 0; $i < $charLen; $i++ ) {
     * $replace_char = mb_substr ( $chars, $i, 1 );
     * $in = ereg_replace ( $replace_char, self::$conversionmap[ $replace_char ], $in );
     * }
     * return $in;
     * }
     */

    /**
     * Checks internal encoding. If not UTF8 the methods
     * throws an exception
     * @return void
     * @throws \wplibs\exception\CharsetConversionException
     */
    public static function checkInternalEncoding() {

        if ( mb_internal_encoding() != 'UTF-8' ) {
            throw new CharsetConversionException( 'mb_internal_encoding: UTF-8 needed but is ' . mb_internal_encoding()
            );
        }

        if ( mb_regex_encoding() != 'UTF-8' ) {
            throw new CharsetConversionException( 'mb_regex_encoding: UTF-8 needed but is ' . mb_regex_encoding() );
        }
    }

    /**
     * Unichr
     *
     * @param string
     *
     * @throws \Exception
     * @return string
     */
    public static function unichr( $c ) {

        if ( $c <= 0x7F ) {
            return chr( $c );
        }
        else if ( $c <= 0x7FF ) {
            return chr( 0xC0 | $c >> 6 ) . chr( 0x80 | $c & 0x3F );
        }
        else if ( $c <= 0xFFFF ) {
            return chr( 0xE0 | $c >> 12 ) . chr( 0x80 | $c >> 6 & 0x3F ) . chr( 0x80 | $c & 0x3F );
        }
        else if ( $c <= 0x10FFFF ) {
            return chr( 0xF0 | $c >> 18 ) . chr( 0x80 | $c >> 12 & 0x3F ) . chr( 0x80 | $c >> 6 & 0x3F
            ) . chr( 0x80 | $c & 0x3F );
        }
        else {
            throw new CharsetConversionException( 'invalid char in unichr ' . intval( $c ) );
        }
    }

    /**
     * Convert a string from ISO to UTF8
     * do a conversion loop on the text until
     * there no further changes
     *
     * @param string
     * @param boolean
     *
     * @return string
     * @throws \wplibs\exception\CharsetConversionException
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
     * @param string
     * @param boolean
     *
     * @return string
     * @throws \wplibs\exception\CharsetConversionException
     */
    public static function toUTF8Convert( $text, $useIconvIfExists = false ) {

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
     * @param string
     *
     * @return boolean
     */
    public static function detectUTF8( $string ) {

        return preg_match( '%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs',
                           $string
        );
    }

    /**
     * Convert a string from UTF8 to ISO
     * do a conversion loop on the text until
     * there no further changes
     *
     * @param string
     * @param boolean
     *
     * @return string
     * @throws \wplibs\exception\CharsetConversionException
     */
    public static function utf8ToIso( $text, $useIconvIfExists = false ) {

        $text = trim( $text );
        $converted = '';
        $runcnt = 0;

        while ( $converted != $text ) {
            if ( $converted != '' ) {
                $text = $converted;
            }
            $converted = self::utf8ToIsoConvert( trim( $text ), $useIconvIfExists );
            if ( $runcnt > 10 ) {
                throw new CharsetConversionException( 'loop detected during conversion' );
            }
            $runcnt++;
        }

        return $text;
    }

    /**
     * Convert a string from utf8 to ISO
     *
     * @param string
     * @param boolean
     *
     * @return string
     * @throws \wplibs\exception\CharsetConversionException
     */
    public static function utf8ToIsoConvert( $text, $useIconvIfExists = false ) {

        if ( $useIconvIfExists === true && function_exists( 'iconv' ) ) {
            if ( self::detectUTF8( $text ) ) {
                $text = iconv( 'UTF-8', 'ISO-8859-1', $text );
            }
        }
        elseif ( $useIconvIfExists === true && ( !function_exists( 'iconv' ) ) ) {
            throw new CharsetConversionException( 'utf8ToIsoConvert: missing iconv' );
        }
        else {
            if ( mb_detect_encoding( ' ' . $text . ' ', 'UTF-8, ISO-8859-1' ) == 'UTF-8' ) {
                $text = utf8_decode( $text );
            }
        }

        return ( $text );
    }

    /**
     * Function to encode a header if necessary
     * according to RFC2047
     *
     * @param        $input
     * @param string $charset
     *
     * @return mixed
     */
    public static function encodeHeader( $input, $charset = 'ISO-8859-1' ) {

        preg_match_all( '/(\w*[\x80-\xFF]+\w*)/', $input, $matches );

        foreach ( $matches[ 1 ] as $value ) {
            $replacement = preg_replace( '/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value );
            $input = str_replace( $value, '=?' . $charset . '?Q?' . $replacement . '?=', $input );
        }

        return $input;
    }

    /**
     * Checks if $in is clearly ASCII or UTF8
     *
     * @param string
     *
     * @return string
     * @throws \wplibs\exception\CharsetConversionException
     */
    protected static function checkUTF8( $in ) {

        if ( !mb_check_encoding( $in, 'UTF-8' ) ) {
            throw new CharsetConversionException( 'checkUTF8: String is not valid UTF-8' );
        }

        return mb_detect_encoding( $in );
    }
}
