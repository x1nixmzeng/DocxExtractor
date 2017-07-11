<?php namespace Label305\DocxExtractor;


interface Extractor {

    /**
     * @param $originalFilePath
     * @throws DocxParsingException
     * @throws DocxFileException
     * @return Array The mapping of all the strings
     */
    public function extractStrings($originalFilePath);

}