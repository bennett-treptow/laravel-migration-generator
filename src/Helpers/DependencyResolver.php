<?php

namespace LaravelMigrationGenerator\Helpers;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Directed;
use LaravelMigrationGenerator\Definitions\IndexDefinition;

class DependencyResolver
{
    const SEPARATOR = '#';

    /** @var array|\LaravelMigrationGenerator\Definitions\TableDefinition[] */
    private array $tableDefinitions;

    protected Graph $graph;

    public function __construct(array $tableDefinitions)
    {
        $this->tableDefinitions = $tableDefinitions;
        $this->buildGraph();
    }

    private function buildGraph()
    {
        $graph = new Graph();

        foreach ($this->tableDefinitions as $tableDefinition) {
            $foreignIndices = collect($tableDefinition->getIndexDefinitions())
                ->filter(function (IndexDefinition $def) {
                    return $def->getIndexType() == 'foreign';
                });
            if ($foreignIndices->count() > 0) {
                $tableName = $tableDefinition->getTableName();
                if (! $graph->hasVertex($tableName)) {
                    $graph->createVertex($tableName);
                }
                $tableVertex = $graph->getVertex($tableName);

                foreach ($foreignIndices as $indexDefinition) {
                    /** @var IndexDefinition $indexDefinition */
                    $foreignTable = $indexDefinition->getForeignReferencedTable();

                    if (! $graph->hasVertex($foreignTable)) {
                        $graph->createVertex($foreignTable);
                    }
                    $vertexForForeignTable = $graph->getVertex($foreignTable);
                    $dependency = $vertexForForeignTable->getAttribute('columns', new Dependency($foreignTable));
                    foreach ($indexDefinition->getIndexColumns() as $indexColumn) {
                        $dependency->addDependent($indexDefinition->getForeignReferencedColumns(), $tableName, $indexColumn);
                    }
                    $vertexForForeignTable->setAttribute(
                        'columns',
                        $dependency
                    );
                    if (! $vertexForForeignTable->hasEdgeTo($tableVertex)) {
                        $vertexForForeignTable->createEdgeTo($tableVertex);
                    }
                }
            }
        }
        $this->graph = $graph;
    }

    public function graph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return array
     */
    public function getDependencyOrder()
    {
        $graph = $this->graph()->createGraphClone();

        //kahn's algorithm for topological sort
        //https://en.wikipedia.org/wiki/Topological_sorting

        $elements = []; //L
        $verticesWithNoIncomingEdge = collect($graph->getVertices()->getVector())
            ->filter(function (Vertex $vertex) {
                return $vertex->getEdgesIn()->isEmpty();
            })
            ->toArray(); //S
        while (count($verticesWithNoIncomingEdge) > 0) {
            $elements[] = $n = array_pop($verticesWithNoIncomingEdge); //N
            foreach ($graph->getVertices()->getIterator() as $m) {
                /** @var Vertex $m */
                if (! $n->hasEdgeTo($m)) {
                    continue;
                }
                $edges = $n->getEdgesTo($m);
                $e = $edges->getEdgeFirst();
                $e->destroy();
                if ($m->getEdgesIn()->isEmpty()) {
                    $verticesWithNoIncomingEdge[] = $m;
                }
            }
        }
        $circularRelations = [];
        if (! $graph->getEdges()->isEmpty()) {
            //do something to resolve the circular relationships
            $circularRelations = $this->getCircularDependencies($graph);
        }

        return [
            'nonCircular' => collect($elements)->mapWithKeys(function ($vertex) {
                return [$vertex->getId() => $vertex->getAttribute('columns')];
            })->toArray(),
            'circular' => $circularRelations
        ];
    }

    public function getCircularDependencies(Graph $graph): array
    {
        $circularDependencies = [];
        foreach ($graph->getVertices()->getIterator() as $vertex) {
            /** @var Vertex $vertex */
            foreach ($vertex->getEdgesOut()->getIterator() as $edge) {
                /** @var Directed $edge */
                $dependency = $edge->getVertexEnd();
                $circularDependencies[] = [
                    $vertex->getId()     => $vertex->getAttribute('columns'),
                    $dependency->getId() => $dependency->getAttribute('columns')
                ];
            }
        }

        return $circularDependencies;
    }
}
