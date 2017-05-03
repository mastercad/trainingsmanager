<?php

class CAD_DomTree_DOMRecursiveDecoratorStringAsCurrent extends CAD_DomTree_RecursiveIteratorDecoratorStub
{
    public function current()
    {
        /* @var $node DOMNode */
        $node = parent::current();
        $nodeType = new CAD_DomTree_DOMNodeType($node);

        switch ($nodeType->getType()) {
            case XML_ELEMENT_NODE:
                return $this->tag($node);

            case XML_TEXT_NODE:
                return $this->string($node->nodeValue);

            case XML_DOCUMENT_NODE;
            return $this->docnode($node);

            case XML_COMMENT_NODE:
                return $this->comment($node);

            default:
                return sprintf('[%s (%d)]', $nodeType, $nodeType->getType());
        }
    }

    private function comment($node) {
        return '<!-- ' . $this->string($node->nodeValue);
    }

    private function string($string)
    {
        $string = strtr($string, array("\xEF\xBB\xBF" => '/!\ BOM:UTF-8 /!\\', "\0" => '\0', "\n" => '\n', "\t" => '\t', "\r" => '\r'));
        if (strlen($string) > 80) {
            $string = substr($string, 0, 77) . '...';
        }
        return sprintf('"%s"', $string);
    }

    /**
     * @param DOMDocument $node
     * @return string
     */
    private function docnode(DOMDocument $node)
    {
        var_dump($node);
        $tag = "<{$node->documentURI}>";
        return $tag;
    }

    /**
     * @param DOMElement $node
     * @param array $attributes to optionally expand to
     * @return string
     */
    private function tag(DOMElement $node, $attributes = array('id', 'class', 'href'))
    {
        $tag = "<$node->tagName";
        if ($node->hasAttributes()) {
            foreach ($attributes as $attribute) {
                if ($att = $node->getAttribute($attribute)) $tag .= " id=" . $this->string($att);
            }
        }
        return "$tag>";

    }
}