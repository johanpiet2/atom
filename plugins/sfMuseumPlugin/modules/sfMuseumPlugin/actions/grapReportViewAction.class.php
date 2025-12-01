<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

// Load museum data
use Illuminate\Database\Capsule\Manager as DB;

class sfMuseumPlugingrapReportViewAction extends sfAction
{
    public function execute($request)
    {
        $this->resource = $this->getRoute()->resource;

        // Check that this exists
        if (!isset($this->resource)) {
            $this->forward404();
        }

        // Check user has permission to view
        if (!QubitAcl::check($this->resource, 'read')) {
            QubitAcl::forwardUnauthorized();
        }


        try {
            // Load basic museum object data
            $museumData = DB::table('museum_object')
                ->where('object_id', $this->resource->id)
                ->first();

            if ($museumData) {
                $this->museumData = (array) $museumData;
            }

            // Load GRAP heritage asset data
            $grapData = DB::table('grap_heritage_asset')
                ->where('object_id', $this->resource->id)
                ->first();

            if ($grapData) {
                $this->grapData = (array) $grapData;
            } else {
                $this->getUser()->setFlash('notice', 'No GRAP financial data found for this object.');
            }

            // Load creator information if available
            $creators = [];
            foreach ($this->resource->getCreators() as $item) {
                $creators[] = $item->getAuthorizedFormOfName(['cultureFallback' => true]);
            }
            $this->creators = $creators;

            // Load repository
            $this->repository = $this->resource->getRepository(['inherit' => true]);

        } catch (Exception $e) {
            error_log('Error loading GRAP report data: ' . $e->getMessage());
            $this->getUser()->setFlash('error', 'Error loading GRAP report data.');
        }
    }
}