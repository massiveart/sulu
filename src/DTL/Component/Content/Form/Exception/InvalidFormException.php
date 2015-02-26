<?php

namespace DTL\Component\Content\Form\Exception;

use Symfony\Component\Form\FormInterface;

class InvalidFormException extends \Exception
{
    public function __construct(FormInterface $form)
    {
        $message = array();
        foreach ($form->getErrors() as $error) {
            $message[] = sprintf(
                '[%s] %s (%s)', 
                $error->getOrigin() ? $error->getOrigin()->getPropertyPath() : '-',
                $error->getMessage(),
                json_encode($error->getMessageParameters())
            );
        }

        parent::__construct(implode("\n", $message));
    }
}
