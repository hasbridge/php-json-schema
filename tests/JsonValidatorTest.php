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
     * @param string $schemaFile
     * 
     * @return JsonValidator 
     */
    protected function getValidator($schemaFile = null)
    {
        if (!$schemaFile) {
            $schemaFile = 'test.json';
        }
        
        return new Validator(TEST_DIR . '/mock/' . $schemaFile);
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
        $v = new Validator(TEST_DIR . '/mock/empty.json');
    }
    
    /**
     * @expectedException Json\SchemaException
     */
    public function testMissingProperties()
    {
        $v = $this->getValidator('missing-properties.json');
        
        $o = (object)array(
            'foo' => 'bar'
        );
        
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
     * Test multiple types for property
     */
    public function testMultiProp()
    {
        $v = $this->getValidator('multitype.json');
        $v->validate("asdf");
        $v->validate(1234);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testMissingRequired()
    {
        $v = $this->getValidator('required.json');
        $o = (object)array(
            'baz' => 'bar'
        );
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidAdditionalProperties()
    {
        $v = $this->getValidator('additionalProperties.json');
        $o = (object)array(
            'foo' => 'bar'
        );
        $v->validate($o);
    }
    
    public function testString()
    {
        $v = $this->getValidator('string.json');
        $v->validate('foo');
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidString()
    {
        $v = $this->getValidator('string.json');
        $v->validate(1234);
    }
    
    public function testNumber()
    {
        $v = $this->getValidator('number.json');
        $v->validate(1);
        $v->validate(1.1);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidNumber()
    {
        $v = $this->getValidator('number.json');
        $v->validate('asdf');
    }
    
    public function testInteger()
    {
        $v = $this->getValidator('integer.json');
        $v->validate(1);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidInteger()
    {
        $v = $this->getValidator('integer.json');
        $v->validate('asdf');
    }
    
    public function testBoolean()
    {
        $v = $this->getValidator('boolean.json');
        $v->validate(true);
        $v->validate(false);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidBoolean()
    {
        $v = $this->getValidator('boolean.json');
        $v->validate('asdf');
    }
    
    public function testArray()
    {
        $v = $this->getValidator('array.json');
        $v->validate(array(1, 2, 3));
        $v->validate(array());
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidArray()
    {
        $v = $this->getValidator('array.json');
        $v->validate('asdf');
    }
    
    public function testNull()
    {
        $v = $this->getValidator('null.json');
        $v->validate(null);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidNull()
    {
        $v = $this->getValidator('null.json');
        $v->validate(1234);
    }
    
    public function testObject()
    {
        $v = $this->getValidator('object.json');
        $o = new stdClass();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidObject()
    {
        $v = $this->getValidator('object.json');
        $v->validate('asdf');
    }
    
    public function testMinimum()
    {
        $v = $this->getValidator('minimum.json');
        $v->validate(1);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMinimum()
    {
        $v = $this->getValidator('minimum.json');
        $v->validate(0);
    }
    
    public function testMaximum()
    {
        $v = $this->getValidator('maximum.json');
        $v->validate(1);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMaximum()
    {
        $v = $this->getValidator('maximum.json');
        $v->validate(3);
    }
    
    public function testExclusiveMinimum()
    {
        $v = $this->getValidator('exclusiveMinimum.json');
        $v->validate(2);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidExclusiveMinimum()
    {
        $v = $this->getValidator('exclusiveMinimum.json');
        $v->validate(1);
    }
    
    public function testExclusiveMaximum()
    {
        $v = $this->getValidator('exclusiveMaximum.json');
        $v->validate(1);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidExclusiveMaximum()
    {
        $v = $this->getValidator('exclusiveMaximum.json');
        $v->validate(2);
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
    
    public function testItemsSchema()
    {
        $v = $this->getValidator('items-schema.json');
        $o = (object)array(
            'foo' => array(
                (object)array(
                    'bar' => 'baz'
                )
            )
        );
        
        $v->validate($o);
    }
    
    public function testItemsArray()
    {
        $v = $this->getValidator('items-array.json');
        $o = (object)array(
            'foo' => array('foo', 1)
        );
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidItemsArray()
    {
        $v = $this->getValidator('items-array.json');
        $o = (object)array(
            'foo' => array('foo', 1, true)
        );
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\SchemaException
     */
    public function testInvalidItemsValue()
    {
        $v = $this->getValidator('invalid-items.json');
        $o = (object)array(
            'foo' => array('blah')
        );
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidItemsSchemaProperty()
    {
        $v = $this->getValidator('items-schema.json');
        $o = (object)array(
            'foo' => array(
                (object)array(
                    'bar' => 1
                )
            )
        );
        
        $v->validate($o);
    }
    
    public function testDisallow()
    {
        $v = $this->getValidator('disallow.json');
        $o = (object)array(
            'foo' => 'bar'
        );
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidDisallow()
    {
        $v = $this->getValidator('disallow.json');
        $o = (object)array(
            'foo' => 123
        );
        
        $v->validate($o);
    }
    
    public function testDivisibleBy()
    {
        $v = $this->getValidator('divisibleBy.json');
        $o = 8;
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidDivisibleBy()
    {
        $v = $this->getValidator('divisibleBy.json');
        $o = 3;
        
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\SchemaException
     */
    public function testInvalidDivisibleByValue()
    {
        $v = $this->getValidator('invalid-divisibleBy.json');
        $o = 3;
        
        $v->validate($o);
    }
}