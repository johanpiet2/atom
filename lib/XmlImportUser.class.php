<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Import an XML document of User into Qubit.
 *
 * jjp SITA 07 Jan 2015 - Add import ID to update record if already exist
 *
 * @package    AccesstoMemory
 * @subpackage library
 * @author     MJ Suhonos <mj@suhonos.ca>
 * @author     Peter Van Garderen <peter@artefactual.com>
 * @author     Mike Cantelon <mike@artefactual.com>
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class XmlImportUser
{
  protected
    $errors = null,
    $rootObject = null,
    $parent = null;

  public function import($xmlFile, $options = array(), $type)
  {
    // load the XML document into a DOMXML object
    $importDOM = $this->loadXML($xmlFile, $options);

    // if we were unable to parse the XML file at all
    if (empty($importDOM->documentElement))
    {
   		throw new sfError404Exception('Unable to parse XML file: malformed or unresolvable entities');
    }

    // if libxml threw errors, populate them to show in the template
    if ($importDOM->libxmlerrors)
    {
      // warning condition, XML file has errors (perhaps not well-formed or invalid?)
      foreach ($importDOM->libxmlerrors as $libxmlerror)
      {
        $xmlerrors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array('%code%' => $libxmlerror->code, '%message%' => $libxmlerror->message, '%line%' => $libxmlerror->line));
      }

      $this->errors = array_merge((array) $this->errors, $xmlerrors);
    }

    // FIXME hardcoded until we decide how these will be developed
    $validSchemas = array(
      // document type declarations
      'user' => 'user'
    );

    // determine what kind of schema we're trying to import
    $schemaDescriptors = array($importDOM->documentElement->tagName);
    
    if (!empty($importDOM->namespaces))
    {
      krsort($importDOM->namespaces);
      $schemaDescriptors = array_merge($schemaDescriptors, $importDOM->namespaces);
    }
    if (!empty($importDOM->doctype))
    {
      $schemaDescriptors = array_merge($schemaDescriptors, array($importDOM->doctype->name, $importDOM->doctype->systemId, $importDOM->doctype->publicId));
    }

	$importSchema = "";
    foreach ($schemaDescriptors as $descriptor)
    {
      if (array_key_exists($descriptor, $validSchemas))
      {
        $importSchema = $validSchemas[$descriptor];
      }
    }

	if ($importSchema == 'user')
	{
		$importDOM->validate();

		// if libxml threw errors, populate them to show in the template
		foreach (libxml_get_errors() as $libxmlerror)
		{
			$this->errors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array('%code%' => $libxmlerror->code, '%message%' => $libxmlerror->message, '%line%' => $libxmlerror->line));
		}
	}
	else
	{
   		throw new sfError404Exception('Unable to parse XML file: Perhaps not a User import file');
    }

    $importMap = sfConfig::get('sf_app_module_dir').DIRECTORY_SEPARATOR.'object'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'import'.DIRECTORY_SEPARATOR.$importSchema.'.yml';
    if (!file_exists($importMap))
    {
      // error condition, unknown schema or no import filter
      $errorMsg = sfContext::getInstance()->i18n->__('Unknown schema or import format: "%format%"', array('%format%' => $importSchema));

      throw new Exception($errorMsg);
    }

    $this->schemaMap = sfYaml::load($importMap);

    // if XSLs are specified in the mapping, process them
    if (!empty($this->schemaMap['processXSLT']))
    {
      // pre-filter through XSLs in order
      foreach ((array) $this->schemaMap['processXSLT'] as $importXSL)
      {
        $importXSL = sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'xslt'.DIRECTORY_SEPARATOR.$importXSL;

        if (file_exists($importXSL))
        {
          // instantiate an XSLT parser
          $xslDOM = new DOMDocument;
          $xslDOM->load($importXSL);

          // Configure the transformer
          $xsltProc = new XSLTProcessor;
          $xsltProc->registerPHPFunctions();
          $xsltProc->importStyleSheet($xslDOM);

          $importDOM->loadXML($xsltProc->transformToXML($importDOM));
          unset($xslDOM);
          unset($xsltProc);
        }
        else
        {
          $this->errors[] = sfContext::getInstance()->i18n->__('Unable to load import XSL filter: "%importXSL%"', array('%importXSL%' => $importXSL));
        }
      }

      // re-initialize xpath on the new XML
      $importDOM->xpath = new DOMXPath($importDOM);
    }

        unset($this->schemaMap['processXSLT']);

    // go through schema map and populate objects/properties
    foreach ($this->schemaMap as $name => $mapping)
    {
      // if object is not defined or a valid class, we can't process this mapping
      if (empty($mapping['Object']) || !class_exists('Qubit'.$mapping['Object']))
      {
        $this->errors[] = sfContext::getInstance()->i18n->__('Non-existent class defined in import mapping: "%class%"', array('%class%' => 'Qubit'.$mapping['Object']));
        continue;
      }

      // get a list of XML nodes to process
      $nodeList = $importDOM->xpath->query($mapping['XPath']);

      foreach ($nodeList as $domNode)
      {
        // create a new object
        $class = 'Qubit'.$mapping['Object'];
        $currentObject = new $class;

        // set the rootObject to use for initial display in successful import
        if (!$this->rootObject)
        {
          $this->rootObject = $currentObject;
        }

        // use DOM to populate object
        $this->populateObject($domNode, $importDOM, $mapping, $currentObject, $importSchema);
      }
    }

    return $this;
  }

  private function populateObject(&$domNode, &$importDOM, &$mapping, &$currentObject, $importSchema)
  {
    // if a parent path is specified, try to parent the node
    if (empty($mapping['Parent']))
    {
      $parentNodes = new DOMNodeList;
    }
    else
    {
      $parentNodes = $importDOM->xpath->query('('.$mapping['Parent'].')', $domNode);
    }

    if ($parentNodes->length > 0)
    {
      // parent ID comes from last node in the list because XPath forces forward document order
      $parentId = $parentNodes->item($parentNodes->length - 1)->getAttribute('xml:id');
      unset($parentNodes);

      if (!empty($parentId) && is_callable(array($currentObject, 'setParentId')))
      {
        $currentObject->parentId = $parentId;
      }
    }
    else
    {
      // orphaned object, set root if possible
      if (isset($this->parent))
      {
        $currentObject->parentId = $this->parent->id;
      }
      else if (is_callable(array($currentObject, 'setRoot')))
      {
        $currentObject->setRoot();
      }
    }

    // go through methods and populate properties
    $this->processMethods($domNode, $importDOM, $mapping['Methods'], $currentObject, $importSchema);

	// jjp SITA 07 Jan 2015 - Search is already imported. If exist then update else insert
	$updateObject = QubitUser::getByUsername($currentObject->username);
	if (isset($updateObject))
	{
		$updateObject->active = $currentObject->active;

		// save the object after it's fully-populated
		$updateObject->save();

		// write the ID onto the current XML node for tracking
		$domNode->setAttribute('xml:id', $updateObject->id);
	}
	else
	{
		// save the object after it's fully-populated
		$currentObject->save();

		// write the ID onto the current XML node for tracking
		$domNode->setAttribute('xml:id', $currentObject->id);
	}
  }

  /*
   * Cycle through methods and populate object based on relevant data
   *
   * @return  null
   */
  private function processMethods(&$domNode, &$importDOM, $methods, &$currentObject, $importSchema)
  {
    // go through methods and populate properties
    foreach ($methods as $name => $methodMap)
    {
      // if method is not defined, we can't process this mapping
      if (empty($methodMap['Method']) || !is_callable(array($currentObject, $methodMap['Method'])))
      {
        $this->errors[] = sfContext::getInstance()->i18n->__('Non-existent method defined in import mapping: "%method%"', array('%method%' => $methodMap['Method']));
        continue;
      }

      $nodeList2 = $importDOM->xpath->query($methodMap['XPath'], $domNode);

      if (is_object($nodeList2))
      {
        switch($name)
        {
          case 'userName':
            foreach ($nodeList2 as $item)
            {
              if (($childNode = $importDOM->xpath->query('.', $item)) !== null)
              {
                $currentObject->username = $childNode->item(0)->nodeValue;
              }
			}

            break;

          case 'userEmail':
            foreach ($nodeList2 as $item)
            {
              if (($childNode = $importDOM->xpath->query('.', $item)) !== null)
              {
                $currentObject->email = $childNode->item(0)->nodeValue;
              }
			}

            break;

          case 'userActive':
            foreach ($nodeList2 as $item)
            {
              if (($childNode = $importDOM->xpath->query('.', $item)) !== null)
              {
                $currentObject->active = $childNode->item(0)->nodeValue;
              }
			}

            break;

          case 'userId':
            foreach ($nodeList2 as $item)
            {
              if (($childNode = $importDOM->xpath->query('.', $item)) !== null)
              {
	            $this->importID = $childNode->item(0)->nodeValue;
              }
			}

            break;

          // hack: some multi-value elements (e.g. 'languages') need to get passed as one array instead of individual nodes values
          case 'languages':
          case 'language':
            $langCodeConvertor = new fbISO639_Map;
            $isID3 = ($importSchhema == 'dc') ? true : false;

            $value = array();
            foreach ($nodeList2 as $item)
            {
              if ($twoCharCode = $langCodeConvertor->getID1($item->nodeValue, $isID3))
              {
                $value[] = strtolower($twoCharCode);
              }
              else
              {
                $value[] = $item->nodeValue;
              }
            }
            $currentObject->language = $value;

            break;

          default:
            foreach ($nodeList2 as $key => $domNode2)
            {
              // normalize the node text; NB: this will strip any child elements, eg. HTML tags
              $nodeValue = self::normalizeNodeValue($domNode2);

              // if you want the full XML from the node, use this
              $nodeXML = $domNode2->ownerDocument->saveXML($domNode2);
              // set the parameters for the method call
              if (empty($methodMap['Parameters']))
              {
                $parameters = array($nodeValue);
              }
              else
              {
                $parameters = array();
                foreach ((array) $methodMap['Parameters'] as $parameter)
                {
                  // if the parameter begins with %, evaluate it as an XPath expression relative to the current node
                  if ('%' == substr($parameter, 0, 1))
                  {
                    // evaluate the XPath expression
                    $xPath = substr($parameter, 1);
                    $result = $importDOM->xpath->query($xPath, $domNode2);

                    if ($result->length > 1)
                    {
                      // convert nodelist into an array
                      foreach ($result as $element)
                      {
                        $resultArray[] = $element->nodeValue;
                      }
                      $parameters[] = $resultArray;
                    }
                    else
                    {
                      // pass the node value unaltered; this provides an alternative to $nodeValue above
                      $parameters[] = $result->item(0)->nodeValue;
                    }
                  }
                  else
                  {
                    // Confirm DOMXML node exists to avoid warnings at run-time
                    if (false !== preg_match_all('/\$importDOM->xpath->query\(\'@\w+\', \$domNode2\)->item\(0\)->nodeValue/', $parameter, $matches))
                    {
                      foreach ($matches[0] as $match)
                      {
                        $str = str_replace('->nodeValue', '', $match);

                        if (null !== ($node = eval('return '.$str.';')))
                        {
                          // Substitute node value for search string
                          $parameter = str_replace($match, '\''.$node->nodeValue.'\'', $parameter);
                        }
                        else
                        {
                          // Replace empty nodes with null in parameter string
                          $parameter = str_replace($match, 'null', $parameter);
                        }
                      }
                    }

                    eval('$parameters[] = '.$parameter.';');
                  }
                }
              }

              // invoke the object and method defined in the schema map
              call_user_func_array(array( & $currentObject, $methodMap['Method']), $parameters);
            }
        }

        unset($nodeList2);
      }
    }
  }

  /**
   * modified helper methods from (http://www.php.net/manual/en/ref.dom.php):
   *
   * - create a DOMDocument from a file
   * - parse the namespaces in it
   * - create a XPath object with all the namespaces registered
   *  - load the schema locations
   *  - validate the file on the main schema (the one without prefix)
   *
   * @param string $xmlFile XML document file
   * @param array $options optional parameters
   * @return DOMDocument an object representation of the XML document
   */
  protected function loadXML($xmlFile, $options = array())
  {
    libxml_use_internal_errors(true);

    // FIXME: trap possible load validation errors (just suppress for now)
    $err_level = error_reporting(0);
    $doc = new DOMDocument('1.0', 'UTF-8');

    // Default $strictXmlParsing to false
    $strictXmlParsing = (isset($options['strictXmlParsing'])) ? $options['strictXmlParsing'] : false;

    // Pre-fetch the raw XML string from file so we can remove any default
    // namespaces and reuse the string for later when finding/registering namespaces.
    $rawXML = file_get_contents($xmlFile);

    if ($strictXmlParsing)
    {
      // enforce all XML parsing rules and validation
      $doc->validateOnParse = true;
      $doc->resolveExternals = true;
    }
    else
    {
      // try to load whatever we've got, even if it's malformed or invalid
      $doc->recover = true;
      $doc->strictErrorChecking = false;
    }
    $doc->formatOutput = false;
    $doc->preserveWhitespace = false;
    $doc->substituteEntities = true;

    $doc->loadXML($this->removeDefaultNamespace($rawXML));

    $xsi = false;
    $doc->namespaces = array();
    $doc->xpath = new DOMXPath($doc);

    // pass along any XML errors that have been generated
    $doc->libxmlerrors = libxml_get_errors();

    // if the document didn't parse correctly, stop right here
    if (empty($doc->documentElement))
    {
      return $doc;
    }

    error_reporting($err_level);

    // look through the entire document for namespaces
    // FIXME: #2787
    // https://projects.artefactual.com/issues/2787
    //
    // THIS SHOULD ONLY INSPECT THE ROOT NODE NAMESPACES
    // Consider: http://www.php.net/manual/en/book.dom.php#73793

    $re = '/xmlns:([^=]+)="([^"]+)"/';
    preg_match_all($re, $rawXML, $mat, PREG_SET_ORDER);

    foreach ($mat as $xmlns)
    {
      $pre = $xmlns[1];
      $uri = $xmlns[2];

      $doc->namespaces[$pre] = $uri;

      if ($pre == '')
      {
        $pre = 'noname';
      }
      $doc->xpath->registerNamespace($pre, $uri);
    }

    return $doc;
  }

  /**
   *
   *
   * @return DOMNodeList
   */
  public static function queryDomNode($node, $xpathQuery)
  {
    $doc = new DOMDocument();
    $doc->loadXML('<xml></xml>');
    $doc->documentElement->appendChild($doc->importNode($node, true));
    $xpath = new DOMXPath($doc);
    return $xpath->query($xpathQuery);
  }

  /**
   * Return true if import had errors
   *
   * @return boolean
   */
  public function hasErrors()
  {
    return $this->errors != null;
  }

  /**
   * Return array of error messages
   *
   * @return unknown
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Get the root object for the import
   *
   * @return mixed the root object (object type depends on import type)
   */
  public function getRootObject()
  {
    return $this->rootObject;
  }

  /**
   * Get the root object for the import
   *
   * @return mixed the root object (object type depends on import type)
   */
  public function setParent($parent)
  {
    return $this->parent = $parent;
  }

  /**
   * Replace </lb> tags for '\n'
   *
   * @return node value without linebreaks tags
   */
  public static function replaceLineBreaks($node)
  {
    $nodeValue = '';

    foreach ($node->childNodes as $child)
    {
      if ($child->nodeName == 'lb')
      {
        $nodeValue .= "\n";
      }
      else
      {
        $nodeValue .= preg_replace('/[\n\r\s]+/', ' ', $child->nodeValue);
      }
    }

    return $nodeValue;
  }

  /**
   * Make sure to remove any default namespaces from
   * EAD tags. See issue #7280 for details.
   */
  private function removeDefaultNamespace($xml)
  {
    return preg_replace('/(<ead.*?)xmlns="[^"]*"\s+(.*?>)/', '${1}${2}', $xml, 1);
  }

  /**
   * Normalize node, replaces <p> and <lb/>
   *
   * @return node value normalized
   */
  public static function normalizeNodeValue($node)
  {
    $nodeValue = '';

    if (!($node instanceof DOMAttr))
    {
      $nodeList = $node->getElementsByTagName('p');

      if (0 < $nodeList->length)
      {
        $i = 0;
        foreach ($nodeList as $pNode)
        {
          if ($i++ == 0)
          {
            $nodeValue .= self::replaceLineBreaks($pNode);
          }
          else
          {
            $nodeValue .= "\n\n" . self::replaceLineBreaks($pNode);
          }
        }
      }
      else
      {
        $nodeValue .= self::replaceLineBreaks($node);
      }
    }
    else
    {
      $nodeValue .= $node->nodeValue;
    }

    return $nodeValue;
  }
}
