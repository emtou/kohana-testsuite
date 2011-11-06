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
   * Gets a list of all constraints of a given type for all registered fields
   *
   * @param string $constraint_type type of the constraints
   *
   * @return array fieldalias => constraint
   */
  protected function _get_all_constraints($constraint_type)
  {
    $constraints = array();

    foreach ($this->_fields as $field_alias => $field)
    {
      if ($field->has_constraint($constraint_type))
      {
        $constraints[$field_alias] = $field->constraint($constraint_type);
      }
    }

    return $constraints;
  }


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
    unset($alias);

    throw new TestSuite_Exception(
      'TestSuite_Model::_test_field_exists($alias) '.
      'must be overridden by specific TestSuite_Model.'
    );
  }


  /**
   * Checks all given lengths' constraints for the given type and expected result
   *
   * This method must be overridden by specific TestSuite_Model.
   *
   * @param array  $constraints     list of length constraints (key is field alias)
   * @param string $check_type      type of length check
   * @param bool   $expected_result expected assert result while checking length
   *
   * @return null
   *
   * @throws TestSuite_Exception TestSuite_Model::_test_fields_length()
   *                             must be overridden by specific TestSuite_Model.
   */
  protected function _test_fields_length(array $constraints, $check_type, $expected_result)
  {
    unset($constraints, $check_type, $expected_result);

    throw new TestSuite_Exception(
      'TestSuite_Model::_test_fields_length() '.
      'must be overridden by specific TestSuite_Model.'
    );
  }


  /**
   * Checks all given primary constraints
   *
   * This method must be overridden by specific TestSuite_Model.
   *
   * @param array $constraints list of primary constraints (key is field alias)
   *
   * @return null
   *
   * @throws TestSuite_Exception TestSuite_Model::_test_fields_primary()
   *                             must be overridden by specific TestSuite_Model.
   */
  protected function _test_fields_primary(array $constraints)
  {
    unset($constraints);

    throw new TestSuite_Exception(
      'TestSuite_Model::_test_fields_primary() '.
      'must be overridden by specific TestSuite_Model.'
    );
  }


  /**
   * Checks all given required constraints
   *
   * This method must be overridden by specific TestSuite_Model.
   *
   * @param array $constraints list of required constraints (key is field alias)
   *
   * @return null
   *
   * @throws TestSuite_Exception TestSuite_Model::_test_fields_required()
   *                             must be overridden by specific TestSuite_Model.
   */
  protected function _test_fields_required(array $constraints)
  {
    unset($constraints);

    throw new TestSuite_Exception(
      'TestSuite_Model::_test_fields_required() '.
      'must be overridden by specific TestSuite_Model.'
    );
  }


  /**
   * Checks all given unique constraints
   *
   * This method must be overridden by specific TestSuite_Model.
   *
   * @param array $constraints list of unique constraints (key is field alias)
   *
   * @return null
   *
   * @throws TestSuite_Exception TestSuite_Model::_test_fields_unique()
   *                             must be overridden by specific TestSuite_Model.
   */
  protected function _test_fields_unique(array $constraints)
  {
    unset($constraints);

    throw new TestSuite_Exception(
      'TestSuite_Model::_test_fields_unique() '.
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
   * Checks all registered fields' length constraints
   *
   * List of the checks:
   *  - all fields at minimum length should be OK
   *  - all fields at minimum length minus one (if > 0) should be ERROR
   *  - all fields at average length between min and max lengths should be OK
   *  - all fields at maximum length should be OK
   *  - all fields at maximum length plus one should be ERROR
   *
   * @return null
   */
  public function test_fields_lengths()
  {
    $constraints = $this->_get_all_constraints('length');

    $this->_test_fields_length($constraints, TestSuite_Constraint_Length::MIN, TRUE);
    $this->_test_fields_length($constraints, TestSuite_Constraint_Length::MIN_MINUS_ONE, FALSE);
    $this->_test_fields_length($constraints, TestSuite_Constraint_Length::AVERAGE, TRUE);
    $this->_test_fields_length($constraints, TestSuite_Constraint_Length::MAX, TRUE);
    $this->_test_fields_length($constraints, TestSuite_Constraint_Length::MAX_PLUS_ONE, FALSE);
  }


  /**
   * Checks all registered fields' primary constraints
   *
   * @return null
   */
  public function test_fields_primaries()
  {
    $constraints = $this->_get_all_constraints('primary');

    $this->_test_fields_primary($constraints);
  }


  /**
   * Checks all registered fields' required constraints
   *
   * @return null
   */
  public function test_fields_requireds()
  {
    $constraints = $this->_get_all_constraints('required');

    $this->_test_fields_required($constraints);
  }

  /**
   * Checks all registered fields' unique constraints
   *
   * @return null
   */
  public function test_fields_uniques()
  {
    $constraints = $this->_get_all_constraints('unique');

    $this->_test_fields_unique($constraints);
  }

} // End class TestSuite_Core_Model