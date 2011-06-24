<?php
/**
 * Declares TestSuite_Core_Model_Jelly
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/model/jelly.php
 * @since     2011-06-23
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Model_Jelly
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/model/jelly.php
 */
abstract class TestSuite_Core_Model_Jelly extends TestSuite_Model
{

  /**
   * Base configuration for Jelly models
   *
   * @param string $name      see PHPUnit_Framework_TestCase::__construct()
   * @param array  $data      see PHPUnit_Framework_TestCase::__construct()
   * @param string $data_name see PHPUnit_Framework_TestCase::__construct()
   *
   * @return null
   */
  public function __construct($name = NULL, array $data = array(), $data_name = '')
  {
    parent::__construct($name, $data, $data_name);

    $this->_register_public_static_method('initialize');
  }

} // End class TestSuite_Core_Model_Jelly