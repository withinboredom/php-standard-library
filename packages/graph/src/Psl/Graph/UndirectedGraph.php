<?php

declare(strict_types=1);

namespace Psl\Graph;

use function array_values;
use function Psl\Graph\Internal\get_node_key;

/**
 * Immutable undirected graph using adjacency list representation.
 *
 * In an undirected graph, edges are bidirectional.
 *
 * Supports any node type (scalars, objects, arrays, resources, etc.).
 *
 * @api
 */
final readonly class UndirectedGraph<TNode = mixed, TWeight = mixed> implements GraphInterface<TNode, TWeight>
{
    /**
     * @param array<non-empty-string, TNode> $nodes Map from node key to node
     * @param array<non-empty-string, list<Edge<TNode, TWeight>>> $edges Map from node key to edges
     *
     * @internal Use Graph\undirected() to create instances
     */
    public function __construct(
        private array $nodes = [],
        private array $edges = [],
    ) {}

    /**
     * Returns all nodes in the graph.
     *
     * @return list<TNode>
     *
     * @pure
     */
    public function getNodes(): array
    {
        return array_values($this->nodes);
    }

    /**
     * Returns all edges from a given node.
     *
     * @return list<Edge<TNode, TWeight>>
     *
     * @pure
     */
    public function getEdgesFrom(TNode $from): array
    {
        $key = get_node_key($from);
        return $this->edges[$key] ?? [];
    }

    /**
     * Checks if a node exists in the graph.
     *
     * @pure
     */
    public function hasNode(TNode $node): bool
    {
        $key = get_node_key($node);
        return isset($this->nodes[$key]);
    }

    /**
     * Checks if an edge exists between two nodes.
     *
     * @pure
     */
    public function hasEdge(TNode $node1, TNode $node2): bool
    {
        $key = get_node_key($node1);
        if (!isset($this->edges[$key])) {
            return false;
        }

        foreach ($this->edges[$key] as $edge) {
            if ($edge->to !== $node2) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * Returns a new graph with the node added.
     *
     * @return UndirectedGraph<TNode, TWeight>
     *
     * @internal
     *
     * @pure
     */
    public function withNode(TNode $node): UndirectedGraph<TNode, int>
    {
        if ($this->hasNode($node)) {
            return $this;
        }

        $key = get_node_key($node);
        $nodes = $this->nodes;
        $edges = $this->edges;
        $nodes[$key] = $node;
        $edges[$key] = [];

        return new UndirectedGraph::<TNode, TWeight>($nodes, $edges);
    }

    /**
     * Returns a new graph with the edge added.
     *
     * @return UndirectedGraph<TNode, TWeight>
     *
     * @internal
     *
     * @pure
     */
    public function withEdge(TNode $from, Edge $edge): UndirectedGraph<TNode, TWeight>
    {
        $key = get_node_key($from);
        $edges = $this->edges;
        $edges[$key] ??= [];
        $edges[$key][] = $edge;

        return new UndirectedGraph::<TNode, TWeight>($this->nodes, $edges);
    }

    /**
     * Checks if the graph contains a cycle.
     *
     * Uses DFS with parent tracking to detect cycles.
     * A cycle exists if we encounter a visited node that's not the parent.
     *
     * @pure
     */
    public function hasCycle(): bool
    {
        $visited = [];

        $dfsCheck =
            function (TNode $node, ?TNode $parent) use (&$visited, &$dfsCheck): bool {
                $key = get_node_key($node);
                $visited[$key] = true;

                foreach ($this->getEdgesFrom($node) as $edge) {
                    $neighborKey = get_node_key($edge->to);

                    if (!isset($visited[$neighborKey])) {
                        if ($dfsCheck($edge->to, $node)) {
                            return true;
                        }

                        continue;
                    }

                    if ($edge->to !== $parent) {
                        return true;
                    }
                }

                return false;
            };

        foreach ($this->getNodes() as $node) {
            $key = get_node_key($node);
            if (!isset($visited[$key])) {
                if ($dfsCheck($node, null)) {
                    return true;
                }
            }
        }

        return false;
    }
}
