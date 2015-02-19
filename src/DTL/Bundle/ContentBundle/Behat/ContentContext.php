<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Behat;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Sulu\Component\Content\StructureInterface;
use Sulu\Component\Content\Mapper\ContentMapperRequest;
use Behat\Gherkin\Node\TableNode;
use Sulu\Bundle\ContentBundle\Behat\BaseStructureContext;
use Behat\WebApiExtension\Context;
use PHPUnit_Framework_Assert as Assert;
use Behat\WebApiExtension\Context\ApiClientAwareContext;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Exception\RequestException;
use SebastianBergmann\Diff\Differ;

/**
 * Behat context class for the ContentBundle
 */
class ContentContext extends BaseStructureContext implements SnippetAcceptingContext, ApiClientAwareContext
{
    /**
     * @var ClientInterface
     */
    private $client;

    private $lastResponse;

    private $lastRouteParams = array();

    /**
     * {@inheritDoc}
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Given I request the page template ":structureType" for webspace ":webspace" and locale ":locale"
     */
    public function requestTemplateForWebspaceAndLocale($structureType, $webspace, $locale)
    {
        $routeParams = array(
            'key' => $structureType,
            'webspace' => $webspace,
            'locale' => $locale
        );
        $uri = $this->generateUrl('dtl_content.content_form', $routeParams);
        $this->lastRouteParams = $routeParams;

        $this->getRequest($uri);
    }

    /**
     * @Then the structure template should be the same as the legacy API
     */
    public function thenPageShouldBeTheSameAsTheLegacyApi()
    {
        $uri = $this->generateUrl('sulu_content.content_form', $this->lastRouteParams);
        $newBody = (string) $this->lastResponse->getBody();

        /** @var Guzzle\Http\Message\Response */
        $response = $this->getRequest($uri);
        $legacyBody = (string) $response->getBody();

        $this->assertEqualsNoWhitespace($legacyBody, $newBody);
    }


    /**
     * @Given there exists a page template ":arg1"
     */
    public function thereExistsAPageTemplate($structureName, PyStringNode $data)
    {
        $this->createStructureTemplate('page', $structureName, $data->getRaw());
    }

    /**
     * @Given there exists a page template :arg1 with the following property configuration
     */
    public function thereExistsAPageTemplateWithTheFollowingPropertyConfiguration($pageName, PyStringNode $propertiesXml)
    {
        $this->createStructureWithProperties($pageName, $propertiesXml);
    }

    /**
     * @Given the following pages exist:
     */
    public function theFollowingPagesExist(TableNode $table)
    {
        $data = $table->getColumnsHash();

        $this->createStructures('page', $data);
    }

    /**
     * Prints last response body.
     *
     * @Then print response
     */
    public function printResponse()
    {
        $response = $this->lastResponse;

        echo sprintf(
            "%s %s => %d:\n%s",
            $this->request->getMethod(),
            $this->request->getUrl(),
            $response->getStatusCode(),
            $response->getBody()
        );
    }

    /**
     * @Then pause
     */
    public function andPause()
    {
        while (true) {
            sleep(1);
        }
    }

    protected function getStructurePaths($type)
    {
        $paths =$this->getContainer()->getParameter('dtl_content.structure.paths');
        return array($paths[$type]);
    }

    protected function generateUrl($routeName, $params)
    {
        return $this->getContainer()->get('router')->generate($routeName, $params);
    }

    protected function getRequest($uri)
    {
        $request = $this->createRequest('GET', $uri);
        return $this->sendRequest($request);
    }

    protected function createRequest($method, $uri)
    {
        $this->request = $this->client->createRequest($method, $uri);
        return $this->request;
    }

    protected function sendRequest(Request $request)
    {
        $authorization = base64_encode('test:test');
        $request->addHeaders(array(
            'Authorization' => 'Basic ' . $authorization
        ));

        try {
            $this->lastResponse = $this->client->send($request);
        } catch (RequestException $e) {
            $this->lastResponse = $e->getResponse();
        }

        return $this->lastResponse;
    }

    private function assertEqualsNoWhitespace($expected, $actual)
    {
        $diff = new Differ;
        $cleaner = function ($subject) {
            $lines = explode("\n", $subject);
            $lines = array_filter($lines, function ($line) {
                if (trim($line) == '') {
                    return false;
                }

                return true;
            });

            array_walk($lines, function (&$line) {
                $line = trim($line);
            });
            return implode("\n", $lines);
        };

        $expected = $cleaner($expected);
        $actual = $cleaner($actual);

        if ($expected != $actual) {
            echo $diff->diff($expected, $actual);
            Assert::assertTrue(false, 'Legacy and new responses do not match');
        }
    }
}
