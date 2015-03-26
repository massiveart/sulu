<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\WebsiteBundle\Controller;

use Sulu\Component\Content\StructureInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default Controller for rendering templates, uses the themes from the ClientWebsiteBundle
 * @package Sulu\Bundle\WebsiteBundle\Controller
 */
class DefaultController extends WebsiteController
{
    /**
     * Loads the content from the request (filled by the route provider) and creates a response with this content and
     * the appropriate cache headers
     * @param \Sulu\Component\Content\StructureInterface $structure
     * @param bool $preview
     * @param bool $partial
     * @return Response
     */
    public function indexAction(StructureInterface $structure, $preview = false, $partial = false)
    {
        $response = $this->renderStructure(
            $structure,
            array(),
            $preview,
            $partial
        );

        return $response;
    }

    /**
     * Creates a redirect for configured webspaces
     */
    public function redirectWebspaceAction(Request $request)
    {
        $url = $this->resolveRedirectUrl(
            $request->get('redirect'),
            $request->getUri()
        );

        return new RedirectResponse($url, 301);
    }

    /**
     * Creates a redirect for *.html to * (without html)
     */
    public function redirectAction(Request $request)
    {
        return new RedirectResponse($request->get('url'), 301);
    }

    /**
     * Resolve the redirect URL, appending any additional path data
     *
     * @param string $url Original webspace URI
     * @param string $redirectUrl  Redirect webspace URI
     * @param string $requestUri The actual incoming request URI
     *
     * @return string URL to redirect to
     */
    protected function resolveRedirectUrl($redirectUrl, $requestUri)
    {
        $requestInfo = $this->parseUrl($requestUri);

        $url = '';

        if ($redirectUrl) {
            $redirectInfo = $this->parseUrl($redirectUrl);
            $url .= sprintf('%s://%s', $requestInfo['scheme'], $redirectInfo['host']);

            if (isset($requestInfo['port'])) {
                $url .= ':' . $requestInfo['port'];
            }

            if (isset($redirectInfo['path'])) {
                $url .= $redirectInfo['path'];
            }
        }

        if (isset($requestInfo['path'])) {
            $url .= $requestInfo['path'];
            $url = rtrim($url, '/');
        }

        if (isset($requestInfo['query'])) {
            $url .= '?' . $requestInfo['query'];
        }

        if (isset($requestInfo['fragment'])) {
            $url .= '#' . $requestInfo['fragment'];
        }

        return $url;
    }

    /**
     * Prefix http to the URL if it is missing and
     * then parse the string using parse_url
     *
     * @param string
     *
     * @return string
     */
    private function parseUrl($url)
    {
        if (!preg_match('{^https?://}', $url)) {
            $url = 'http://' . $url;
        }

        return parse_url($url);
    }
}
