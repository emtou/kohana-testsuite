<?php
/**
 * Declares TestSuite_Core_Field_Timestamp
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/field/timestamp.php
 * @since     2011-06-24
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Field_Timestamp
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/field/timestamp.php
 */
abstract class TestSuite_Core_Field_Timestamp extends TestSuite_Field
{

  /**
   * Adds default constraints
   *
   * @param string $alias alias of the field
   *
   * @return TestSuite_Field initialized field
   */
  public function __construct($alias)
  {
    parent::__construct($alias);

    $this->add(TestSuite_Constraint::factory('required', FALSE));
    $this->add(TestSuite_Constraint::factory('unique', FALSE));
  }

} // End class TestSuite_Core_Field_Timestamp