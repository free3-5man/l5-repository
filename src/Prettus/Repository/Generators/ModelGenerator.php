<?php

namespace Prettus\Repository\Generators;

use Prettus\Repository\Generators\Migrations\SchemaParser;

/**
 * Class ModelGenerator
 * @package Prettus\Repository\Generators
 */
class ModelGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'model';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'models';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . '.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */

    public function getBasePath()
    {
        return config('repository.generator.basePath', app_path());
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        return array_merge(parent::getReplacements(), [
            'fillable'          => $this->getFillable(),
            'fields'            => $this->getFields(),
            'transform_aliases' => $this->getTransformAliases(),
            'table'             => $this->table,
        ]);
    }

    /**
     * Get the fillable attributes.
     *
     * @return string
     */
    public function getFillable()
    {
        if (!$this->fillable) {
            return '[]';
        }
        $results = '[' . PHP_EOL;

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $columnUpperCase = strtoupper($column);
            $results .= "\t\tself::FIELD_$columnUpperCase," . PHP_EOL;
        }

        return $results . "\t" . ']';
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser()
    {
        return new SchemaParser($this->fillable);
    }

    private function getFields()
    {
        $results = '';

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $results .= "\t/** Field name */" . PHP_EOL;
            $columnUpperCase = strtoupper($column);
            $results .= "\tconst FIELD_{$columnUpperCase} = '$column';" . PHP_EOL;
        }

        return $results;
    }

    private function getTransformAliases()
    {
        $results = '';

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $columnUpperCase = strtoupper($column);
            $results .= "\t\t\t'$column' => \$this->{self::FIELD_$columnUpperCase}," . PHP_EOL;
        }

        return $results;
    }
}
