<?php

class JsonValidationException extends Exception {};
class JsonSchemaException extends Exception {};

/**
 * JSON Schema Validator
 * 
 * Implements schema draft version 03, as defined at http://json-schema.org
 * 
 * @author Harold Asbridge <hasbridge@gmail.com>
 * @version 0.1
 */
class JsonValidator
{
    /**
     * @var stdClass
     */
    protected $schema;
    
    /**
     * Initialize validation object
     * 
     * @param string $schemaFile 
     */
    public function __construct($schemaFile)
    {
        if (!file_exists($schemaFile)) {
            throw new RuntimeException('Schema file not found');
        }
        $data = file_get_contents($schemaFile);
        $this->schema = json_decode($data);
        
        if ($this->schema === null) {
            throw new JsonSchemaException('Unable to parse JSON data - syntax error?');
        }
        
        // Validate schema itself
    }
    
    /**
     * Validate schema object
     * 
     * @param mixed $entity
     * @param string $entityName
     * 
     * @return JsonValidator
     */
    public function validate($entity, $entityName = null)
    {
        $entityName = $entityName ?: 'root';
        
        // Validate root type
        $this->validateType($entity, $this->schema, $entityName);
        
        /*
        // Validate additional properties
        if (isset($this->schema->additionalProperties) && !$this->schema->additionalProperties) {
            $extra = array_diff(array_keys((array)$jsonObject), array_keys((array)$this->schema->properties));
            if (count($extra)) {
                throw new JsonValidationException(sprintf('Additional properties [%s] not allowed for property [%s]', implode(',', $extra), $objectName));
            }
        }
         */
        
        return $this;
    }
    
    /**
     * Validate object properties
     * 
     * @param object $entity
     * @param object $schema
     * @param string $entityName
     * 
     * @return JsonValidator
     */
    protected function validateProperties($entity, $schema, $entityName)
    {
        foreach($schema->properties as $propertyName => $property) {
            if (isset($entity->{$propertyName})) {
                // Check type
                $path = $entityName . '.' . $propertyName;
                $this->validateType($entity->{$propertyName}, $property, $path);
            } else {
                // Check required
                if (isset($property->required) && $property->required) {
                    throw new JsonValidationException(sprintf('Missing required property [%s] for [%s]', $propertyName, $entityName));
                }
            }
        }
        
        return $this;
    }

    /**
     * Validate entity type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName
     * 
     * @return JsonValidator
     */
    protected function validateType($entity, $schema, $entityName)
    {
        if ($schema->type == 'object') {
            $this->checkTypeObject($entity, $schema, $entityName);
        } else if ($schema->type == 'number') {
            $this->checkTypeNumber($entity, $schema, $entityName);
        } else if ($schema->type == 'integer') {
            $this->checkTypeInteger($entity, $schema, $entityName);
        } else if ($schema->type == 'boolean') {
            $this->checkTypeBoolean($entity, $schema, $entityName);
        } else if ($schema->type == 'string') {
            $this->checkTypeString($entity, $schema, $entityName);
        } else if ($schema->type == 'array') {
            $this->checkTypeArray($entity, $schema, $entityName);
        } else if ($schema->type == 'null') {
            $this->checkTypeNull($entity, $schema, $entityName);
        }
        
        return $this;
    }
    
    /**
     * Check object type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName 
     * 
     * @return JsonValidator
     */
    protected function checkTypeObject($entity, $schema, $entityName) 
    {
        if (!is_object($entity)) {
            throw new JsonValidationException(sprintf('Expected object for [%s]', $entityName));
        } else {
            $this->validateProperties($entity, $schema, $entityName);
        }
        
        return $this;
    }
    
    /**
     * Check number type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName
     * 
     * @return JsonValidator 
     */
    protected function checkTypeNumber($entity, $schema, $entityName)
    {
        if (!is_numeric($entity)) {
            throw new JsonValidationException(sprintf('Expected number for [%s]', $entityName));
        }
        
        $this->checkMinimum($entity, $schema, $entityName);
        $this->checkMaximum($entity, $schema, $entityName);
        
        return $this;
    }
    
    /**
     * Check integer type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName 
     * 
     * @return JsonValidator
     */
    protected function checkTypeInteger($entity, $schema, $entityName)
    {
        if (!is_int($entity)) {
            throw new JsonValidationException(sprintf('Expected integer for [%s]', $entityName));
        }
        
        $this->checkMinimum($entity, $schema, $entityName);
        $this->checkMaximum($entity, $schema, $entityName);
        
        return $this;
    }
    
    /**
     * Check boolean type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName
     * 
     * @return JsonValidator 
     */
    protected function checkTypeBoolean($entity, $schema, $entityName)
    {
        if (!is_bool($entity)) {
            throw new JsonValidationException(sprintf('Expected boolean for [%s]', $entityName));
        }
        
        return $this;
    }
    
    /**
     * Check string type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName
     * 
     * @return JsonValidator
     */
    protected function checkTypeString($entity, $schema, $entityName)
    {
        if (!is_string($entity)) {
            throw new JsonValidationException(sprintf('Expected string for [%s]', $entityName));
        } else {
            $this->checkPattern($entity, $schema, $entityName);
            $this->checkMinLength($entity, $schema, $entityName);
            $this->checkMaxLength($entity, $schema, $entityName);
        }
        
        return $this;
    }
    
    /**
     * Check array type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName
     * 
     * @return JsonValidator 
     */
    protected function checkTypeArray($entity, $schema, $entityName)
    {
        if (!is_array($entity)) {
            throw new JsonValidationException(sprintf('Expected array for [%s]', $entityName));
        } else {
            $this->checkMinItems($entity, $schema, $entityName);
            $this->checkMaxItems($entity, $schema, $entityName);
            $this->checkUniqueItems($entity, $schema, $entityName);
        }
        
        return $this;
    }
    
    /**
     * Check null type
     * 
     * @param mixed $entity
     * @param object $schema
     * @param string $entityName
     * 
     * @return JsonValidator 
     */
    protected function checkTypeNull($entity, $schema, $entityName)
    {
        if (!is_null($entity)) {
            throw new JsonValidationException(sprintf('Expected null for [%s]', $entityName));
        }
        
        return $this;
    }
    
    protected function checkMinimum($entity, $schema, $entityName)
    {
        if (isset($schema->minimum) && $schema->minimum) {
            if ($entity < $schema->minimum) {
                throw new JsonValidationException(sprintf('Invalid value for [%s], minimum is [%s]', $entityName, $schema->minimum));
            }
        }
    }
    
    protected function checkMaximum($entity, $schema, $entityName)
    {
        if (isset($schema->maximum) && $schema->maximum) {
            if ($entity > $schema->maximum) {
                throw new JsonValidationException(sprintf('Invalid value for [%s], maximum is [%s]', $entityName, $schema->maximum));
            }
        }
    }
    
    public function checkPattern($entity, $schema, $entityName)
    {
        if (isset($schema->pattern) && $schema->pattern) {
            if (!preg_match($schema->pattern, $entity)) {
                throw new JsonValidationException(sprintf('String does not match pattern for [%s]', $entityName));
            }
        }
    }
    
    public function checkMinLength($entity, $schema, $entityName)
    {
        if (isset($schema->minLength) && $schema->minLength) {
            if (strlen($entity) < $schema->minLength) {
                throw new JsonValidationException(sprintf('String too short for [%s], minimum length is [%s]', $entityName, strlen($entity)));
            }
        }
    }
    
    public function checkMaxLength($entity, $schema, $entityName)
    {
        if (isset($schema->maxLength) && $schema->maxLength) {
            if (strlen($entity) > $schema->maxLength) {
                throw new JsonValidationException(sprintf('String too long for [%s], maximum length is [%s]', $entityName, $schema->maxLength));
            }
        }
    }
    
    public function checkMinItems($entity, $schema, $entityName)
    {
        if (isset($schema->minItems) && $schema->minItems) {
            if (count($entity) < $schema->minItems) {
                throw new JsonValidationException(sprintf('Not enough array items for [%s], minimum is [%s]', $entityName, $schema->minItems));
            }
        }
    }
    
    public function checkMaxItems($entity, $schema, $entityName)
    {
        if (isset($schema->maxItems) && $schema->maxItems) {
            if (count($entity) > $schema->maxItems) {
                throw new JsonValidationException(sprintf('Too many array items for [%s], maximum is [%s]', $entityName, $schema->maxItems));
            }
        }
    }
    
    public function checkUniqueItems($entity, $schema, $entityName)
    {
        if (isset($schema->uniqueItems) && $schema->uniqueItems) {
            if (count(array_unique($entity)) != count($entity)) {
                throw new JsonValidationException(sprintf('All items in array [%s] must be unique', $entityName));
            }
        }
    }
}