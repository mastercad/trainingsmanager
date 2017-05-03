<?php

/**
 * Recursive Iterator for DOMNode(s)
 */
class CAD_DomTree_DOMRecursiveIterator extends CAD_DomTree_DOMIterator implements RecursiveIterator
{
    public function hasChildren()
    {
        /* @var $current DOMNode */
        $current = $this->current();
        return $current->hasChildNodes();
    }

    public function getChildren()
    {
        /* @var $current DOMNode */
        $current = $this->current();
        return new self($current->childNodes);
    }
}
