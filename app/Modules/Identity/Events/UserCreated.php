<?php

namespace App\Modules\Identity\Events;

use App\Modules\Identity\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Phat ra sau khi tao tai khoan. Khong mang mat khau trong event (se bi
 * serialize vao queue); Listener gui mail se dung luong reset mat khau.
 */
class UserCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly User $user) {}
}
