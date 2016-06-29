<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require 'Builder/NodeBuilderInterface.php';
require 'Builder/NodeBuilder.php';
require 'Node/NodeTrait.php';
require 'Node/NodeInterface.php';
require 'Node/Node.php';
require 'Visitor/Visitor.php';
require 'Visitor/YieldVisitor.php';

class Tree {

	public $nodebuilder;
	public $node;
	public $yieldvisitor;
	
	function __construct() {
		
		$this->nodebuilder = $this->nodebuilder();
		
		$this->node = $this->node();
		
		$this->yieldvisitor = $this->yieldvisitor();
	}
	
	public function nodebuilder() {
		
		return (new ReflectionClass('Tree\Builder\NodeBuilder'))->newInstanceArgs(func_get_args());
	}
	
	public function node() {
		
		return (new ReflectionClass('Tree\Node\Node'))->newInstanceArgs(func_get_args());
	}
	
	public function yieldvisitor() {
		
		return (new ReflectionClass('Tree\Visitor\YieldVisitor'))->newInstanceArgs(func_get_args());
	}
}