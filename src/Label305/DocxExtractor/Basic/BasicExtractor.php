<?php namespace Label305\DocxExtractor\Basic;


use DOMDocument;
use DOMNode;
use DOMText;
use Label305\DocxExtractor\DocxFileException;
use Label305\DocxExtractor\DocxHandler;
use Label305\DocxExtractor\DocxParsingException;
use Label305\DocxExtractor\Extractor;


class BasicExtractor extends DocxHandler implements Extractor {

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

        if ($node instanceof DOMText) {
            $result[$this->nextTagIdentifier] = $node->nodeValue;
            $this->nextTagIdentifier++;
        }

        if ($node->childNodes !== null) {
            foreach ($node->childNodes as $child) {
                $result = array_merge(
                    $result,
                    $this->replaceAndMapValues($child)
                );
            }
        }

        return $result;
    }

}