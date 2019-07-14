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

class RegistryDeleteAction extends sfAction
{
	protected static
		$conn;

  public function execute($request)
  {
    $this->form = new sfForm;

    if (isset($this->request->source))
    {
		$this->resource = QubitRegistry::getById($this->request->source);
	}

    $this->resource = $this->getRoute()->resource;
    if ($request->isMethod('delete'))
    {
      foreach (QubitRelation::getBySubjectOrObjectId($this->resource->id) as $item)
      {
 //       $item->delete();
      }

      	//$this->resource->delete();
      	// SITA Elasticsearch error hack

		if (!isset(self::$conn))
		{
		  self::$conn = Propel::getConnection();
		}

		$sql  = '';
		$sql  = 'DELETE FROM object WHERE id='.$this->resource->id;
		$stmt = self::$conn->prepare($sql);
		$stmt->execute();
echo $sql;

		$sql  = '';
		$sql  = 'DELETE FROM registry WHERE id='.$this->resource->id;
		$stmt = self::$conn->prepare($sql);
		$stmt->execute();
echo $sql;
      $this->redirect(array('module' => 'registry', 'action' => 'browse'));
    }
  } 
}
