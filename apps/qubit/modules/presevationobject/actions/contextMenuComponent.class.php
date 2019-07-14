

<?php

/**
 * Presevation Object edit component.
 *
 * @package    qubit
 * @subpackage Preservation Module
 * @author     Ramaano Ndou <ramaano.ndou@sita.co.za>
 * @version    SVN: $Id
 */
 
class PresevationObjectContextMenuComponent extends sfComponent
{
  public function execute($request)
  {
    $this->resource = $request->getAttribute('sf_route')->resource;

    $this->presevationObjects = array();
    foreach (QubitRelation::getRelatedSubjectsByObjectId('QubitPresevationObject', $this->resource->id, array('typeId' => QubitTaxonomy::PRESERVATION_TYPE_ID)) as $item)

    {
      $this->presevationObjects[$item->id] = $item;
    }

    if (1 > count($this->presevationObjects))
    {
      return sfView::NONE;
    }
  }
}
