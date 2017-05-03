<?php

class CAD_DomTree_DOMNodeType
{
    private $type;

    public function __construct($typeOrNode)
    {
        if ($typeOrNode instanceof DOMNode)
            $typeOrNode = $typeOrNode->nodeType;

        if (!is_int($typeOrNode))
            $typeOrNode = 0;

        $this->type = $typeOrNode;
    }

    public function __toString()
    {
        return $this->getString();
    }

    public function getType()
    {
        return $this->type;
    }

    public function getString()
    {
        return $this->nodeTypeText($this->type);
    }

    private function nodeTypeText($nodeType)
    {
        $constants = array_flip($this->getDOMConstants('^XML_.+_NODE$'));
        if (isset($constants[$nodeType]))
            $text = $constants[$nodeType];
        else
            $text = 'XML_UNKNOWN_NODE';

        return $text;
    }

    private function getDOMConstants($filter = NULL)
    {
        $constants = get_defined_constants(true);
        $constants = $constants['dom'];
        if ($filter) {
            $pattern = sprintf('/%s/', $filter);
            foreach ($constants as $key => $value) {
                if (!preg_match($pattern, $key)) {
                    unset($constants[$key]);
                }
            }
        }
        return $constants;
    }
}
