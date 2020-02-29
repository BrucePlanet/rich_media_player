<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 04/12/2014
 * Time: 15:28
 */

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

class IndexForm extends Form
{

    /**
     * Initialize the index form
     */
    public function initialize($entity = null, $options = array())
    {

        $category = new Text ("category");
        $category->setLabel("Category");
        $category->setFilters(array('striptags', 'string'));

        $this->add($category);

        $brand = new Text ("brand");
        $brand->setLabel("Brand");
        $brand->setFilters(array('striptags', 'string'));

        $this->add($brand);

        $manufacturer = new Text ("manufacturer");
        $manufacturer->setLabel("Manufacturer");
        $manufacturer->setFilters(array('striptags', 'string'));

        $this->add($manufacturer);

    }

}