<?php
/**
 * Declares TestSuite_Core_Constraint
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/constraint.php
 * @since     2011-06-24
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Constraint
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/constraint.php
 */
abstract class TestSuite_Core_Constraint
{
  protected $_params = array();

  protected static $_constraint_prefix = 'TestSuite_Constraint_';

  public $type = '';


  /**
   * Fills in type and params
   *
   * return TestSuite_Constraint initialized constraint
   */
  public function __construct()
  {
    $this->type = $this->type();

    $params = func_get_args();
    if (isset($params[0]))
    {
      $this->_params = $params[0];
    }
  }


  /**
   * Gets typed constraint class name
   *
   * @param string $type type of the constraint
   *
   * @return string name of the constraint object
   */
  public static function class_name($type)
  {
    return strtolower(TestSuite_Constraint::$_constraint_prefix.$type);
  }


  /**
   * Factory to instanciate typed constraints
   *
   * @param string $type type of the constraint to create
   *
   * @return TestSuite_Constraint instanciated constraint
   */
  public static function factory($type)
  {
    $class = TestSuite_Constraint::class_name($type);

    if (func_num_args() > 1)
    {
      $args = func_get_args();
      array_shift($args);
      return new $class($args);
    }
    else
    {
      return new $class;
    }
  }


  /**
   * Fetches internal params for the constraint
   *
   * @return array internal params
   */
  public function params()
  {
    return $this->_params;
  }


  /**
   * Finds the type of the constraint from its class name
   *
   * @return string type of the constraint
   */
  public function type()
  {
    $type = preg_replace(
              '/^'.TestSuite_Constraint::$_constraint_prefix.'/i',
              '',
              get_class($this)
            );

    return strtolower($type);
  }

} // End class TestSuite_Core_Constraint