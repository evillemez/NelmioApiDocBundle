<?php

namespace Nelmio\ApiDocBundle\Formatter;

/**
 * This formatter renders Api Doc information in the Swagger JSON format.
 * 
 * Read about Swagger here: 
 * 
 * Read about the Swagger spec here: 
 *
 * @author Evan Villemez
 */
class SwaggerFormatter extends AbstractFormatter
{
    private $apiBasePath;
    
    private $primitiveTypes = array(
        'string' => 'String',
        'integer' => 'int',
        'boolean' => 'boolean',
        'float' => 'float',
        'double' => 'double',
        'float' => 'float',
        'DateTime' => 'Date',
    );
    
    public function __construct($apiBasePath)
    {
        $this->apiBasePath = $apiBasePath;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function renderOne(array $data)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function render(array $collection)
    {
        $swaggerJson = array(
            'apiVersion' => '1',
            'swaggerVersion' => '1.1',
            'basePath' => $this->apiBasePath,
            //'resourcePath' => '', //???
            'apis' => $apis = array(),
            'models' => $models = array()
        );
        
//        die(sprintf("<pre>%s</pre>", print_r($collection, true)));
        
        foreach ($collection as $uri => $operations) {
            $apis[] = array(
                "path" => $uri,
                "operations" => array(),
            );

            foreach ($operations as $operation) {
                $op = array(
                    'httpMethod' => $operation['method'],
                    'summary' => $operation['description'],
                    'notes' => $operation['documentation'],
                );
                
                $params = array();
                $filters = array();
                $requirements = array();
                
                if (isset($operation['response'])) {
                    $this->parseModels($operation['response'], $models);
                    $op['responseClass'] = $this->parseSwaggerResponseClass($operation['response']);
                }
                
                if (isset($operation['parameters'])) {
                    $this->parseModels($operation['parameters'], $models);
                    $params = $operation['parameters'];
                }
                
                if (isset($operation['filters'])) {
                    $filters = $operation['filters'];
                }
                
                if (isset($operation['requirements'])) {
                    $requirements = $operation['requirements'];
                }

                $op['parameters'] = $this->parseSwaggerParameters($requirements, $filters, $params);

                $apis['operations'][] = $op;
            }
        }
        
        return json_encode($swaggerJson);
    }

    protected function parseSwaggerParameters($requirements = array(), $filters = array(), $params = array())
    {
        $parameters = array();
        
        foreach ($requirements as $requirement) {
            $parameters[] = $this->createSwaggerParameter($requirement, 'path');
        }
        
        foreach ($filters as $filter) {
            $parameters[] = $this->createSwaggerParameter($filter, 'query');
        }
        
        foreach ($params as $param) {
            $parameters[] = $this->createSwaggerParameter($param, 'body');
        }
        
        return $parameters;
    }

    protected function createSwaggerParameter(array $data, $paramType)
    {
        return array(
            'paramType' => $paramType,
            'name' => $data['name'],
            'description' => isset($data['description']) ? $data['description'] : "No description.",
            'required' => ('path' === $paramType) ? true : $data['required'],
            'dataType' => $this->getSwaggerDataType($type)
        );
    }
    
    protected function getSwaggerDataType($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }
    }

    protected function parseSwaggerResponseClass(array $data)
    {
        
    }

}
