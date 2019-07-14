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

class serviceProviderDeleteAction extends sfAction
{
	protected static
		$conn;
    
  public function execute($request)
  {
    $this->form = new sfForm;
    $source = $this->request->source;
    if (isset($source))
    {
		$this->resource = QubitServiceProvider::getById($this->request->source);
	}

    $this->resource = $this->getRoute()->resource;
    if ($request->isMethod('delete'))
    {

      foreach (QubitRelation::getBySubjectOrObjectId($this->resource->id) as $item)
      {
        $item->delete();
      }
		//SITA  hack
		// elasticsearch error
		if (!isset(self::$conn))
		{
		  self::$conn = Propel::getConnection();
		}
      //$this->resource->delete();
		$sql  = '';
		$sql  = 'DELETE FROM object WHERE id='.$this->resource->id;
		$stmt = self::$conn->prepare($sql);
		$stmt->execute();

		$sql  = '';
		$sql  = 'DELETE FROM service_provider WHERE id='.$this->resource->id;
		$stmt = self::$conn->prepare($sql);
		$stmt->execute();
		
      $this->redirect(array('module' => 'serviceProvider', 'action' => 'browse'));
    }
  } 
}
