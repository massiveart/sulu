<?php

namespace vendor\sulu\sulu\src\DTL\Bundle\ContentBundle\RoutingAuto;

class SuluRouteLoader
{
    public function __construct(
    )
    {}

    public function loadByResourceLocator($resourceLocator, $webspaceKey, $locale)
    {
        $resourceLocator = ltrim($resourceLocator, '/');
        $routePath = $this->sessionManager->getRoutePath($webspaceKey, $locale);
        if ($resourceLocator !== '') {
            $routePath = sprintf('%s/%s', $routesPath, $resourceLocator);
        }

        $route = $this->documentManager->find(null, $routePath);

        if (null === $route) {
            throw new ResourceLocatorNotFoundException(sprintf(
                'Could not find route at "%s"'
            ), $routePath);
        }

        if ($route->getType() === AutoRouteInterface::TYPE_REDIRECT) {

        }

        if ($route->hasProperty('sulu:content') && $route->hasProperty('sulu:history')) {
            if (!$route->getPropertyValue('sulu:history')) {
                /** @var NodeInterface $content */
                $content = $route->getPropertyValue('sulu:content');

                return $content->getIdentifier();
            } else {
                // get path from history node
                /** @var NodeInterface $realPath */
                $realPath = $route->getPropertyValue('sulu:content');

                throw new ResourceLocatorMovedException(
                    $this->getResourceLocator($realPath->getPath(), $webspaceKey, $locale, $segmentKey),
                    $realPath->getIdentifier()
                );
            }
        } else {
            throw new ResourceLocatorNotFoundException();
        }
    }
    }
}
