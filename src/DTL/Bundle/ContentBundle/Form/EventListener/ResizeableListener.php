<?php

namespace DTL\Bundle\ContentBundle\Form\EventListener;

use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormEvent;

class ResizeableListener extends ResizeFormListener
{
    private $isMultiple;

    public function __construct($type, array $options = array(), $isMultiple)
    {
        $this->isMultiple = $isMultiple;

        parent::__construct($type, $options, true, true, true);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (false === $this->isMultiple) {
            // inbound value should be converted to array
            $event->setData(array($data));
        }

        parent::preSubmit($event);
    }
}
