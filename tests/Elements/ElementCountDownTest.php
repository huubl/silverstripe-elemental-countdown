<?php

namespace Dynamic\Elements\CountDown\Tests;

use Dynamic\Elements\CountDown\Elements\ElementCountDown;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\View\ArrayData;

/**
 * Class ElementCountDownTest
 * @package Dynamic\Elements\Tests
 */
class ElementCountDownTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

    /**
     *
     */
    public function testGetType()
    {
        /** @var ElementCountDown $element */
        $element = $this->objFromFixture(ElementCountDown::class, 'endonly');
        $this->assertEquals($element->getType(), 'Countdown');
    }

    /**
     *
     */
    public function testValidate()
    {
        /** @var ElementCountDown $element */
        $element = ElementCountDown::create();
        $element->Title = 'Element';

        $valid = $element->validate();
        $this->assertInstanceOf(ValidationResult::class, $valid);
        $this->assertTrue($valid->isValid());

        $element->ID = 351120;
        $this->assertFalse($element->validate()->isValid());

        $element->ID = 0;
        $element->write();

        $element->Sort = 5;
        $this->assertTrue($element->validate()->isValid());

        $element->Title = 'New Title';
        $this->assertFalse($element->validate()->isValid());

        $element->End = DBDatetime::now();
        $this->assertTrue($element->validate()->isValid());
    }

    /**
     *
     */
    public function testGetClientConfig()
    {
        /** @var ElementCountDown $element */
        $element = $this->objFromFixture(ElementCountDown::class, 'endonly');
        $this->assertInstanceOf(ArrayData::class, $element->getClientConfig());
    }

    /**
     *
     */
    public function testEncodeArrayValues()
    {
        /** @var ElementCountDown $element */
        $element = $this->objFromFixture(ElementCountDown::class, 'elapse');
        $config = $element->getClientConfig();

        $this->assertEquals(json_decode($config->getField('End')), $element->End);
        $this->assertEquals(json_decode($config->getField('Elapse')), $element->Elapse);
    }
}
