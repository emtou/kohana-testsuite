<?php
/**
 * Declares TestSuite_Core
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core.php
 * @since     2011-06-23
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core.php
 */
abstract class TestSuite_Core extends Kohana_Unittest_TestCase
{
  protected $_object            = NULL;
  protected $_object_class_name = '';
  protected $_object_name       = '';
  protected $_private_methods   = array();
  protected $_protected_methods = array();
  protected $_public_methods    = array();
  protected $_static_methods    = array();


  /**
   * Asserts a method exists in the object
   *
   * @param string $method_name method's name
   *
   * @return null
   */
  protected function _assert_method_exists($method_name)
  {
    $this->assertTrue(
        method_exists($this->_object, $method_name),
        $method_name.'() method does not exist.'
    );
  }


  /**
   * Registers a private method for this object
   *
   * @param string $method_name method's name
   *
   * @return null
   */
  protected function _register_private_method($method_name)
  {
    $this->_private_methods[] = $method_name;
  }


  /**
   * Registers a protected method for this object
   *
   * @param string $method_name method's name
   *
   * @return null
   */
  protected function _register_protected_method($method_name)
  {
    $this->_protected_methods[] = $method_name;
  }


  /**
   * Registers a public method for this object
   *
   * @param string $method_name method's name
   *
   * @return null
   */
  protected function _register_public_method($method_name)
  {
    $this->_public_methods[] = $method_name;
  }


  /**
   * Registers a public static method for this object
   *
   * @param string $method_name method's name
   *
   * @return null
   */
  protected function _register_public_static_method($method_name)
  {
    $this->_register_public_method($method_name);
    $this->_register_static_method($method_name);
  }


  /**
   * Registers a static method for this object
   *
   * @param string $method_name method's name
   *
   * @return null
   */
  protected function _register_static_method($method_name)
  {
    $this->_static_methods[] = $method_name;
  }


  // *******************************************************************
  // End of protected methods
  // *******************************************************************
  // Start of public methods
  // *******************************************************************

  /**
   * Provides registered private method's names
   *
   * If no private method has been registered, provides a dummy method name
   *
   * @return array list of private method's names
   */
  public function provider_private_methods_names()
  {
    if (sizeof($this->_private_methods) == 0)
      return array(array('dummy'));

    $private_methods_names = array();

    foreach ($this->_private_methods as $private_methods_name)
    {
      $private_methods_names[] = array($private_methods_name);
    }

    return $private_methods_names;
  }


  /**
   * Provides registered protected method's names
   *
   * If no protected method has been registered, provides a dummy method name
   *
   * @return array list of protected method's names
   */
  public function provider_protected_methods_names()
  {
    if (sizeof($this->_protected_methods) == 0)
      return array(array('dummy'));

    $protected_methods_names = array();

    foreach ($this->_protected_methods as $protected_methods_name)
    {
      $protected_methods_names[] = array($protected_methods_name);
    }

    return $protected_methods_names;
  }


  /**
   * Provides registered public method's names
   *
   * If no public method has been registered, provides a dummy method name
   *
   * @return array list of public method's names
   */
  public function provider_public_methods_names()
  {
    if (sizeof($this->_public_methods) == 0)
      return array(array('dummy'));

    $public_methods_names = array();

    foreach ($this->_public_methods as $public_methods_name)
    {
      $public_methods_names[] = array($public_methods_name);
    }

    return $public_methods_names;
  }


  /**
   * Provides registered static method's names
   *
   * If no static method has been registered, provides a dummy method name
   *
   * @return array list of static method's names
   */
  public function provider_static_methods_names()
  {
    if (sizeof($this->_static_methods) == 0)
      return array(array('dummy'));

    $static_methods_names = array();

    foreach ($this->_static_methods as $static_methods_name)
    {
      $static_methods_names[] = array($static_methods_name);
    }

    return $static_methods_names;
  }


  /**
   * Global testCase initialization
   *
   * Creates a local instance of the object
   *
   * @return null
   */
  public function setUp()
  {
    parent::setUp();

    if (class_exists($this->_object_class_name))
    {
      $this->_object = new $this->_object_class_name;
    }
  }


  /**
   * Clean up object instance
   *
   * @return null
   */
  public function tearDown()
  {
    unset($this->_object);
  }


  /**
   * Checks the local instance of the object
   *
   * @return null
   *
   * @test
   */
  public function test_object()
  {
    $this->assertInstanceOf($this->_object_class_name, $this->_object);
  }


  /**
   * Checks if registered private methods exist
   *
   * @param string $method_name name of the private method to check
   *
   * @return null
   *
   * @dataProvider provider_private_methods_names
   *
   * @test
   */
  public function test_private_method($method_name)
  {
    if ($method_name == 'dummy')
      return;

    $this->_assert_method_exists($method_name);

    $reflector = new ReflectionMethod($this->_object_class_name, $method_name);
    $this->assertTrue(
        $reflector->isPrivate(),
        $method_name.'() method should be private.'
    );
  }


  /**
   * Checks if registered protected methods exist
   *
   * @param string $method_name name of the protected method to check
   *
   * @return null
   *
   * @dataProvider provider_protected_methods_names
   *
   * @test
   */
  public function test_protected_method($method_name)
  {
    if ($method_name == 'dummy')
      return;

    $this->_assert_method_exists($method_name);

    $reflector = new ReflectionMethod($this->_object_class_name, $method_name);
    $this->assertTrue(
        $reflector->isProtected(),
        $method_name.'() method should be protected.'
    );
  }


  /**
   * Checks if registered public methods exist
   *
   * @param string $method_name name of the public method to check
   *
   * @return null
   *
   * @dataProvider provider_public_methods_names
   *
   * @test
   */
  public function test_public_method($method_name)
  {
    if ($method_name == 'dummy')
      return;

    $this->_assert_method_exists($method_name);

    $reflector = new ReflectionMethod($this->_object_class_name, $method_name);
    $this->assertTrue(
        $reflector->isPublic(),
        $method_name.'() method should be private.'
    );
  }


  /**
   * Checks if registered static methods exist
   *
   * @param string $method_name name of the static method to check
   *
   * @return null
   *
   * @dataProvider provider_static_methods_names
   *
   * @test
   */
  public function test_static_method($method_name)
  {
    if ($method_name == 'dummy')
      return;

    $this->_assert_method_exists($method_name);

    $reflector = new ReflectionMethod($this->_object_class_name, $method_name);
    $this->assertTrue(
        $reflector->isStatic(),
        $method_name.'() method should be private.'
    );
  }

} // End class TestSuite_Core