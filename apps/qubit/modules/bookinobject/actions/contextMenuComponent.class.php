<?php

/**
 * Bookin Object edit component.
 *
 * @package    qubit
 * @subpackage Preservation Module
 * @author     Ramaano Ndou <ramaano.ndou@sita.co.za>
 * @version    SVN: $Id
 */
 
class BookinObjectContextMenuComponent extends sfComponent
{
  public function execute($request)
  {
    $this->resource = $request->getAttribute('sf_route')->resource;

    $this->bookinObjects = array();
    foreach (QubitRelation::getRelatedSubjectsByObjectId('QubitBookinObject', $this->resource->id, array('requestorId' => QubitTaxonomy::BOOKIN_TYPE_ID)) as $item)

    {
      $this->bookinObjects[$item->id] = $item;
    }

    if (1 > count($this->bookinObjects))
    {
      return sfView::NONE;
    }
  }
}
