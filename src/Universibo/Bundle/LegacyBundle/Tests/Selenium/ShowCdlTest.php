<?php
namespace Universibo\Bundle\LegacyBundle\Tests\Selenium;

class ShowCdlTest extends UniversiBOSeleniumTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testSimple()
    {
        $sentences = array (
                'CORSO DI LAUREA DI ECONOMIA AZIENDALE - 0891',
        );

        $this->openCommand('ShowCdl','&id_canale=6172');
        $this->assertSentences($sentences);
    }
    
    public function testNoAcademicalYear1()
    {
        $this->open('/v2.php?do=ShowCdl&id_canale=6172&anno_accademico=2100');
        $this->assertSentence('Sorry, the page you are looking for could not be found.');
    }
    
    public function testNoAcademicalYear2()
    {
    	$this->open('/v2.php?do=ShowCdl&id_canale=6172&anno_accademico=2000');
    	$this->assertSentence('Sorry, the page you are looking for could not be found.');
    }
}
