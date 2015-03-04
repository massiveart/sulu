<?php 
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
namespace DTL\Component\Content\PhpcrOdm\EventSubscriber;

use DTL\Bundle\ContentBundle\Document\DocumentName;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\Event;
use PHPCR\Util\UUIDHelper;
use Sulu\Component\Security\Authentication\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DTL\Component\Content\Document\DocumentInterface;

/**
 * Manage the creator and changer (a.k.a. created by and last modified by)
 * values on Documents before they are persisted.
 */
class BlameSubscriber implements EventSubscriber
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Event::prePersist,
            Event::preUpdate
        );
    }

    /**
     * Handle prePersist
     *
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $this->handleBlame($event, true);
    }

    /**
     * Handle preUpdate
     *
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->handleBlame($event);
    }


    /**
     * @param LifecycleEventArgs $event
     * @param boolean $setCreator If we should set the creator (i.e. if this is a new object)
     */
    private function handleBlame(LifecycleEventArgs $event, $setCreator = false)
    {
        $document = $event->getObject();

        if (!$document instanceof DocumentInterface) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return;
        }

        if (!$token->isAuthenticated()) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            throw new \RuntimeException(sprintf(
                'Expected user object to be instance of Sulu UserInterface, got "%s"',
                is_object($user) ? get_class($user) : gettype($user)
            ));
        }

        if ($setCreator && !$document->getCreator()) {
            $document->setCreator($user->getId());
        }

        $document->setChanger($user->getId());
    }
}
