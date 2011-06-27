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
  protected $_fields = array();


  /**
   * Registers a new field for the model
   *
   * @param TestSuite_Field $field       field object to register
   * @param array           $constraints list of optional constraints
   *
   * @return null
   */
  protected function _register_field(TestSuite_Field $field, array $constraints = array())
  {
    foreach ($constraints as $constraint)
    {
      $constraint_type = $constraint[0];

      if ( ! isset($constraint[1]))
      {
        $field->add_constraint($constraint_type);
      }
      else
      {
        $field->add_constraint($constraint_type, $constraint[1]);
      }
    }

    $this->_fields[$field->alias] = $field;
  }


  /**
   * Checks a constraint on a field
   *
   * This method must be overridden by specific TestSuite_Model.
   *
   * @param string $alias           alias of the field
   * @param string $constraint_type type of the constraint to test
   *
   * @return null
   *
   * @throws TestSuite_Exception TestSuite_Model::_test_field_constraint($alias, $constraint_type)
   *                             must be overridden by specific TestSuite_Model.
   */
  protected function _test_field_constraint($alias, $constraint_type)
  {
    throw new TestSuite_Exception(
      'TestSuite_Model::_test_field_constraint($alias, $constraint_type) '.
      'must be overridden by specific TestSuite_Model.'
    );
  }


  /**
   * Checks if a field exists
   *
   * This method must be overridden by specific TestSuite_Model.
   *
   * @param string $alias alias of the field
   *
   * @return null
   *
   * @throws TestSuite_Exception TestSuite_Model::_test_field_exists($alias)
   *                             must be overridden by specific TestSuite_Model.
   */
  protected function _test_field_exists($alias)
  {
    throw new TestSuite_Exception(
      'TestSuite_Model::_test_field_exists($alias) '.
      'must be overridden by specific TestSuite_Model.'
    );
  }


  /**
   * Provides registered fields's aliases
   *
   * If no field has been registered, provides a dummy field name
   *
   * @return array list of fields's names
   */
  public function provider_fields_aliases()
  {
    if (sizeof($this->_fields) == 0)
      return array(array('dummy'));

    $fields_aliases = array();

    foreach (array_keys($this->_fields) as $field_alias)
    {
      $fields_aliases[] = array($field_alias);
    }

    return $fields_aliases;
  }


  /**
   * Provides registered fields's constraints
   *
   * If no constraint has been configured, provides a dummy field name
   *
   * @return array list of fields' constraints
   */
  public function provider_fields_constraints()
  {
    $fields_constraints = array();

    foreach ($this->_fields as $field_alias => $field)
    {
      foreach ($field->constraints_types() as $constraint_type)
      {
        $fields_constraints[] = array($field_alias, $constraint_type);
      }
    }

    if (sizeof($fields_constraints) == 0)
      return array(array('dummy', 'dummy'));

    return $fields_constraints;
  }


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


  /**
   * Checks if a registered field exists in the model
   *
   * @param string $alias alias of the field
   *
   * @return null
   *
   * @dataProvider provider_fields_aliases
   *
   * @test
   */
  public function test_field_exists($alias)
  {
    if ($alias == 'dummy')
      return;

    $this->_test_field_exists($alias);
  }


  /**
   * Checks a constraint on a registered field
   *
   * @param string $alias           alias of the field
   * @param string $constraint_type type of the constraint to test
   *
   * @return null
   *
   * @dataProvider provider_fields_constraints
   *
   * @test
   */
  public function test_field_constraint($alias, $constraint_type)
  {
    if ($alias == 'dummy')
      return;

    $this->_test_field_constraint($alias, $constraint_type);
  }

} // End class TestSuite_Core_Model