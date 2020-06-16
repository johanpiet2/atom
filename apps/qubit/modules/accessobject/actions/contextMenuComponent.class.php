<?php

/**
 * Access Object edit component.
 *
 * @package    qubit
 * @subpackage Access Module
 * @author     JJP
 * @version    SVN: $Id
 */
 
class AccessObjectContextMenuComponent extends sfComponent
{
  public function execute($request)
  {
    $this->resource = $request->getAttribute('sf_route')->resource;

    $this->accessObjects = array();
    
    foreach (QubitRelation::getRelatedSubjectsByObjectId('QubitAccessObject', $this->resource->id, array('typeId' => QubitTaxonomy::ACCESS_TYPE_ID)) as $item)

    {
      $this->accessObjects[$item->id] = $item;
    }

    if (1 > count($this->accessObjects))
    {
      return sfView::NONE;
    }
  }
}
