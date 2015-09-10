<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-10
 * Time: 11:11
 */

namespace Auth\Form;


use Zend\Form\Form;
use Zend\InputFilter\Factory;

class UserForm extends Form {

    public function __construct() {

        parent::__construct();

        $this->setAttribute('method', 'post');

        //Fields
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'Email : '
            ),
            'filters'   => array(
                'required' => true,
            )
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password : '
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Submit',
                'id' => 'submit',
            ),
        ));

        //Input Filter
        $factory = new Factory();
        $inputFilter = $factory->createInputFilter(array(
            'password' => array(
                'name'       => 'password',
                'required'   => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 8
                        ),
                    ),
                ),
            ),
            'email' => array(
                'name'       => 'email',
                'required'   => true,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            ),
        ));

        $this->setInputFilter($inputFilter);
    }
}