This library provides JSON schema validation using the schema found at http://json-schema.org. 
Note that it is not yet feature complete, but does support basic validation. The JSON schema
draft can be found at http://tools.ietf.org/html/draft-zyp-json-schema-03

## Usage

    $someJson = '{"foo":"bar"}';
    $jsonObject = json_decode($someJson);
    
    $validator = new JsonValidator('/path/to/yourschema.json');
    
    $validator->validate($jsonObject);


## Supported Types

Types may be defined as either a single string type name, or an array of allowable
type names.

- string
- number
- integer
- boolean
- object
- array
- null
- any

## Supported Restrictions

Not all restrictions are yet supported, but here is a list of those which are:

- additionalItems
- required
- pattern (string)
- minLength (string)
- maxLength (string)
- minimum (number, integer)
- maximum (number, integer)
- enum (array)
- minItems (array)
- maxItems (array)
- uniqueItems (array)