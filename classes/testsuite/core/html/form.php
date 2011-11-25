<?php
/**
 * Declares TestSuite_Core_HTML_Form
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/html/form.php
 * @since     2011-06-24
 */

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides TestSuite_Core_HTML_Form
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
 * @link      https://github.com/emtou/kohana-testsuite/tree/master/classes/testsuite/core/html/form.php
 */
abstract class TestSuite_Core_HTML_Form
{
  const INPUT_SUBMIT = 'INPUT_SUBMIT';
  const INPUT_TEXT   = 'INPUT_TEXT';
  const SELECT       = 'SELECT';
  const TEXTAREA     = 'TEXTAREA';


  protected $_attributes = array(); /** list of key value attribute pairs */
  protected $_doc        = NULL;    /** DOMDocument instance of the HTML */
  protected $_element    = NULL;    /** DOMElement instance of the form */
  protected $_fields     = array(); /** fields' definitions for test() */
  protected $_formid     = NULL;    /** id of the form */
  protected $_html       = '';      /** HTML text defining the form */
  protected $_testcase   = NULL;    /** Kohana_Unittest_TestCase instance */
  protected $_xpath      = NULL;    /** COMXPath instance on the document */


  /**
   * Find a form element in a HTML text
   *
   * @param string                   $html     HTML text to search form into
   * @param Kohana_Unittest_TestCase $testcase PHPUnit testcase
   * @param string                   $id       optional id attribute of the form element
   *
   * @return TestSuite_Core_HTML_Form initialised form
   */
  public function __construct($html, Kohana_Unittest_TestCase $testcase, $id = NULL)
  {
    $this->_html     = $html;
    $this->_testcase = $testcase;

    if ( ! is_null($id))
    {
      $this->_attributes['id'] = $id;
    }
    elseif ( ! is_null($this->_formid))
    {
      $this->_attributes['id'] = $this->_formid;
    }
  }


  /**
   * Asserts a field matches a given option
   *
   * @param DOMElement $field   field instance
   * @param string     $key     option's key
   * @param mixed      $value   option's value
   * @param string     $message fail message
   *
   * @return null
   */
  protected function _assert_field_option(DOMElement $field, $key, $value, $message)
  {
    switch ($key)
    {
      case 'label':
        $labeltext = $this->_get_field_label_text($field, $message);
        $this->_testcase->assertEquals(
            $value,
            $labeltext,
            $message.': label '.$labeltext.' does not match expected value '.$value
        );
      break;

      case 'selected':
        $optionvalue = $this->_get_field_selected_optionvalue($field);
        $this->_testcase->assertEquals(
            $value,
            $optionvalue,
            $message.': selected option value '.$optionvalue.' does not match expected value '.$value
        );
      break;

      case 'value':
        $current_value = $this->_get_field_value($field);
        $this->_testcase->assertEquals(
            $value,
            $current_value,
            $message.': value does not match expectation'
        );
      break;

      case 'values':
        $options = $this->_get_field_options($field);
        $this->_testcase->assertEquals(
            $value,
            $options,
            $message.': values do not match expectation'
        );
      break;

      case 'visible':
        $visible = $this->_is_field_visible($field);
        $message = ($value
                    ?($message.': field should be visible but isn\'t')
                    :($message.': field should not be visible but is'));
        $this->_testcase->assertEquals(
            $value,
            $visible,
            $message
        );
      break;

      default:
        $this->_testcase->fail($message.': untestable option '.$key);
    }
  }


  /**
   * Find a field in the form
   *
   * @param string $tag     field's tag
   * @param string $name    name of the field
   * @param string $message fail message
   *
   * @return DOMElement found element
   */
  protected function _get_field($tag, $name, $message)
  {
    $fields = $this->_xpath->query('.//'.$tag.'[@name="'.$name.'"]', $this->_element);

    if ($fields->length == 0)
    {
      $this->_testcase->fail(
          $message.
          ': no '.$tag.' field with name '.$name.' has been found in the form'
      );
    }
    elseif ($fields->length > 1)
    {
      $this->_testcase->fail(
          $message.
          ': '.$fields->length.' '.$tag.' fields with name '.$name.' have been found in the form, expected only one'
      );
    }

    return $fields->item(0);
  }


  /**
   * Get a field's label element
   *
   * @param DOMElement $field field instance
   *
   * @return DOMElement|NULL
   */
  protected function _get_field_label(DOMElement $field)
  {
    $labels = $this->_xpath->query('.//label[@for="'.$field->getAttribute('name').'"]', $this->_element);

    if ($labels->length == 1)
    {
      return $labels->item(0);
    }

    return NULL;
  }


  /**
   * Get a field's label text
   *
   * @param DOMElement $field   field instance
   * @param string     $message fail message
   *
   * @return string
   */
  protected function _get_field_label_text(DOMElement $field, $message)
  {
    unset($message);

    return utf8_decode(trim($field->textContent));
  }

  /**
   * Get a select field's options
   *
   * @param DOMElement $field field instance
   *
   * @return DOMNodelist list of option nodes
   */
  protected function _get_field_options(DOMElement $field)
  {
    $options = array();

    foreach ($this->_xpath->query('.//option', $field) as $option)
    {
      $options[] = array($option->getAttribute('value'), utf8_decode(trim($option->textContent)));
    }

    return $options;
  }


  /**
   * Get a select field's selected option's value (NULL is none selected)
   *
   * @param DOMElement $field field instance
   *
   * @return string|NULL selected option's value
   */
  protected function _get_field_selected_optionvalue(DOMElement $field)
  {
    $selected = $this->_xpath->query('.//option[@selected]', $field);

    if ($selected->length == 0)
      return NULL;

    return utf8_decode(trim($selected->item(0)->getAttribute('value')));
  }


  /**
   * Get a field's value
   *
   * @param DOMElement $field field instance
   *
   * @return string field's value
   */
  protected function _get_field_value(DOMElement $field)
  {
    if ($field->tagName == 'textarea')
      return utf8_decode(trim($field->nodeValue));

    return utf8_decode(trim($field->getAttribute('value')));
  }


  /**
   * Get a field visibility
   *
   * @param DOMElement $field field's instance
   *
   * @return bool is field visible ?
   */
  protected function _is_field_visible($field)
  {
    $current_field = $field;
    while ( ! $current_field instanceof DOMDocument)
    {
      if ($current_field->hasAttribute('style')
          and preg_match('/display\s*:\s*none/', $current_field->getAttribute('style')))
      {
        return FALSE;
      }
      $current_field = $current_field->parentNode;
    }

    return TRUE;
  }


  /**
   * Parses the DOMDocument to find the form
   *
   * @param string $message fail message
   *
   * @return null
   */
  protected function _parse_form($message)
  {
    if (array_key_exists('id', $this->_attributes))
    {
      $this->_parse_form_with_id($this->_attributes['id'], $message);
    }
    else
    {
      $this->_parse_form_without_id($message);
    }
  }


  /**
   * Parses the DOMDocument to find the form with a given id
   *
   * @param string $id      form's id
   * @param string $message fail message
   *
   * @return null
   */
  protected function _parse_form_with_id($id, $message)
  {
    if (is_null($element = $this->_doc->getElementById($id)))
    {
      $this->_testcase->fail(
          $message.
          ': no element with id '.$id.' has been found in HTML'
      );
    }

    if ($element->tagName != 'form')
    {
      $this->_testcase->fail(
          $message.
          ': an element with id '.$id.' has been found in HTML '.
          'but it\'s not a form, it\'s a '.$element->tagName
      );
    }

    $this->_element = $element;
  }


  /**
   * Parses the DOMDocument to find the only form it contains
   *
   * @param string $message fail message
   *
   * @return null
   */
  protected function _parse_form_without_id($message)
  {
    $forms = $this->_doc->getElementsByTagName('form');

    if ($forms->length == 0)
    {
      $this->_testcase->fail(
          $message.
          ': no form has been found in HTML'
      );
    }
    elseif ($forms->length > 1)
    {
      $this->_testcase->fail(
          $message.
          ': '.$forms->length.'forms have been found in HTML, be more specific'
      );
    }

    $this->_element = $forms->item(0);
  }


  /**
   * Parses the HTML text to a DOMDocument
   *
   * @return null
   */
  protected function _parse_html_doc()
  {
    $this->_doc                     = new DOMDocument;
    $this->_doc->preserveWhiteSpace = FALSE;
    try
    {
      if ( ! $this->_doc->loadHTML($this->_html))
      {
        throw new Kohana_Exception('loadHTML failed');
      }
    }
    catch (Exception $exception)
    {
      $this->_testcase->fail(
          'Can\'t parse HTML to find form: '.
          'a '.get_class($exception).' exception has been thrown with message'.
          '«'.$exception->getMessage().'»'
      );
    }

    $this->_xpath = new DOMXPath($this->_doc);
  }


  /**
   * Perform tests on a given field
   *
   * @param string $type    field's type (one of this class' consts)
   * @param string $name    field's name
   * @param array  $options field's options
   * @param string $message fail message
   *
   * @return null
   */
  protected function _test_field($type, $name, $options, $message)
  {
    switch ($type)
    {
      case self::INPUT_SUBMIT:
        $this->assert_input('submit', $name, $options, $message);
      break;

      case self::INPUT_TEXT:
        $this->assert_input('text', $name, $options, $message);
      break;

      case self::SELECT:
        $this->assert_select($name, $options, $message);
      break;

      case self::TEXTAREA:
        $this->assert_textarea($name, $options, $message);
      break;

      default:
        $this->_testcase->fail($message.': unknown '.$type.' field type');
    }
  }


  /**
   * Add a value a configured field's option
   *
   * Chainable method.
   *
   * @param string $name  field's name
   * @param string $key   option's key
   * @param mixed  $value value to add
   *
   * @return this
   */
  public function add($name, $key, $value)
  {
    try
    {
      $this->_fields[$name]['options'][$key][] = $value;
    }
    catch (Exception $exception)
    {
      $this->_testcase->fail(
          'Can\'t add value to '.$name.' field\'s '.$key.' option: '.
          'Caught '.get_class($exception).' exception with message '.
          '«'.$exception->getMessage().'»'
      );
    }
  }


  /**
   * Asserts the form as a given attribute
   *
   * @param string $name    attribute's name
   * @param string $value   optional attribute's value
   * @param string $message optional fail message
   *
   * @return null
   */
  public function assert_attribute($name, $value = NULL, $message = NULL)
  {
    $this->assert_exists();

    if (is_null($message))
    {
      $message = 'Error on '.$name.' form attribute';
    }

    $this->_testcase->assertTrue(
        $this->_element->hasAttribute($name),
        $message.': attribute does not exist'
    );

    if ( ! is_null($value))
    {
      $this->_testcase->assertEquals(
          $this->_element->getAttribute($name),
          $value,
          $message.': attribute value should be '.$value.' but is '.$this->_element->getAttribute($name)
      );
    }
  }


  /**
   * Asserts the form is found in the HTML text
   *
   * Should be called just after instanciation but will be called
   * by all the other assert method
   *
   * @param string $message optional fail message
   *
   * @return null
   */
  public function assert_exists($message = NULL)
  {
    // Element has already been created: it exists!
    if ( ! is_null($this->_element))
      return;

    if (is_null($message))
    {
      $message = 'Form does not exist';
    }

    $this->_parse_html_doc();
    $this->_parse_form($message);
  }


  /**
   * Asserts the form contains a given INPUT field
   *
   * List of possible options :
   *   - label    (string)      : exact label found for the field
   *   - value    (string)      : field's value
   *   - visible  (bool)        : is this field visible ?
   *
   * @param string $type    input field's type
   * @param string $name    name of the input field
   * @param array  $options optional key value pairs of options
   * @param string $message optional fail message
   *
   * @return null
   */
  public function assert_input($type, $name, array $options = array(), $message = NULL)
  {
    $this->assert_exists();

    if (is_null($message))
    {
      $message = 'Error on '.$name.' input field';
    }

    $field = $this->_get_field('input', $name, $message);

    $this->_testcase->assertEquals(
        $type,
        $field->getAttribute('type'),
        $message.': field type is '.$field->getAttribute('type').' but should be '.$type
    );

    foreach ($options as $key => $value)
    {
      $this->_assert_field_option($field, $key, $value, $message);
    }
  }


  /**
   * Asserts the form contains a given select field
   *
   * List of possible options :
   *   - label    (string)      : exact label found for the field
   *   - selected (string|NULL) : selected option value (NULL for none)
   *   - values   (array)       : ordered name value pairs of select options
   *   - visible  (bool)        : is this field visible ?
   *
   * @param string $name    name of the select field
   * @param array  $options optional key value pairs of options
   * @param string $message optional fail message
   *
   * @return null
   */
  public function assert_select($name, array $options = array(), $message = NULL)
  {
    $this->assert_exists();

    if (is_null($message))
    {
      $message = 'Error on '.$name.' select field';
    }

    $field = $this->_get_field('select', $name, $message);

    foreach ($options as $key => $value)
    {
      $this->_assert_field_option($field, $key, $value, $message);
    }
  }


  /**
   * Asserts the form contains a given TEXTAREA field
   *
   * List of possible options :
   *   - label    (string)      : exact label found for the field
   *   - value    (string)      : field's value
   *   - visible  (bool)        : is this field visible ?
   *
   * @param string $name    name of the textarea field
   * @param array  $options optional key value pairs of options
   * @param string $message optional fail message
   *
   * @return null
   */
  public function assert_textarea($name, array $options = array(), $message = NULL)
  {
    $this->assert_exists();

    if (is_null($message))
    {
      $message = 'Error on '.$name.' textarea field';
    }

    $field = $this->_get_field('textarea', $name, $message);

    foreach ($options as $key => $value)
    {
      $this->_assert_field_option($field, $key, $value, $message);
    }
  }


  /**
   * Set a configured field's option
   *
   * Chainable method.
   *
   * @param string $name  field's name
   * @param string $key   option's key
   * @param mixed  $value value to set
   *
   * @return this
   */
  public function set($name, $key, $value)
  {
    try
    {
      $this->_fields[$name]['options'][$key] = $value;
    }
    catch (Exception $exception)
    {
      $this->_testcase->fail(
          'Can\'t set '.$name.' field\'s '.$key.' option: '.
          'Caught '.get_class($exception).' exception with message '.
          '«'.$exception->getMessage().'»'
      );
    }
  }


  /**
   * Perform every test on configured fields
   *
   * @param string $message optional fail message
   *
   * @return null
   */
  public function test($message = '')
  {
    if ( ! empty($message))
    {
      $message .= ': ';
    }

    foreach ($this->_fields as $name => $definition)
    {
      $field_message = $message.'Error on '.$name.' field';
      if (array_key_exists('message', $definition))
      {
        $field_message = $message.$definition['message'];
      }

      $options = array();
      if (array_key_exists('options', $definition))
      {
        $options = $definition['options'];
      }

      $this->_testcase->assertTrue(
          array_key_exists('type', $definition),
          $field_message.': no field type found in definition'
      );

      $this->_test_field($definition['type'], $name, $options, $field_message);
    }
  }

} // End class TestSuite_Core_HTML_Form