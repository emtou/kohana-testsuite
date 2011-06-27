<?php
/**
 * Declares TestSuite_Core_Field
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/field.php
 * @since     2011-06-24
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Field
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/field.php
 */
abstract class TestSuite_Core_Field
{
  protected $_constraints = array();

  protected static $_field_prefix = 'TestSuite_Field_';

  public $alias = '';


  /**
   * Fills in alias and type for this field
   *
   * @param string $alias alias of this field
   *
   * @return TestSuite_Field initialized field object
   */
  public function __construct($alias)
  {
    $this->alias = $alias;
    $this->type  = $this->type();
  }


  /**
   * Adds a constraint to this field
   *
   * @param TestSuite_Constraint $constraint constraint to attach
   *
   * @return TestSuite_Field this field
   */
  public function add(TestSuite_Constraint $constraint)
  {
    $this->_constraints[$constraint->type] = $constraint;

    return $this;
  }


  /**
   * Gets typed field class name
   *
   * @param string $type type of the field
   *
   * @return string name of the field object
   */
  public static function class_name($type)
  {
    return strtolower(TestSuite_Field::$_field_prefix.$type);
  }


  /**
   * Gets an attached constraint from its type
   *
   * @param string $type type of the constraint to fetch
   *
   * @return TestSuite_Constraint atatched constraint
   */
  public function constraint($type)
  {
    return $this->_constraints[$type];
  }


  /**
   * Provides a list of constraint types attached to this field
   *
   * @return array list of constraint types
   */
  public function constraints_types()
  {
    return array_keys($this->_constraints);
  }


  /**
   * Factory to instanciate typed fields
   *
   * @param string $type  type of the field to create
   * @param string $alias alias of the field to create
   *
   * @return TestSuite_Field instanciated field
   *
   * @throws TestSuite_Exception Can't create field: Field type «X» does not exist
   */
  public static function factory($type, $alias)
  {
    $class = TestSuite_Field::class_name($type);

    if ( ! class_exists($class))
    {
      throw new TestSuite_Exception(
          'Can\'t create field: Field type «'.$type.'» does not exist'
      );
    }
    return new $class($alias);
  }


  /**
   * Finds the type of the field from its class name
   *
   * @return string type of the field
   */
  public function type()
  {
    $type = preg_replace(
              '/^'.TestSuite_Field::$_field_prefix.'/i',
              '',
              get_class($this)
            );

    return strtolower($type);
  }

} // End class TestSuite_Core_Field