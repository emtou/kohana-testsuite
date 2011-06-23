<?php
/**
 * Declares TestSuite_Core_Controller
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller.php
 * @since     2011-06-23
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Controller
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller.php
 */
abstract class TestSuite_Core_Controller extends TestSuite
{

  /**
   * Global testCase initialization for the controller
   *
   * Creates a local instance of the model
   *
   * @return null
   */
  public function setUp()
  {
    $this->_object_class_name = 'Controller_'.$this->_object_name;

    if (class_exists($this->_object_class_name))
    {
      $this->_object = new $this->_object_class_name(
                                new Request(''),
                                new Response(array())
                               );
    }
  }

} // End class TestSuite_Core_Controller