<?php
// @codingStandardsIgnoreStart
/**
 * Declares TestSuite_Core_Controller_Action_Steps
 *
 * @group testsuite
 *
 * @category  TestSuite
 * @package   TestSuiteController
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller/action/steps.php
 * @since     2011-11-24
 */

defined('SYSPATH') or die('No direct access allowed!');

/**
 * Declares TestSuite_Core_Controller_Action_Steps
 *
 * @group testsuite
 *
 * @category  TestSuite
 * @package   TestSuiteController
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/controller/action/steps.php
 */
abstract class TestSuite_Core_Controller_Action_Steps extends TestSuite_Controller_Action
{
  static $completed_steps = array('step-1');

  protected $_steps = array();


  /**
   * Call controller's action for a given step
   *
   * First, prepare uri and action's options.
   * Then, call the action.
   *
   * @param int   $step_nb    step number
   * @param array $definition step's definition's array
   *
   * return bool action called as expected ?
   */
  protected function _step_call_action($step_nb, array $definition)
  {
    $options = $this->_step_get_action_options($definition);

    if (array_key_exists('uri', $definition['action']))
    {
      $this->_uri = $definition['action']['uri'];
    }

    return $this->_call_action($options);
  }


  /**
   * Prepare controller's action's option's array for a given step's definition
   *
   * Add method and posts keys from the definition to the options
   *
   * @param array $definition step's definition
   *
   * @return array controller's action's option's array
   */
  protected function _step_get_action_options(array $definition)
  {
    $options = array();

    if (array_key_exists('method', $definition['action']))
    {
      $options['method'] = $definition['action']['method'];
    }

    if (array_key_exists('posts', $definition['action']))
    {
      $options['posts'] = $definition['action']['posts'];
    }

    return $options;
  }


  /**
   * Perform a given step's tests
   *
   * @param int   $step_nb    step number
   * @param array $definition step's definition's array
   *
   * @return null
   */
  protected function _test_step($step_nb, array $definition)
  {
    $this->_step_call_action($step_nb, $definition);
  }


  /**
   * Provides all configured step numbers to feed self::test_step()
   *
   * @return array array of step numbers
   */
  public function provider_step_numbers()
  {
    $array = array();

    for ($nb=0; $nb < count($this->_steps); ++$nb)
    {
      $array[] = array($nb);
    }

    return $array;
  }


  /**
   * Perform a given step's tests
   *
   * Don't perform the test if previous step has not completed
   *
   * @param int $step_nb step number (starts with zero)
   *
   * @return null
   *
   * @dataProvider provider_step_numbers
   *
   * @test
   */
  public function test_step($step_nb)
  {
    if ( ! in_array('step'.$step_nb, self::$completed_steps)
         and in_array('step'.( (string) ($step_nb-1)), self::$completed_steps))
    {
      $this->_test_step($step_nb, $this->_steps[$step_nb]);
      self::$completed_steps[] = 'step'.$step_nb;
    }
  }

} // end class TestSuite_Core_Controller_Action_Steps