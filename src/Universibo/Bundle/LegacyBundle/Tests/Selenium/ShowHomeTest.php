<?php
namespace Universibo\Bundle\LegacyBundle\Tests\Selenium;

class ShowHomeTest extends UniversiBOSeleniumTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testSimple()
    {
        $sentences = [
            'Benvenuto in UniversiBO!',
            'la nuova versione della community e degli strumenti per la didattica ideato dagli studenti'
        ];

        $this->openPrefix('/');
        $this->assertSentences($sentences);
    }
}
