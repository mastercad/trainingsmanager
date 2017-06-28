<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

namespace Auth\Model\Assertion;

use Auth\Model\Assertion\AbstractAssertion;




/**
 * klasse hatte das interface Zend_Acl_Assert_Interface implementiert, da diese assert funktion aber vorschreibt
 * die zend interfaces zu übergeben als parameter, die klasse aber speziefisch für eine spezielle rolle und eine
 * spezielle klasse (hier kommentare) ist, ist es überflüssig hier auf ein explizites interface in der signatur zu
 * bestehen, da man sonst nicht sauber auf die funktionalitäten zugreifen kann, die die einzelnen role und resource
 * klassen zur verfügung stellen, das assert interface schreibt eh nur eine assert funktion vor ... PRIMA ....
 *
 * Class Application_Model_RecipeAssertion
 */

class Exercises extends AbstractAssertion {

}