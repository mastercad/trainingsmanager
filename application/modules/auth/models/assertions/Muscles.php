<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:50
 */

/**
 * klasse hatte das interface Zend_Acl_Assert_Interface implementiert, da diese assert funktion aber vorschreibt
 * die zend interfaces zu übergeben als parameter, die klasse aber speziefisch für eine spezielle rolle und eine
 * spezielle klasse (hier kommentare) ist, ist es überflüssig hier auf ein explizites interface in der signatur zu
 * bestehen, da man sonst nicht sauber auf die funktionalitäten zugreifen kann, die die einzelnen role und resource
 * klassen zur verfügung stellen, das assert interface schreibt eh nur eine assert funktion vor ... PRIMA ....
 *
 * Class Application_Model_RecipeAssertion
 */

class Auth_Model_Assertion_Muscles extends Auth_Model_Assertion_Abstract {
}