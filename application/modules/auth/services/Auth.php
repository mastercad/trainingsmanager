<?php
/**
 *
 * @author Andreas Kempe / andreas.kempe@byte-artist.de
 *
 */

namespace Auth\Service;

use Zend_Auth;

/**
 * Class Auth
 *
 * @package Auth\Service
 */
class Auth extends Zend_Auth
{
    public $b_logged_in = false;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}