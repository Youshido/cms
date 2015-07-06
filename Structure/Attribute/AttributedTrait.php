<?php
/*
 * This file is a part of cms project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:46 PM 6/24/15
 */

namespace Youshido\CMSBundle\Structure\Attribute;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Form;
use Youshido\CMSBundle\Service\AttributeService;

trait AttributedTrait
{
    /**
     * @var ArrayCollection $attributes
     * @ORM\Column(name="attributes", type="object")
     */
    protected $attributes;

    /**
     * @var Form
     */
    public $attributesForm;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

    public function setAttributes($attributes)
    {
        $this->attributes = new ArrayCollection();

        foreach ($attributes as $key => $info) {
            if (is_object($info) && $info instanceof BaseAttribute) {
                $source = AttributeService::getAttributeForType($info->getType(), $info);

                if($source) {
                    $source->setValue($info->getValue());
                }
            }
            if (!empty($source)) {
                $this->attributes[$key] = clone $source;
            }
        }
        return $this;
    }

    public function applyAttributesToObject()
    {
        $data = $this->getAttributesViewValues();
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function loadAttributesDefaultValues()
    {
        foreach($this->getAttributes() as $attr) {
            if (!$attr->getValue()) {
                $attr->setValue($attr->getDefaultValue());
            }
        }
    }


    public function getAttributesViewValues()
    {
        $data = array();
        foreach ($this->attributes as $attr) {
            /**
             * @var BaseAttribute $attr
             */
            $data = array_merge($data, $attr->getViewData());
        }
        return $data;
    }

    /**
     * @return BaseAttribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttributesFormFields()
    {
        $fields = array();
        foreach ($this->attributes as $attr) {
            /**
             * @var BaseAttribute $attr
             */
            $fields = array_merge($fields, $attr->getFormWidgetInfo());
        }
        return $fields;
    }

    public function refreshAttributes()
    {
        $attributes       = $this->attributes;
        $this->attributes = new ArrayCollection();
        foreach ($attributes as $value) {
            $this->attributes->add($value);
        }
    }


}