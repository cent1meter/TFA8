<?php
namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AvatarStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;
class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['title' => 'User Accounts', 'users' => User::query()->orderBy('id')->get()]);
    }
    public function create(): View
    {
        return view('users.form', ['title' => 'Create User', 'user' => new User]);
    }
    public function store(UserRequest $request): RedirectResponse
    {
        User::create($request->safe()->only(['username', 'full_name']));
        return to_route('users.index')->with('success', 'User created successfully. You can now edit the user to add an avatar.');
    }
    public function show(User $user): View
    {
        return view('users.show', ['title' => 'User Details', 'user' => $user]);
    }
    public function edit(User $user): View
    {
        return view('users.form', ['title' => 'Edit User', 'user' => $user]);
    }
    public function update(UserRequest $request, User $user, AvatarStorage $avatars): RedirectResponse
    {
        $data = $request->safe()->only(['username', 'full_name']);
        $newPath = null;
        if ($request->hasFile('avatar')) {
            $newPath = $avatars->store($request->file('avatar'));
            $data['avatar'] = $newPath;
        }
        $oldPath = null;
        try {
            DB::transaction(function () use ($user, $data, &$oldPath): void {
                // Re-read inside the lock so simultaneous replacements clean up correctly.
                $record = User::query()->lockForUpdate()->findOrFail($user->id);
                $oldPath = $record->avatar;
                $record->fill($data)->save();
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                try { $avatars->delete($newPath); } catch (Throwable $cleanup) { report($cleanup); }
            }
            throw $exception;
        }
        if ($newPath && $oldPath && $oldPath !== $newPath) {
            try { $avatars->delete($oldPath); } catch (Throwable $cleanup) { report($cleanup); }
        }
        return to_route('users.index')->with('success', 'User updated successfully.');
    }
    public function destroy(\Illuminate\Http\Request $request, User $user, AvatarStorage $avatars): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return to_route('users.index')->with('error', 'You cannot delete the account you are signed in with.');
        }
        $oldPath = $user->avatar;
        $user->delete();
        try { $avatars->delete($oldPath); } catch (Throwable $cleanup) { report($cleanup); }
        return to_route('users.index')->with('success', 'User deleted successfully.');
    }
}
