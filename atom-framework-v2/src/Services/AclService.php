<?php

declare(strict_types=1);

namespace AtomExtensions\Services;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * ACL (Access Control List) Service.
 *
 * Provides access control functionality throughout the solution.
 * Replaces QubitAcl with Laravel Query Builder.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class AclService
{
    /**
     * Well-known ACL actions from AtoM.
     */
    public const CREATE = 101;
    public const READ = 102;
    public const UPDATE = 103;
    public const DELETE = 104;
    public const TRANSLATE = 105;
    public const PUBLISH = 106;
    public const DISCOVER = 107;
    public const VIEW_DRAFT = 108;

    private ?object $user = null;

    public function __construct(?object $user = null)
    {
        $this->user = $user;
    }

    /**
     * Set the current user.
     */
    public function setUser(object $user): void
    {
        $this->user = $user;
    }

    /**
     * Check if user is administrator.
     *
     * Replaces: QubitAcl::check($resource, 'administrator')
     */
    public function isAdministrator(?object $user = null): bool
    {
        $user = $user ?? $this->user;

        if (!$user) {
            return false;
        }

        // Check if user has administrator group
        $isAdmin = DB::table('aclUserGroup')
            ->where('userId', $user->id)
            ->where('groupId', 100) // Administrator group ID
            ->exists();

        return $isAdmin;
    }

    /**
     * Check if user has permission for an action on a resource.
     *
     * Replaces: QubitAcl::check($resource, $action)
     */
    public function check(?object $resource, string $action, ?object $user = null): bool
    {
        $user = $user ?? $this->user;

        if (!$user) {
            return false;
        }

        // Administrators have all permissions
        if ($this->isAdministrator($user)) {
            return true;
        }

        // Map action strings to action IDs
        $actionId = $this->getActionId($action);
        if (!$actionId) {
            return false;
        }

        // Check ACL permissions
        return $this->checkPermission($user->id, $resource?->id, $actionId);
    }

    /**
     * Forward to unauthorized page.
     *
     * Replaces: QubitAcl::forwardUnauthorized()
     */
    public static function forwardUnauthorized(): void
    {
        if (class_exists('sfContext')) {
            $context = \sfContext::getInstance();
            $context->getController()->forward('user', 'login');
        }

        throw new \RuntimeException('Unauthorized access');
    }

    /**
     * Forward to access denied page (for authenticated users).
     *
     * Replaces: QubitAcl::forwardToSecureAction()
     */
    public static function forwardToSecureAction(): void
    {
        if (class_exists('sfContext')) {
            $context = \sfContext::getInstance();
            $context->getController()->forward('error', 'accessDenied');
        }

        throw new \RuntimeException('Access denied');
    }

    /**
     * Check if user is authenticated.
     */
    public function isAuthenticated(?object $user = null): bool
    {
        $user = $user ?? $this->user;

        return $user !== null && isset($user->id);
    }

    /**
     * Check if user has any of the specified groups.
     *
     * Replaces: QubitAcl::hasGroup($groupIds)
     */
    public function hasGroup(array $groupIds, ?object $user = null): bool
    {
        $user = $user ?? $this->user;

        if (!$user) {
            return false;
        }

        $hasGroup = DB::table('aclUserGroup')
            ->where('userId', $user->id)
            ->whereIn('groupId', $groupIds)
            ->exists();

        return $hasGroup;
    }

    /**
     * Get user's groups.
     */
    public function getUserGroups(?object $user = null): array
    {
        $user = $user ?? $this->user;

        if (!$user) {
            return [];
        }

        $groups = DB::table('aclUserGroup as ug')
            ->join('aclGroup as g', 'ug.groupId', '=', 'g.id')
            ->where('ug.userId', $user->id)
            ->select('g.id', 'g.name')
            ->get();

        return $groups->map(fn ($item) => (object) $item)->all();
    }

    /**
     * Check permission for user on resource.
     */
    private function checkPermission(int $userId, ?int $resourceId, int $actionId): bool
    {
        // Get user's groups
        $groupIds = DB::table('aclUserGroup')
            ->where('userId', $userId)
            ->pluck('groupId')
            ->all();

        if (empty($groupIds)) {
            return false;
        }

        // Check ACL permissions
        $query = DB::table('aclPermission')
            ->whereIn('groupId', $groupIds)
            ->where('action', $actionId);

        // If resource specified, check specific permission
        if ($resourceId !== null) {
            $query->where(function ($q) use ($resourceId) {
                $q->where('objectId', $resourceId)
                  ->orWhereNull('objectId'); // Global permissions
            });
        }

        return $query->exists();
    }

    /**
     * Map action string to action ID.
     */
    private function getActionId(string $action): ?int
    {
        $actionMap = [
            'create' => self::CREATE,
            'read' => self::READ,
            'update' => self::UPDATE,
            'delete' => self::DELETE,
            'translate' => self::TRANSLATE,
            'publish' => self::PUBLISH,
            'discover' => self::DISCOVER,
            'viewDraft' => self::VIEW_DRAFT,
            'administrator' => 100, // Special case
        ];

        return $actionMap[strtolower($action)] ?? null;
    }

    /**
     * Check if current user can create resources.
     */
    public function canCreate(?object $resource = null, ?object $user = null): bool
    {
        return $this->check($resource, 'create', $user);
    }

    /**
     * Check if current user can read resources.
     */
    public function canRead(?object $resource = null, ?object $user = null): bool
    {
        return $this->check($resource, 'read', $user);
    }

    /**
     * Check if current user can update resources.
     */
    public function canUpdate(?object $resource = null, ?object $user = null): bool
    {
        return $this->check($resource, 'update', $user);
    }

    /**
     * Check if current user can delete resources.
     */
    public function canDelete(?object $resource = null, ?object $user = null): bool
    {
        return $this->check($resource, 'delete', $user);
    }

    /**
     * Check if current user can publish resources.
     */
    public function canPublish(?object $resource = null, ?object $user = null): bool
    {
        return $this->check($resource, 'publish', $user);
    }

    /**
     * Check if current user can translate resources.
     */
    public function canTranslate(?object $resource = null, ?object $user = null): bool
    {
        return $this->check($resource, 'translate', $user);
    }
}
