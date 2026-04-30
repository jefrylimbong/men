<?php

namespace App\Observers;

use App\Models\AndroidActionHistory;
use App\Models\User;
use App\Models\WithdrawalData;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        if ($user->isDirty('avatar')) {
            $oldAvatar = $user->getOriginal('avatar');

            if ($oldAvatar && ! in_array($oldAvatar, ['null', 'avatars/user.png', '/avatars/user.png'])) {
                $cleanPath = str_replace('storage/', '', $oldAvatar);
                if (Storage::disk('public')->exists($cleanPath)) {
                    Storage::disk('public')->delete($cleanPath);
                }
            }
        }
    }

    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        // Cek referensi di tabel lain
        $hasReferences = WithdrawalData::where('user_id', $user->id)->exists() ||
                         AndroidActionHistory::where('user_id', $user->id)->exists();

        if ($hasReferences) {
            throw new \Exception('User tidak dapat dihapus karena memiliki riwayat data. Silakan nonaktifkan saja.');
        }

        if ($user->avatar && ! in_array($user->avatar, ['null', 'avatars/user.png', '/avatars/user.png'])) {
            $cleanPath = str_replace('storage/', '', $user->avatar);
            if (Storage::disk('public')->exists($cleanPath)) {
                Storage::disk('public')->delete($cleanPath);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Avatar logic moved to deleting to ensure it runs before DB deletion
    }
}
