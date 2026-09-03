<?php

namespace App\Support;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Shared helpers for API order resolution and authorization.
 */
class ApiAccess
{
    /**
     * Staff-like roles that may access any order / warehouse ops.
     */
    public static function isStaffRole($user): bool
    {
        if (!$user) {
            return false;
        }
        return in_array((int) $user->role, [
            User::ROLE_ADMIN,
            User::ROLE_STAFF,
            User::ROLE_PICKER,
            User::ROLE_PACKER,
            User::ROLE_RECEIVER,
        ], true);
    }

    public static function isCustomerRole($user): bool
    {
        return $user && (int) $user->role === User::ROLE_USER;
    }

    /**
     * Resolve order by primary id or order_code (e.g. ODR0000091955).
     */
    public static function resolveOrder($idOrCode): ?Order
    {
        if ($idOrCode === null || $idOrCode === '') {
            return null;
        }

        $key = is_string($idOrCode) ? trim($idOrCode) : $idOrCode;

        return Order::query()
            ->where(function ($q) use ($key) {
                if (is_numeric($key)) {
                    $q->where('id', (int) $key)
                        ->orWhere('order_code', (string) $key);
                } else {
                    $q->where('order_code', (string) $key);
                }
            })
            ->first();
    }

    /**
     * Customer may only access own orders; staff/admin may access all.
     */
    public static function canAccessOrder($user, ?Order $order): bool
    {
        if (!$user || !$order) {
            return false;
        }
        if (self::isStaffRole($user)) {
            return true;
        }
        return (int) $order->user_id === (int) $user->id;
    }

    /**
     * Forbidden JSON response for APIs.
     */
    public static function forbidden(string $message = 'Forbidden')
    {
        return response()->json([
            'status' => 'error',
            'message_code' => 'FORBIDDEN',
            'message' => $message,
        ], 403);
    }

    public static function notFound(string $message = 'Not found')
    {
        return response()->json([
            'status' => 'error',
            'message_code' => 'NOT_FOUND',
            'message' => $message,
        ], 404);
    }
}
