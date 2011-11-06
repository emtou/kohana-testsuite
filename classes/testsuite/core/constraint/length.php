<?php
/**
 * Declares TestSuite_Core_Constraint_Length
 *
 * PHP version 5
 *
 * @group testsuite
 *
 * @category  TestSuite
 * @package   TestSuiteConstraint
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/constraint/length.php
 * @since     2011-06-24
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_Constraint_Length
 *
 * PHP version 5
 *
 * @group testsuite
 *
 * @category  TestSuite
 * @package   TestSuiteConstraint
 * @author    mtou <mtou@charougna.com>
 * @copyright 2011 mtou
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/constraint/length.php
 */
abstract class TestSuite_Core_Constraint_Length extends TestSuite_Constraint
{
  const MIN           = 'MIN';
  const MIN_MINUS_ONE = 'MIN_MINUS_ONE';
  const AVERAGE       = 'AVERAGE';
  const MAX           = 'MAX';
  const MAX_PLUS_ONE  = 'MAX_PLUS_ONE';


  /**
   * Returns the length of the field given a length type
   *
   * @param string $type type of length check
   *
   * @return int string length
   *
   * @throws TestSuite_Exception Unknown length constraint type :type
   */
  public function get_length($type)
  {
    $params = $this->params();

    switch ($type)
    {
      case TestSuite_Constraint_Length::MIN :
        return $params[0];

      case TestSuite_Constraint_Length::MIN_MINUS_ONE :
        return $params[0]-1;

      case TestSuite_Constraint_Length::AVERAGE :
        return intval(($params[0]+$params[0])/2);

      case TestSuite_Constraint_Length::MAX :
        return $params[1];

      case TestSuite_Constraint_Length::MAX_PLUS_ONE :
        return $params[1]+1;

      default :
        throw new TestSuite_Exception(
          'Unknown length constraint type :type',
          array(':type' => $type)
        );
    }
  }

} // End class TestSuite_Core_Constraint_Length