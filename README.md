TestSuite is a unittest module for Kohana 3 that lets you automate some of the tedious testing of controllers and models.

Requires Kohana 3.1.x

# Features

* Made for [Kohana 3.1.x](http://github.com/kohana/kohana)
* Jelly integration (ready for other ORMs)
* Completely extendable

# Installation

* clone this repository as a module

    ```shell
    git submodule add git://github.com/emtou/kohana-testsuite.git modules/testsuite
    git submodule update --init
    ```

* enable the module in your bootstrap


# Usage

## Testing controllers

* create a test class for a specific controller in your application/tests/classes directory

    ```php
    <?php
    defined('SYSPATH') or die('No direct access allowed!');
    class Controller_WelcomeTest extends TestSuite_Controller
    {
      public function __construct($name = NULL, array $data = array(), $data_name = '')
      {
        parent::__construct($name, $data, $data_name);
        $this->_object_name = 'Welcome';
        //$this->_register_private_method('_private_method_in_welcome_controller');
        //$this->_register_protected_method('_protected_method_in_welcome_controller');
        $this->_register_public_method('action_index');
        //$this->_register_public_static_method('_public_static_method_in_welcome_controller');
        //$this->_register_static_method('_static_method_in_welcome_controller');
      }
    }
    ```

## Testing models

### Jelly

* create a test class for a specific Jelly model in your application/tests/classes directory

    ```php
    <?php
    defined('SYSPATH') or die('No direct access allowed!');
    class Model_UserTest extends TestSuite_Model_Jelly
    {
      public function __construct($name = NULL, array $data = array(), $data_name = '')
      {
        parent::__construct($name, $data, $data_name);
        $this->_object_name = 'User';
        //$this->_register_private_method('_private_method_in_user_model');
        //$this->_register_protected_method('_protected_method_in_user_model');
        //$this->_register_public_method('_public_method_in_user_model');
        //$this->_register_public_static_method('_public_static_method_in_user_model');
        //$this->_register_static_method('_static_method_in_user_model');

        // Testing fields
        $this->_register_field(
          TestSuite_Field::factory('integer', 'id')
            ->add(TestSuite_Constraint::factory('primary'))
        );

        $this->_register_field(
          TestSuite_Field::factory('string', 'code')
            ->add(TestSuite_Constraint::factory('length', 1, 32))
            ->add(TestSuite_Constraint::factory('required'))
            ->add(TestSuite_Constraint::factory('unique'))
        );

        $this->_register_field(
          TestSuite_Field::factory('text', 'description')
            ->add(TestSuite_Constraint::factory('required', FALSE))
            ->add(TestSuite_Constraint::factory('unique', FALSE))
        );
      }
    }
    ```

Note that TestSuite_Model_Jelly automatically registers a public static
method initialize().


# Versions

* 0.1 (2011-06-24): tests controllers' and models' members and their visibility
* 0.2 (2011-06-27): tests models' fields