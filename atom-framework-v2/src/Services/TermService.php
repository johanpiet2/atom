<?php

declare(strict_types=1);

namespace AtomExtensions\Services;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

/**
 * Term Service.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class TermService
{
    // Correct taxonomy IDs from AtoM database
    public const ACTOR_ENTITY_TYPE_TAXONOMY_ID = 32;
    public const LEVEL_OF_DESCRIPTION_TAXONOMY_ID = 34;
    public const DESCRIPTION_STATUS_TAXONOMY_ID = 48;
    public const PUBLICATION_STATUS_TAXONOMY_ID = 60;  // FIXED: was 68

    private string $defaultCulture;

    public function __construct(string $defaultCulture = 'en')
    {
        $this->defaultCulture = $defaultCulture;
    }

    public function getById(int $id, ?string $culture = null): ?object
    {
        $culture = $culture ?? $this->defaultCulture;

        $term = DB::table('term')
            ->leftJoin('term_i18n', function ($join) use ($culture) {
                $join->on('term.id', '=', 'term_i18n.id')
                     ->where('term_i18n.culture', $culture);
            })
            ->where('term.id', $id)
            ->select(
                'term.id',
                'term.taxonomy_id',
                'term.code',
                'term.parent_id',
                'term_i18n.name'
            )
            ->first();

        return $term;
    }

    public function getByTaxonomyId(int $taxonomyId, ?string $culture = null): Collection
    {
        $culture = $culture ?? $this->defaultCulture;

        $terms = DB::table('term')
            ->leftJoin('term_i18n', function ($join) use ($culture) {
                $join->on('term.id', '=', 'term_i18n.id')
                     ->where('term_i18n.culture', $culture);
            })
            ->where('term.taxonomy_id', $taxonomyId)
            ->whereNotNull('term.parent_id')
            ->orderBy('term_i18n.name')
            ->select(
                'term.id',
                'term.taxonomy_id',
                'term.code',
                'term.parent_id',
                'term_i18n.name'
            )
            ->get();

        return collect($terms);
    }

    public function getActorEntityTypes(?string $culture = null): Collection
    {
        return $this->getByTaxonomyId(self::ACTOR_ENTITY_TYPE_TAXONOMY_ID, $culture);
    }

    public function getLevelsOfDescription(?string $culture = null): Collection
    {
        return $this->getByTaxonomyId(self::LEVEL_OF_DESCRIPTION_TAXONOMY_ID, $culture);
    }

    public function getPublicationStatuses(?string $culture = null): Collection
    {
        return $this->getByTaxonomyId(self::PUBLICATION_STATUS_TAXONOMY_ID, $culture);
    }

    public function toChoices(Collection $terms, bool $includeNull = true, string $nullLabel = 'All'): array
    {
        $choices = $includeNull ? ['' => $nullLabel] : [];

        foreach ($terms as $term) {
            $choices[$term->id] = $term->name ?? "Term {$term->id}";
        }

        return $choices;
    }

    public function getTermName(int $termId, ?string $culture = null): string
    {
        $term = $this->getById($termId, $culture);

        return $term && $term->name ? $term->name : '-';
    }
}
