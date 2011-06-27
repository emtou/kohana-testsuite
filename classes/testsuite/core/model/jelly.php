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
    $field = $this->_object->meta()->field($alias);
    if ($field == NULL)
      return;

    switch ($constraint_type)
    {
      case 'encoding';
        throw new TestSuite_Exception('Encoding constraint is not developped for now.');
      break;

      case 'length';
        $params = $this->_fields[$alias]->constraint($constraint_type)->params();

        if (sizeof($params) != 2)
        {
          throw new TestSuite_Exception(
            'Invalid length constraint on «'.$alias.'» field of «'.$this->_object_name.'» model: '.
            'expected 2 parameters, found '.sizeof($params).'.'
          );
        }

        $length_tests = array();
        // minimum length
        $length_tests[] = array($params[0], TRUE);
        // maximum length
        $length_tests[] = array($params[1], TRUE);
        // minimum length + 1
        $length_tests[] = array($params[1]+1, FALSE);
        // average length
        $length_tests[] = array(intval(($params[0]+$params[0])/2), TRUE);
        // minimum length -1
        if ($params[0] > 0)
        {
          $length_tests[] = array($params[0]-1, FALSE);
        }

        foreach ($length_tests as $length_test)
        {
          $validation_error = TRUE;

          $value = str_repeat('x', $length_test[0]);
          $this->_object->set($alias, $value);
          try
          {
            $this->_object->check();
          }
          catch (Jelly_Validation_Exception $exception)
          {
            $errors = $exception->errors();
            if (isset($errors[$alias])
                and in_array($errors[$alias][0], array('min_length', 'max_length', 'not_empty')))
            {
              $validation_error = FALSE;
            }
          }


          if ($length_test[1])
          {
            $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                       'model must accept input with '.$length_test[0].
                       ' characters but does not: expected length between '.
                       $params[0].' and '.$params[1];
          }
          else
          {
            $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                       'model must not accept input with '.$length_test[0].
                       ' characters but does: expected length between '.
                       $params[0].' and '.$params[1];
          }

          $this->assertTrue(
              $length_test[1] == $validation_error,
              $message
          );

          $this->_object->set($alias, $this->_object->original($alias));
        }

      break;

      case 'primary';
        // Must look into field definition
        $params = $this->_fields[$alias]->constraint($constraint_type)->params();

        if (sizeof($params) == 0)
        {
          $params[] = TRUE;
        }

        if ($params[0])
        {
          $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                     'model must be a primary key but is not.';
        }
        else
        {
          $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                     'model must not be a primary key but is.';
        }

        $this->assertTrue(
            $field->primary == $params[0],
            $message
        );
      break;

      case 'required';
        $params = $this->_fields[$alias]->constraint($constraint_type)->params();

        if (sizeof($params) == 0)
        {
          $params[] = TRUE;
        }

        $validation_error = FALSE;

        $value = '';
        $this->_object->set($alias, $value);
        try
        {
          $this->_object->check();
        }
        catch (Jelly_Validation_Exception $exception)
        {
          $errors = $exception->errors();
          if (isset($errors[$alias])
              and $errors[$alias][0] == 'not_empty')
          {
            $validation_error = TRUE;
          }
        }

        if ($params[0])
        {
          $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                     'model must not accept empty input but does.';
        }
        else
        {
          $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                     'model must accept empty input but does not.';
        }

        $this->assertTrue(
              $validation_error == $params[0],
              $message
          );

        $this->_object->set($alias, $this->_object->original($alias));
      break;

      case 'unique';
        // Must look into field definition
        $params = $this->_fields[$alias]->constraint($constraint_type)->params();

        if (sizeof($params) == 0)
        {
          $params[] = TRUE;
        }

        if ($params[0])
        {
          $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                     'model must be unique but is not.';
        }
        else
        {
          $message = '«'.$alias.'» field of «'.$this->_object_name.'» '.
                     'model must not be unique but is.';
        }

        $this->assertTrue(
            $field->unique == $params[0],
            $message
        );
      break;

      default :

      break;
    }

  }


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