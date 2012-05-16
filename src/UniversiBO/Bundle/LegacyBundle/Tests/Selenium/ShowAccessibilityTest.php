<?php
namespace UniversiBO\Bundle\LegacyBundle\Tests\Selenium;

class ShowAccessibilityTest extends UniversiBOSeleniumTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testSimple()
    {
        $sentences = array (
                'Dichiarazione di accessibilit',
                'vai all\'homepage',
                'vai al forum',
        );

        $this->open('/v2.php?do=ShowAccessibility');

        foreach($sentences as $sentence) {
            self::assertTrue($this->isTextPresent($sentence));
        }
    }
}
