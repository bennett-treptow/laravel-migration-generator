<?php

namespace LaravelMigrationGenerator\Helpers;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Directed;
use LaravelMigrationGenerator\Definitions\IndexDefinition;

class DependencyResolver
{
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
            $foreignIndices = collect($tableDefinition->getIndexDefinitions())->filter(function (IndexDefinition $def) {
                return $def->getIndexType() == 'foreign';
            });
            if ($foreignIndices->count() > 0) {
                if (! $graph->hasVertex($tableDefinition->getTableName())) {
                    $graph->createVertex($tableDefinition->getTableName());
                }
                $tableVertex = $graph->getVertex($tableDefinition->getTableName());
                foreach ($foreignIndices as $indexDefinition) {
                    /** @var IndexDefinition $indexDefinition */
                    if (! $graph->hasVertex($indexDefinition->getForeignReferencedTable())) {
                        $graph->createVertex($indexDefinition->getForeignReferencedTable());
                    }
                    $vertexForForeignTable = $graph->getVertex($indexDefinition->getForeignReferencedTable());
                    if (! $tableVertex->hasEdgeTo($vertexForForeignTable)) {
                        $tableVertex->createEdgeTo($vertexForForeignTable);
                    }
//                    if (! $vertexForForeignTable->hasEdgeTo($tableVertex)) {
//                        $vertexForForeignTable->createEdgeTo($tableVertex);
//                    }
                }
            }
        }
        $this->graph = $graph;
    }

    public function graph(): Graph
    {
        return $this->graph;
    }

    public function getDependencyOrder()
    {
        $graph = $this->graph()->createGraphClone();

//        //kahn's algorithm for topological sort
//        //https://en.wikipedia.org/wiki/Topological_sorting

        $elements = []; //L
        $verticesWithNoIncomingEdge = collect($graph->getVertices()->getVector())->filter(function (Vertex $vertex) {
            return $vertex->getEdgesIn()->isEmpty();
        })->toArray(); //S
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
            collect($elements)->map(function ($vertex) {
                return $vertex->getId();
            })->toArray(),
            $circularRelations
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
                $circularDependencies[] = [$vertex->getId(), $dependency->getId()];
            }
        }

        return $circularDependencies;
    }
}
