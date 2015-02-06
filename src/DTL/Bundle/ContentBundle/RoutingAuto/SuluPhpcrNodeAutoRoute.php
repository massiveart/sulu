<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
namespace DTL\Bundle\ContentBundle\RoutingAuto;

use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;
use PHPCR\NodeInterface;

class SuluPhpcrNodeAutoRoute implements AutoRouteInterface
{
    const PROPERTY_HISTORY = 'sulu:history';
    const PROPERTY_CONTENT = 'sulu:content';
    const PROPERTY_TAG = 'sulu:autoroutetag';

    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }
    
    /**
     * Set a tag which can be used by a database implementation
     * to distinguish a route from other routes as required
     *
     * @param string $tag
     */
    public function setAutoRouteTag($tag)
    {
        $this->node->setProperty(self::PROPERTY_TAG, $tag);
    }

    /**
     * Return the auto route tag
     *
     * @return string
     */
    public function getAutoRouteTag()
    {
        $this->node->setPropertyValueWithDefault(self::AUTO_ROUTE_TAG_PROPERTY, null);
    }

    /**
     * TODO: This method is coupled to the adapter, which is not fine.
     *
     *       https://github.com/symfony-cmf/RoutingAuto/issues/30
     */
    public function setType($mode)
    {
        switch ($mode) {
            case AutoRouteInterface::TYPE_PRIMARY:
                $node->setProperty(self::PROPERTY_HISTORY, false);
                break;
            case AutoRouteInterface::TYPE_REDIRECT:
                $node->setProperty(self::PROPERTY_HISTORY, true);
                break;
            default:
                throw new \InvalidArgumentException(sprintf(
                    'Unknown auto route mode "%s"',
                    $mode
                ));
        }
    }

    /**
     * For use in the REDIRECT mode, specifies the AutoRoute
     * that the AutoRoute should redirect to.
     *
     * @param AutoRouteInterface AutoRoute to redirect to.
     */
    public function setRedirectTarget(AutoRouteInterface $autoTarget)
    {
        throw new \BadMethodCallException(
            'No implemented, handled by the adapter'
        );
    }

    /**
     * Return the redirect target (when the auto route is of type
     * REDIRECT).
     *
     * @return AutoRouteInterface
     */
    public function getRedirectTarget()
    {
        return $this->node->getPropertyValue(self::PROPERTY_CONTENT);
    }

    public function getContent()
    {
        return $this->node->getPropertyValue(self::PROPERTY_CONTENT);
    }

    public function getRouteKey()
    {
        return '';
    }

    public function getNode()
    {
        return $this->node;
    }
}
