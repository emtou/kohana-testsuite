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
   * Checks if a field exists
   *
   * @param string $alias alias of the field
   *
   * @return null
   */
  protected function _test_field_exists($alias)
  {
    $this->assertInstanceOf(
        'Jelly_Field',
        $this->_object->meta()->field($alias),
        'Field «'.$alias.'» does not exist in «'.$this->_object_name.'» model.'
    );
  }


  /**
   * Checks all given lengths' constraints for the given type and expected result
   *
   * @param array  $constraints     list of length constraints (key is field alias)
   * @param string $check_type      type of length check
   * @param bool   $expected_result expected assert result while checking length
   *
   * @return null
   *
   * @throws TestSuite_Exception Invalid length constraint on :modelname:::fieldalias field :
   *                             expected 2 parameters, found :nbparams
   */
  protected function _test_fields_length(array $constraints, $check_type, $expected_result)
  {
    $default_values = array();

    foreach ($constraints as $field_alias => $constraint)
    {
      if (sizeof($constraint->params()) != 2)
      {
        throw new TestSuite_Exception(
          'Invalid length constraint on :modelname:::fieldalias field : '.
          'expected 2 parameters, found :nbparams',
          array(
            ':modelname'  => $this->_object_name,
            ':fieldalias' => $field_alias,
            ':nbparams'   => sizeof($constraint->params())
          )
        );
      }

      $length = $constraint->get_length($check_type);
      if ($length < 0)
        continue;

      $value = str_repeat('x', $length);
      $this->_object->set($field_alias, $value);

      // if this field should be unique, set its default value
      // to the current value to bypass unique rule testing
      $default_values[$field_alias] = $this->_object->meta()->field($field_alias)->default;

      $this->_object->meta()->field($field_alias)->default = $value;
    }

    try
    {
      $this->_object->check(Jelly_Validation::factory(array_keys($constraints)));
    }
    catch (Jelly_Validation_Exception $exception)
    {
      $errors = $exception->errors();

      foreach ($constraints as $field_alias => $constraint)
      {
        $validation_error = TRUE;

        if (isset($errors[$field_alias])
          and in_array($errors[$field_alias][0], array('min_length', 'max_length', 'not_empty')))
        {
          $validation_error = FALSE;
        }

        $params  = $constraint->params();
        $message = $this->_object_name.'::'.$field_alias.' field ';
        if ($expected_result)
        {
          $message .= 'must accept input with '.$constraint->get_length($check_type).
                      ' characters but does not: expected length between '.
                      $params[0].' and '.$params[1];
        }
        else
        {
          $message .= 'must not accept input with '.$constraint->get_length($check_type).
                      ' characters but does: expected length between '.
                      $params[0].' and '.$params[1];
        }

        if ($constraint->get_length($check_type) > 0)
        {
          $this->assertTrue($expected_result == $validation_error, $message);
        }
      }
    }

    foreach ($constraints as $field_alias => $constraint)
    {
      $this->_object->set($field_alias, $this->_object->original($field_alias));

      // if this field should be unique, reset its default value
      if (isset($default_values[$field_alias]))
      {
        $this->_object->meta()->field($field_alias)->default = $default_values[$field_alias];
      }
    }
  }


  /**
   * Checks all given primary constraints
   *
   * @param array $constraints list of primary constraints (key is field alias)
   *
   * @return null
   */
  protected function _test_fields_primary(array $constraints)
  {
    foreach ($constraints as $field_alias => $constraint)
    {
      $params = $constraint->params();

      if (sizeof($params) == 0)
      {
        $params[] = TRUE;
      }

      $message = $this->_object_name.'::'.$field_alias.' field ';
      if ($params[0])
      {
        $message .= 'must be a primary key but is not.';
      }
      else
      {
        $message .= 'must not be a primary key but is.';
      }

      $this->assertTrue($this->_object->meta()->field($field_alias)->primary == $params[0], $message);
    }
  }


  /**
   * Checks all given required constraints
   *
   * @param array $constraints list of required constraints (key is field alias)
   *
   * @return null
   */
  protected function _test_fields_required(array $constraints)
  {
    foreach ($constraints as $field_alias => $constraint)
    {
      $params = $constraint->params();

      if (sizeof($params) == 0)
      {
        $params[] = TRUE;
      }

      $value = '';
      $this->_object->set($field_alias, $value);
    }

    try
    {
      $this->_object->check(Jelly_Validation::factory(array_keys($constraints)));
    }
    catch (Jelly_Validation_Exception $exception)
    {
      $errors = $exception->errors();

      foreach ($constraints as $field_alias => $constraint)
      {
        $validation_error = FALSE;

        if (isset($errors[$field_alias])
          and $errors[$field_alias][0] == 'not_empty')
        {
          $validation_error = TRUE;
        }

        $params = $constraint->params();
        if (sizeof($params) == 0)
        {
          $params[] = TRUE;
        }
        $message = $this->_object_name.'::'.$field_alias.' field ';
        if ($params[0])
        {
          $message .= 'must not accept empty input but does.';
        }
        else
        {
          $message .= 'must accept empty input but does not.';
        }

        $this->assertTrue($validation_error == $params[0], $message);
      }
    }

    foreach ($constraints as $field_alias => $constraint)
    {
      $this->_object->set($field_alias, $this->_object->original($field_alias));
    }
  }


  /**
   * Checks all given unique constraints
   *
   * @param array $constraints list of unique constraints (key is field alias)
   *
   * @return null
   */
  protected function _test_fields_unique(array $constraints)
  {
    foreach ($constraints as $field_alias => $constraint)
    {
      $params = $constraint->params();

      if (sizeof($params) == 0)
      {
        $params[] = TRUE;
      }

      $message = $this->_object_name.'::'.$field_alias.' field ';
      if ($params[0])
      {
        $message .= 'must be a unique but is not.';
      }
      else
      {
        $message .= 'model must not be unique but is.';
      }

      $this->assertTrue($this->_object->meta()->field($field_alias)->unique == $params[0], $message);
    }
  }


  /**
   * Checks a string field
   *
   * @param string $alias alias of the field
   *
   * @return null
   */
  protected function _test_string_field($alias)
  {
    var_dump($this->_fields[$alias]);
  }


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