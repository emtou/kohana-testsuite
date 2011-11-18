<?php
/**
 * Declares TestSuite_Core_Controller_Action
 *
 * PHP version 5
 *
 * @group testsuite
 *
 * @category  TestSuite
 * @package   TestSuiteCore
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller/action.php
 * @since     2011-11-17
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Controller_Action
 *
 * PHP version 5
 *
 * @group testsuite
 *
 * @category  TestSuite
 * @package   TestSuiteCore
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller/action.php
 */
abstract class TestSuite_Core_Controller_Action extends Kohana_Unittest_TestCase
{
  protected $_action_name     = '';
  protected $_controller      = NULL;
  protected $_controller_name = '';
  protected $_response        = NULL;



  /**
   * Call controller's action
   *
   * @return bool action called ?
   */
  protected function _call_action()
  {
    $this->_init_controller();

    if (method_exists($this->_controller, $this->_action_name))
    {
      throw new TestSuite_Exception(
          'Can\'t check :controllername controller\'s :actionname action: '.
          'action does not exist',
          array(
            ':controllername' => $this->_controller_name,
            ':actionname'     => $this->_action_name,
          )
      );
      return FALSE;
    }

    $this->_controller->before();
    $this->_controller->{'action_'.$this->_action_name}();
    $this->_controller->after();

    return TRUE;
  }


  /**
   * Instanciate the controller
   *
   * @return null
   *
   * @throw TestSuite_Exception Controller :controllerclass does not exist
   */
  protected function _init_controller()
  {
    $controller_class_name = 'Controller_'.$this->_controller_name;

    if ( ! class_exists($controller_class_name))
    {
      throw new TestSuite_Exception(
        'Controller :controllerclass does not exist',
        array(':controllerclass' => $controller_class_name)
      );
    }

    $this->_response   = new Response(array());
    $this->_controller = new $controller_class_name(new Request(''), $this->_response);
  }

} // end class TestSuite_Core_Controller_Action