<?php

use Json\Validator;

/**
 * @covers Json\Validator
 */
class JsonValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get mock object
     * 
     * @return stdClass
     */
    protected function getTestObject()
    {
        $o = new stdClass();
        $o->stringProp = "AB";
        $o->arrayProp = array('foo', 'bar');
        $o->numberProp = 1.1;
        $o->integerProp = 2;
        $o->booleanProp = false;
        $o->nullProp = null;
        $o->anyProp = 1;
        $o->multiProp = "foo";
        $o->customProp = 'asdf';
        
        $o->dateTimeFormatProp = '2011-12-14T09:06:00Z';
        $o->dateFormatProp = '2011-12-14';
        $o->timeFormatProp = "09:00:00";
        $o->utcMillisecFormatProp = 123456789;
        $o->colorFormatProp = "#000000";
        $o->styleFormatProp = "background: #FFF url('foo.png') no-repeat 0px 0px;";
        $o->phoneFormatProp = "555-555-1234";
        $o->uriFormatProp = "https://www.google.com/";
        
        $o->objectProp = new stdClass();
        $o->objectProp->foo = 'bar';
        
        return $o;
    }
    
    /**
     * Get validator object
     * 
     * @return JsonValidator 
     */
    protected function getValidator()
    {
        return new Validator(TEST_DIR . '/mock/test-schema.json');
    }
    
    public function testConstruct()
    {
        $v = new Validator(TEST_DIR . '/mock/test-schema.json');
    }

    /**
     * @expectedException Json\SchemaException
     */
    public function testSchemaNotFound()
    {
        $v = new Validator('asdf');
    }
    
    /**
     * @expectedException Json\SchemaException
     */
    public function testInvalidSchema()
    {
        $v = new Validator(TEST_DIR . '/mock/empty-schema.json');
    }
    
    /**
     * Test a valid object
     */
    public function testValidObject()
    {
        $o = $this->getTestObject();
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * Test multiple types for property
     */
    public function testMultiProp()
    {
        $o = $this->getTestObject();
        $v = $this->getValidator();
        $v->validate($o);
        
        $o->multiProp = 1234;
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testMissingType()
    {
        $v = new Validator(TEST_DIR . '/mock/missing-type.json');
        
        $o = (object)array(
            'foo' => 'bar'
        );
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testMissingRequired()
    {
        $o = $this->getTestObject();
        unset($o->stringProp);
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidAdditionalProperties()
    {
        $o = $this->getTestObject();
        $o->foo = 'bar';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidString()
    {
        $o = $this->getTestObject();
        $o->stringProp = 1234;
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidNumber()
    {
        $o = $this->getTestObject();
        $o->numberProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidInteger()
    {
        $o = $this->getTestObject();
        $o->integerProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidBoolean()
    {
        $o = $this->getTestObject();
        $o->booleanProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidArray()
    {
        $o = $this->getTestObject();
        $o->arrayProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidNull()
    {
        $o = $this->getTestObject();
        $o->nullProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidObject()
    {
        $o = $this->getTestObject();
        $o->objectProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMinimum()
    {
        $o = $this->getTestObject();
        $o->numberProp = 0;
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMaximum()
    {
        $o = $this->getTestObject();
        $o->numberProp = 100;
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidExclusiveMinimum()
    {
        $o = $this->getTestObject();
        $o->integerProp = 1;
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidExclusiveMaximum()
    {
        $o = $this->getTestObject();
        $o->integerProp = 3;
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidPattern()
    {
        $o = $this->getTestObject();
        $o->stringProp = "1234";
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMinLength()
    {
        $o = $this->getTestObject();
        $o->stringProp = "a";
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMaxLength()
    {
        $o = $this->getTestObject();
        $o->stringProp = "abcd";
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMinItems()
    {
        $o = $this->getTestObject();
        $o->arrayProp = array();
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMaxItems()
    {
        $o = $this->getTestObject();
        $o->arrayProp = array('a', 'b', 'c', 'd');
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidUniqueItems()
    {
        $o = $this->getTestObject();
        $o->arrayProp = array('a', 'a');
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidArrayEnum()
    {
        $o = $this->getTestObject();
        $o->arrayProp = array('foo', 'blah');
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidStringEnum()
    {
        $v =  new Validator(TEST_DIR . '/mock/enum-string.json');
        $o = (object)array(
            'foo' => 'Bar2'
        );
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\SchemaException
     */
    public function testInvalidEnum()
    {
        $v =  new Validator(TEST_DIR . '/mock/invalid-enum.json');
        $o = (object)array(
            'foo' => 'Bar'
        );
        
        $v->validate($o);
    }

    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatDateTime()
    {
        $o = $this->getTestObject();
        $o->dateTimeFormatProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatDate()
    {
        $o = $this->getTestObject();
        $o->dateFormatProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatTime()
    {
        $o = $this->getTestObject();
        $o->timeFormatProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatUtcMillisec()
    {
        $o = $this->getTestObject();
        $o->utcMillisecFormatProp = -100;
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatColor()
    {
        $o = $this->getTestObject();
        $o->colorFormatProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatStyle()
    {
        $o = $this->getTestObject();
        $o->styleFormatProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatPhone()
    {
        $o = $this->getTestObject();
        $o->phoneFormatProp = 'asdf';
        $v = $this->getValidator();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatUri()
    {
        $o = $this->getTestObject();
        $o->uriFormatProp = '@*<>';
        $v = $this->getValidator();
        $v->validate($o);
    }
}