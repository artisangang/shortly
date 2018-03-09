<?php
use artisangang\shortly\ServiceProvider;
use artisangang\shortly\Tag;
use PHPUnit\Framework\TestCase;

//commnad: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ServiceProviderTest

require dirname(__FILE__) . '/Tags.php';

final class ServiceProviderTest extends TestCase
{


    public function testRegisterTag()
    {
        $s = $this->getInstance();

        $s->addTag(new SubscribeTag);

        $this->assertInstanceOf(Tag::class, $s->getTag('subscribe'));

    }

    public function testTagOutput()
    {

        $s = $this->getInstance();

        $this->registerTags($s);

        $this->assertEquals(
            'SubscribeTag',
            $s->parse('[subscribe]')
        );

    }

    public function testContextTagOutput()
    {

        $s = $this->getInstance();

        $this->registerTags($s);

        $content = $s->parse('[greetings]Harry[/greetings]');

        $this->assertRegexp('/Harry/', $content);

        $this->assertEquals('Hi Harry, How are you?', $content);


    }

    public function testAttributesOnlyTag()
    {

        $s = $this->getInstance();

        $this->registerTags($s);

        $s->parseAttributesOnly();

        $this->assertEquals(
            '<p data-context="SubscribeTag"></p>',
            $s->parse('<p data-context="[subscribe]"></p>')
        );

    }

    public function testGlobalFunctions()
    {

        $s = $this->getInstance();

        $this->registerTags($s);

        $s->allowGlobalFunctions();

        $this->assertEquals(
            'Test Global Functions',
            $s->parse('[ucwords:test global functions]')
        );


    }

    protected function registerTags($s)
    {
        $s->addTag(new SubscribeTag);
        $s->addTag(new GreetingsTag);
    }

    protected function getInstance()
    {
        return new ServiceProvider;
    }
}