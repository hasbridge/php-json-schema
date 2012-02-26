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
    protected function getValidator($schemaFile)
    {
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
    
    public function testMissingProperties()
    {
        $v = $this->getValidator('missing-properties.json');
        
        $o = (object)array(
            'foo' => 'bar'
        );
        
        $v->validate($o);
    }
    
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
        $v = $this->getValidator('type/multitype.json');
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
        $v = $this->getValidator('type/string.json');
        $v->validate('foo');
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidString()
    {
        $v = $this->getValidator('type/string.json');
        $v->validate(1234);
    }
    
    public function testNumber()
    {
        $v = $this->getValidator('type/number.json');
        $v->validate(1);
        $v->validate(1.1);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidNumber()
    {
        $v = $this->getValidator('type/number.json');
        $v->validate('asdf');
    }
    
    public function testInteger()
    {
        $v = $this->getValidator('type/integer.json');
        $v->validate(1);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidInteger()
    {
        $v = $this->getValidator('type/integer.json');
        $v->validate('asdf');
    }
    
    public function testBoolean()
    {
        $v = $this->getValidator('type/boolean.json');
        $v->validate(true);
        $v->validate(false);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidBoolean()
    {
        $v = $this->getValidator('type/boolean.json');
        $v->validate('asdf');
    }
    
    public function testArray()
    {
        $v = $this->getValidator('type/array.json');
        $v->validate(array(1, 2, 3));
        $v->validate(array());
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidArray()
    {
        $v = $this->getValidator('type/array.json');
        $v->validate('asdf');
    }
    
    public function testNull()
    {
        $v = $this->getValidator('type/null.json');
        $v->validate(null);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidNull()
    {
        $v = $this->getValidator('type/null.json');
        $v->validate(1234);
    }
    
    public function testObject()
    {
        $v = $this->getValidator('type/object.json');
        $o = new stdClass();
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidObject()
    {
        $v = $this->getValidator('type/object.json');
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
    
    public function testPattern()
    {
        $v = $this->getValidator('pattern.json');
        $o = "ASDF";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidPattern()
    {
        $v = $this->getValidator('pattern.json');
        $o = "asdf";
        $v->validate($o);
    }
    
    public function testMinLength()
    {
        $v = $this->getValidator('minLength.json');
        $o = "foo";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMinLength()
    {
        $v = $this->getValidator('minLength.json');
        $o = "a";
        $v->validate($o);
    }
    
    public function testMaxLength()
    {
        $v = $this->getValidator('maxLength.json');
        $o = "foo";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMaxLength()
    {
        $v = $this->getValidator('maxLength.json');
        $o = "foo bar";
        $v->validate($o);
    }
    
    public function testMinItems()
    {
        $v = $this->getValidator('minItems.json');
        $o = array('foo', 'bar');
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMinItems()
    {
        $v = $this->getValidator('minItems.json');
        $o = array('foo');
        $v->validate($o);
    }
    
    public function testMaxItems()
    {
        $v = $this->getValidator('maxItems.json');
        $o = array('foo', 'bar');
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidMaxItems()
    {
        $v = $this->getValidator('maxItems.json');
        $o = array('foo', 'bar', 'baz');
        $v->validate($o);
    }
    
    public function testUniqueItems()
    {
        $v = $this->getValidator('uniqueItems.json');
        $o = array('foo', 'bar');
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidUniqueItems()
    {
        $v = $this->getValidator('uniqueItems.json');
        $o = array('foo', 'foo');
        $v->validate($o);
    }
    
    public function testEnumArray()
    {
        $v = $this->getValidator('enum-array.json');
        $o = array('foo', 'bar');
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidEnumArray()
    {
        $v = $this->getValidator('enum-array.json');
        $o = array('foo', 'baz');
        $v->validate($o);
    }
    
    public function testEnumString()
    {
        $v = $this->getValidator('enum-string.json');
        $o = 'foo';
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidEnumString()
    {
        $v = $this->getValidator('enum-string.json');
        $o = 'baz';
        $v->validate($o);
    }
    
    public function testFormatDateTime()
    {
        $v = $this->getValidator('format/date-time.json');
        $o = "2011-01-01T12:00:00Z";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatDateTime()
    {
        $v = $this->getValidator('format/date-time.json');
        $o = "asdf";
        $v->validate($o); 
    }
    
    public function testFormatDate()
    {
        $v = $this->getValidator('format/date.json');
        $o = "2011-01-01";
        $v->validate($o); 
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatDate()
    {
        $v = $this->getValidator('format/date.json');
        $o = "asdf";
        $v->validate($o); 
    }
    
    public function testFormatTime()
    {
        $v = $this->getValidator('format/time.json');
        $o = "12:00:00";
        $v->validate($o); 
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatTime()
    {
        $v = $this->getValidator('format/time.json');
        $o = "asdf";
        $v->validate($o); 
    }
    
    public function testFormatUtcMillisec()
    {
        $v = $this->getValidator('format/utc-millisec.json');
        $o = 12345;
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatUtcMillisec()
    {
        $v = $this->getValidator('format/utc-millisec.json');
        $o = -100;
        $v->validate($o);
    }
    
    public function testFormatColor()
    {
        $v = $this->getValidator('format/color.json');
        $o = "#CCC";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatColor()
    {
        $v = $this->getValidator('format/color.json');
        $o = "CCC";
        $v->validate($o);
    }
    
    public function testFormatStyle()
    {
        $v = $this->getValidator('format/style.json');
        $o = "background: transparent #FFF url('/path/too/image.jpg') no-repeat 5px 10px;";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatStyle()
    {
        $v = $this->getValidator('format/style.json');
        $o = "asdf";
        $v->validate($o);
    }
    
    public function testFormatPhone()
    {
        $v = $this->getValidator('format/phone.json');
        $o = "555-555-1234";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatPhone()
    {
        $v = $this->getValidator('format/phone.json');
        $o = "foo";
        $v->validate($o);
    }
    
    public function testFormatUri()
    {
        $v = $this->getValidator('format/uri.json');
        $o = "http://www.example.org/page.php?a=foo&b=%20bar";
        $v->validate($o);
    }
    
    /**
     * @expectedException Json\ValidationException
     */
    public function testInvalidFormatUri()
    {
        $v = $this->getValidator('format/uri.json');
        $o = "@^";
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