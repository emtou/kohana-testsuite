<?php
/**
 * Declares TestSuite_Core_Controller_Action_ACL
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller/action/acl.php
 * @since     2011-11-17
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Controller_Action_ACL
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller/action/acl.php
 */
abstract class TestSuite_Core_Controller_Action_ACL extends Kohana_Unittest_TestCase
{
  const NO_USER = 'NO_USER';

  protected $_controller_name = '';


  /**
   * Connect this user
   *
   * This method must be overloaded to fit application
   *
   * @param string $user_login user's login
   *
   * @return bool connection done ?
   */
  protected function _connect_user($user_login)
  {
    unset($user_login);

    throw new TestSuite_Exception(
      ':actionaclclass::_connect_user() '.
      'must be overridden by specific Action_ACL class.',
      array(':actionaclclass' => get_class($this))
    );
  }


  /**
   * Instanciate the controller
   *
   * @param string  $controller_name controller's name
   * @param Reponse $response        instance of a Reponse to link to the controller
   *
   * @return Controller instance of the controller
   */
  protected function _init_controller($controller_name, $response)
  {
    $controller_class_name = 'Controller_'.$controller_name;

    if ( ! class_exists($controller_class_name))
    {
      throw new PHPUnit_Framework_Error(
        'Controller :controllerclass does not exist',
        array(':controllerclass' => $controller_class_name)
      );
    }

    return new $controller_class_name(new Request(''), $response);
  }


  /**
   * Call controller's action and check expected response
   *
   * @param string $user_login          user's login
   * @param string $response_class_name Action_Response' class name
   *
   * @return Response|FALSE instance of the returned response or FALSE if something went wrong
   */
  protected function _test_action($user_login, $response_class_name)
  {
    $response = new Response(array());

    $controller = $this->_init_controller($this->_controller_name, $response);

    if ( ! class_exists($response_class_name))
    {
      throw new TestSuite_Exception(
        'Can\'t check expected reponse: :responseclass does not exist',
        array(':responseclass' => $response_class_name)
      );
      return FALSE;
    }
    $expected_response = new $response_class_name;

    if (method_exists($controller, $this->_action_name))
    {
      throw new TestSuite_Exception(
          'Can\'t check :controllername controller\'s :actionname action: '.
          'action does not exist',
          array(
            ':controllername' => $this->_controller_name,
            ':actionname'     => $this->_action_name,
          )
      );
      return FALSE;
    }
    $expected_response = new $response_class_name;

    try
    {
      if ( ! $this->_connect_user($user_login))
      {
        return FALSE;
      }

      $controller->before();
      $controller->{'action_'.$this->_action_name}();
    }
    catch (Exception $exception)
    {
      $message = (($expected_response instanceof Exception)
                  ?('should repond with a '.get_class($expected_response).' exception but does not: ')
                  :('should not throw an exception but does: '));

      $this->assertInstanceOf(
          get_class($exception),
          $expected_response,
          'When called by user '.$user_login.', '.
          $this->_controller_name.'::'.$this->_action_name.'() action '.
          $message.
          'exception '.get_class($exception).' received with message '.
          '«'.$exception->getMessage().'»'
      );

      return $response;
    }

    $this->assertThat(
        $expected_response,
        $this->logicalNot($this->isInstanceOf('Exception')),
        'When called by user '.$user_login.', '.
        $this->_controller_name.'::'.$this->_action_name.'() action '.
        'should throw an exception but does not throw any: '.
        get_class($expected_response).' exception expected'
    );

    return $response;
  }


  /**
   * Extra tests on controller's action response
   *
   * Could be overloaded to enable extra tests
   *
   * @param string   $user_login user's login
   * @param Response $response   instance of the action's response
   *
   * @return null
   */
  protected function _test_extras_for_user($user_login, $response)
  {
    return;
  }


  /**
   * List of expected reponses received when invoking action with a given user
   *
   * Should be overloaded to return a list of relevant tests
   *
   * @return array list of expected responses array(user_login, reponse_class_name)
   */
  public function provider_expected_response_for_user()
  {
    throw new PHPUnit_Framework_Error_Warning(
      'No expected statuses configured for class :actionaclclass',
      array(':actionaclclass' => get_class($this))
    );

    return array(
      array('dummy', 'dummy'),
    );
  }


  /**
   * Checks if an action invoked by a given user returns the expected ACL status
   *
   * @param string $user_login          user's login
   * @param string $response_class_name Action_Response' class name
   *
   * @return null
   *
   * @dataProvider provider_expected_response_for_user
   *
   * @test
   */
  public function test_action_with_user($user_login, $response_class_name)
  {
    if ($user_login == 'dummy')
      return;

    if (($response = $this->_test_action($user_login, $response_class_name)) === FALSE)
      return;

    $this->_test_extras_for_user($user_login, $response);
  }

} // end class TestSuite_Core_Controller_Action_ACL