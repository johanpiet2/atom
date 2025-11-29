<?php

declare(strict_types=1);

namespace AtomExtensions\Repositories;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

/**
 * Donor Repository.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class DonorRepository
{
    /**
     * Search donors with all fields.
     */
    public function search(array $filters = [], string $culture = 'en'): Collection
    {
        $query = DB::table('donor as d')
            ->join('actor as a', 'd.id', '=', 'a.id')
            ->join('object as o', 'a.id', '=', 'o.id')
            ->leftJoin('actor_i18n as i18n', function ($join) use ($culture) {
                $join->on('a.id', '=', 'i18n.id')
                     ->where('i18n.culture', $culture);
            })
            ->leftJoin('contact_information as ci', function ($join) {
                $join->on('a.id', '=', 'ci.actor_id')
                     ->where('ci.primary_contact', 1);
            })
            ->where('o.class_name', 'QubitDonor')
            ->whereNotNull('a.parent_id')
            ->select(
                'd.id',
                'i18n.authorized_form_of_name as authorizedFormOfName',
                'i18n.dates_of_existence as datesOfExistence',
                'i18n.history',
                'i18n.places',
                'i18n.legal_status as legalStatus',
                'i18n.functions',
                'i18n.mandates',
                'i18n.internal_structures as internalStructures',
                'i18n.general_context as generalContext',
                'a.description_identifier as descriptionIdentifier',
                'a.entity_type_id as entityTypeId',
                'ci.contact_person as contactPerson',
                'ci.street_address as streetAddress',
                'ci.city',
                'ci.region',
                'ci.country_code as countryCode',
                'ci.postal_code as postalCode',
                'ci.telephone',
                'ci.fax',
                'ci.email',
                'ci.website',
                'ci.note as contactNote',
                'i18n.culture',
                'o.created_at as createdAt',
                'o.updated_at as updatedAt'
            );

        return $query->get()->map(fn ($item) => (object) $item);
    }

    /**
     * Get available cultures from database.
     */
    public function getAvailableCultures(): Collection
    {
        // Get distinct cultures from actor_i18n table
        return DB::table('actor_i18n')
            ->select('culture')
            ->distinct()
            ->orderBy('culture')
            ->get()
            ->pluck('culture');
    }

    /**
     * Count total donors.
     */
    public function count(): int
    {
        return DB::table('donor as d')
            ->join('actor as a', 'd.id', '=', 'a.id')
            ->join('object as o', 'a.id', '=', 'o.id')
            ->where('o.class_name', 'QubitDonor')
            ->whereNotNull('a.parent_id')
            ->count();
    }
}
