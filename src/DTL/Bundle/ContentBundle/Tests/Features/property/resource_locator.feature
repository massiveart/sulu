Feature: Resource locator type
    In order for the user to enter the resource locator for a document
    As the sulu user interface
    I need to be able to retrieve the template and modify data

    Scenario: Foo
        Given there exists a page template "hotel_page" with the following property configuration
            """
            <property name="foo" type="resource_locator" mandatory="true">
                <meta>
                    <title lang="de">Titel</title>
                    <title lang="en">Title</title>
                </meta>

                <tag name="sulu.node.name"/>
                <tag name="sulu.rlp.part"/>
            </property>
            """
        And I request the page template "hotel_page" for webspace "sulu_io" and locale "en"
        Then the structure template should be the same as the legacy API
