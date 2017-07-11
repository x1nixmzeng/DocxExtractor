<?php namespace Label305\DocxExtractor\Decorated;
use DOMDocument;
use DOMDocumentFragment;

/**
 * Class Sentence
 * @package Label305\DocxExtractor\Decorated
 *
 * Represents a <w:r> object in the docx format.
 */
class Sentence {

    public $text;

    /**
     * @var bool If sentence is bold or not
     */
    public $bold;

    /**
     * @var bool If sentence is italic or not
     */
    public $italic;

    /**
     * @var bool If sentence is underlined or not
     */
    public $underline;

    /**
     * @var int Number of line breaks
     */
    public $br;
	
    /**
     * @var string of bookmark anchor name
     */
    public $anchorName;
	
	/**
     * @var integer for the bookmark identifier
     */
    public $anchorId;

    function __construct($text, $bold, $italic, $underline, $br, $anchorName, $anchorId)
    {
        $this->text = $text;
        $this->bold = $bold;
        $this->italic = $italic;
        $this->underline = $underline;
        $this->br = $br;
		$this->anchorName = $anchorName;
		$this->anchorId = $anchorId;
    }

    /**
     * To docx xml string
     *
     * @return sting
     */
    public function toDocxXML()
    {
        $value = '<w:r xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:rPr>';

        if ($this->bold) {
            $value .= "<w:b/>";
        }
        if ($this->italic) {
            $value .= "<w:i/>";
        }
        if ($this->underline) {
            $value .= '<w:u w:val="single"/>';
        }

        $value .= "</w:rPr>";
		
		if( $this->anchorName != null ) {
			$value .= '<w:bookmarkStart w:colFirst="0" w:colLast="0" w:name="' . $this->anchorName . '" w:id="' . $this->anchorId . '"/>';
			$value .= '<w:bookmarkEnd w:id="' . $this->anchorId .'"/>';
		}

        for ($i = 0; $i < $this->br; $i++) {
            $value .= "<w:br/>";
        }

        $value .= '<w:t xml:space="preserve">' . htmlentities($this->text, ENT_XML1) . "</w:t></w:r>";

        return $value;
    }

    /**
     * Convert to HTML
     *
     * To prevent duplicate tags (e.g. <strong) and allow wrapping you can use the parameters. If they are set to false
     * a tag will not be opened or closed.
     *
     * @param bool $firstWrappedInBold
     * @param bool $firstWrappedInItalic
     * @param bool $firstWrappedInUnderline
     * @param bool $lastWrappedInBold
     * @param bool $lastWrappedInItalic
     * @param bool $lastWrappedInUnderline
     * @return string HTML string
     */
    public function toHTML($firstWrappedInBold = true, $firstWrappedInItalic = true, $firstWrappedInUnderline = true,
                           $lastWrappedInBold = true, $lastWrappedInItalic = true, $lastWrappedInUnderline = true)
    {
        $value = '';

        for ($i = 0; $i < $this->br; $i++) {
            $value .= "<br />";
        }

		if($this->anchorName !=null ) {
			$value .= '<a name="' . $this->anchorName . '">';
		}
		
        if ($this->bold && $firstWrappedInBold) {
            $value .= "<strong>";
        }
        if ($this->italic && $firstWrappedInItalic) {
            $value .= "<em>";
        }
        if ($this->underline && $firstWrappedInUnderline) {
            $value .= "<u>";
        }

        $value .= htmlentities($this->text);

        if ($this->underline && $lastWrappedInUnderline) {
            $value .= "</u>";
        }
        if ($this->italic && $lastWrappedInItalic) {
            $value .= "</em>";
        }
        if ($this->bold && $lastWrappedInBold) {
            $value .= "</strong>";
        }
		
		if($this->anchorName !=null ) {
			$value .= "</a>";
		}

        return $value;
    }

}
