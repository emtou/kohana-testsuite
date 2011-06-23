<?php
/**
 * Declares TestSuite_Core_Model
 *
 * PHP version 5
 *
 * @group testsuite.model
 *
 * @category  TestSuite
 * @package   TestSuiteCore
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/model.php
 * @since     2011-06-23
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Model
 *
 * PHP version 5
 *
 * @group testsuite.model
 *
 * @category  TestSuite
 * @package   TestSuiteCore
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/model.php
 */
abstract class TestSuite_Core_Model extends TestSuite
{

  /**
   * Global testCase initialization for the model
   *
   * Creates a local instance of the model
   *
   * @return null
   */
  public function setUp()
  {
    $this->_object_class_name = 'Model_'.$this->_object_name;

    if (class_exists($this->_object_class_name))
    {
      $this->_object = Model::factory($this->_object_name);
    }
  }

} // End class TestSuite_Core_Model