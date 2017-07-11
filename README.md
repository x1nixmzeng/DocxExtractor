Docx Extractor
=============

**Please note** This is a fork of the original library which removes the ability to replace or inject changes!

PHP library for extracting string data in .docx files. Docx files are zip archives filled with XML documents and assets. Their format is described by [OOXML](http://nl.wikipedia.org/wiki/Office_Open_XML). This library only manipulates the `word/document.xml` file.

Composer installation
---

```json
"require": {
    "label305/docx-extractor": "0.1.*"
}
```

Basic usage
----

Import the basic classes.

```php
use Label305\DocxExtractor\Basic\BasicExtractor;
```

First we need to extract all the paragraphs from an existing `docx` file. This can be done using the `BasicExtractor` or the `DecoratedTextExtractor`. Calling `extractStrings` will return an array of extracted paragraphs.

```php
$extractor = new BasicExtractor();
$mapping = $extractor->extractStrings(
    'simple.docx'
  );
```

Now that you have extracted paragraphs you can inspect the content of the resulting `$mapping` array.

```php
echo $mapping[0]; // The quick brown fox jumps over the lazy dog
```

Advanced usage
----

The library is also equiped with a `DecoratedTextExtractor` with which you can manipulate basic paragraph styling like bold, italic and underline. You can also use the `Paragraph` objects to distinguish logical groupings of text.

```php
$extractor = new DecoratedTextExtractor();
$mapping = $extractor->extractStrings(
    'simple.docx'
  );
  
$firstParagraph = $mapping[0]; // Paragraph object
$firstSentence = $firstParagraph[0]; // Sentence object

$firstSentence->italic = true;
$firstSentence->bold = false;
$firstSentence->underline = true;
$firstSentence->br = 2; // Two line breaks before this sentence

echo $firstSentence->text; // The quick brown fox jumps over the lazy dog
$firstSentence->text = "Several fabulous dixieland jazz groups played with quick tempo.";


License
---------
Copyright 2014 Label305 B.V.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
