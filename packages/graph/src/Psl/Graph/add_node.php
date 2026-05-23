<?php

declare(strict_types=1);

namespace Psl\Graph;

/**
 * Adds a node to the graph.
 *
 * Returns a new graph with the node added. If the node already exists, returns the original graph.
 *
 * Example:
 *
 *      $graph = Graph\directed();
 *      $graph = Graph\add_node($graph, 'A');
 *      $graph = Graph\add_node($graph, 'B');
 *
 * @param DirectedGraph<TNode, TWeight>|UndirectedGraph<TNode, TWeight> $graph
 *
 * @return ($graph is DirectedGraph<TNode, TWeight> ? DirectedGraph<TNode, TWeight> : UndirectedGraph<TNode, TWeight>)
 *
 * @pure
 *
 * @api
 */
function add_node<TNode = mixed, TWeight = mixed>(DirectedGraph<TNode, TWeight>|UndirectedGraph<TNode, TWeight> $graph, TNode $node): DirectedGraph|UndirectedGraph
{
    return $graph->withNode($node);
}
