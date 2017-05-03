<?php

/**
 * Iterator for DOMNode(s)
 */
class CAD_DomTree_DOMIterator extends IteratorIterator
{
    /**
     * @param array|DOMNode|DOMNodeList $nodeOrNodes
     * @throws InvalidArgumentException
     */
    public function __construct($nodeOrNodes)
    {
        if ($nodeOrNodes instanceof DOMNode) {
            $nodeOrNodes = array($nodeOrNodes);
        } elseif ($nodeOrNodes instanceof DOMNodeList) {
            $nodeOrNodes = new IteratorIterator($nodeOrNodes);
        }
        if (is_array($nodeOrNodes)) {
            $nodeOrNodes = new ArrayIterator($nodeOrNodes);
        }

        if (!$nodeOrNodes instanceof Iterator) {
            throw new InvalidArgumentException('Not an array, DOMNode or DOMNodeList given.');
        }

        parent::__construct($nodeOrNodes);
    }
}