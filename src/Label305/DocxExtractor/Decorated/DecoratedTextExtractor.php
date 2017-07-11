<?php
/**
 * Created by PhpStorm.
 * User: Thijs
 * Date: 13-11-14
 * Time: 09:45
 */

namespace Label305\DocxExtractor\Decorated;


use DOMElement;
use DOMNode;
use DOMText;
use Label305\DocxExtractor\DocxFileException;
use Label305\DocxExtractor\DocxHandler;
use Label305\DocxExtractor\DocxParsingException;
use Label305\DocxExtractor\Extractor;

class DecoratedTextExtractor extends DocxHandler implements Extractor {

    /**
     * @var int
     */
    protected $nextTagIdentifier;

    /**
     * @param $originalFilePath
     * @throws DocxParsingException
     * @throws DocxFileException
     * @return Array The mapping of all the strings
     */
    public function extractStrings($originalFilePath)
    {
        $prepared = $this->prepareDocumentForReading($originalFilePath);

        $this->nextTagIdentifier = 0;
        $result = $this->replaceAndMapValues($prepared['dom']->documentElement);
		
        return $result;
    }

    /**
     * Override this method to make a more complex replace and mapping
     *
     * @param DOMNode $node
     * @return array returns the mapping array
     */
    protected function replaceAndMapValues(DOMNode $node)
    {
        $result = [];

        if ($node instanceof DOMElement && $node->nodeName == "w:p") {
            $result = array_merge(
                $result,
                $this->replaceAndMapValuesForParagraph($node)
            );
        } else {
            if ($node->childNodes !== null) {
                foreach ($node->childNodes as $child) {
                    $result = array_merge(
                        $result,
                        $this->replaceAndMapValues($child)
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @param DOMNode $paragraph
     * @return array
     */
    protected function replaceAndMapValuesForParagraph(DOMNode $paragraph)
    {
        $result = [];

        if ($paragraph->childNodes !== null) {

            $firstTextChild = null;
            $otherNodes = [];
            $parts = new Paragraph();
			$anchorName = null;
			$anchorId = 0;

            foreach ($paragraph->childNodes as $paragraphChild) {
				
				if ($paragraphChild instanceof DOMElement && $paragraphChild->nodeName == "w:bookmarkStart") {
					$anchorName = $paragraphChild->getAttribute('w:name');
					$anchorId = $paragraphChild->getAttribute('w:id');
				}
				
                if ($paragraphChild instanceof DOMElement && $paragraphChild->nodeName == "w:r") {
                    $paragraphPart = $this->parseRNode($paragraphChild, $anchorName, $anchorId);
                    if ($paragraphPart !== null) {
                        $parts[] = $paragraphPart;
                        if ($firstTextChild === null) {
                            $firstTextChild = $paragraphChild;
                        } else {
                            $otherNodes[] = $paragraphChild;
                        }
                    }
                }
            }

            if ($firstTextChild !== null) {
                foreach ($otherNodes as $otherNode) {
                    $paragraph->removeChild($otherNode);
                }

                $result[$this->nextTagIdentifier] = $parts;
                $this->nextTagIdentifier++;
            }
        }

        return $result;
    }

    protected function parseRNode(DOMElement $rNode, $anchorName, $anchorId)
    {
        $bold = false;
        $italic = false;
        $underline = false;
        $brCount = 0;
        $text = null;
		$bmCount = 0;

        foreach ($rNode->childNodes as $rChild) {

            if ($rChild instanceof DOMElement && $rChild->nodeName == "w:rPr") {
                foreach ($rChild->childNodes as $propertyNode) {
                    if ($propertyNode instanceof DOMElement && $propertyNode->nodeName == "w:b") {
                        $bold = true;
                    }
                    if ($propertyNode instanceof DOMElement && $propertyNode->nodeName == "w:i") {
                        $italic = true;
                    }
                    if ($propertyNode instanceof DOMElement && $propertyNode->nodeName == "w:u") {
                        $underline = true;
                    }
                }
            }

            if ($rChild instanceof DOMElement && $rChild->nodeName == "w:t") {
                if ($rChild->getAttribute("xml:space") == 'preserve') {
                    $text = implode($this->parseText($rChild));
                } else {
                    $text = trim(implode($this->parseText($rChild)), " ");
                }
            }

            if ($rChild instanceof DOMElement && $rChild->nodeName == "w:br") {
                $brCount++;
            }
        }

        if ($text != null) {
			return new Sentence($text, $bold, $italic, $underline, $brCount, $anchorName, $anchorId);
        } else {
            return null;
        }
    }

    protected function parseText(DOMNode $node)
    {
        $result = [];

        if ($node instanceof DOMText) {
            $result[] = $node->nodeValue;
        }

        if ($node->childNodes !== null) {
            foreach ($node->childNodes as $child) {
                $result = array_merge(
                    $result,
                    $this->parseText($child)
                );
            }
        }

        return $result;
    }


}