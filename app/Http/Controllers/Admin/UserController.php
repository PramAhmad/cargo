<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Enums\UserStatus;
use App\Enums\UserGender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user.view')->only('index');
        $this->middleware('can:user.create')->only('create', 'store');
        $this->middleware('can:user.edit')->only('edit', 'update');
        $this->middleware('can:user.delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->orderBy('id', 'desc')
            ->when(! empty($request->search), function ($query) use ($request)
            {
                return $query
                    ->where('name', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('email', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('phone', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('status', 'LIKE', '%'.$request->search.'%');
            })
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.user.index', [
            'users' => UserResource::collection($users),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $statuses = UserStatus::cases();
        $genders = UserGender::cases();

        return view('dashboard.user.create', compact('roles', 'statuses', 'genders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'status' => ['required', new Enum(UserStatus::class)],
            'gender' => ['nullable', new Enum(UserGender::class)],
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'language' => 'nullable|string|max:10',
            'time_zone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.user.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'status' => $request->status,
                'gender' => $request->gender,
                'language' => $request->language ?? 'en',
                'time_zone' => $request->time_zone ?? 'UTC',
            ]);

            // Assign roles to user
            // search for the role by name from id
            $roles = Role::whereIn('id', $request->roles)->get();
            foreach ($roles as $role) {
                $user->syncRoles($role);
            }

           
            // Handle image upload
            if ($request->hasFile('image')) {
                $user->addMedia($request->file('image'))
                    ->toMediaCollection('images');
            }

            DB::commit();

            return redirect()
                ->route('admin.user.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('admin.user.create')
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        
        return view('dashboard.user.show', [
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::orderBy('name')->get();
        $statuses = UserStatus::cases();
        $genders = UserGender::cases();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('dashboard.user.edit', compact('user', 'roles', 'statuses', 'genders', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'status' => ['required', new Enum(UserStatus::class)],
            'gender' => ['nullable', new Enum(UserGender::class)],
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'language' => 'nullable|string|max:10',
            'time_zone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.user.edit', $user->id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update user basic information
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
                'gender' => $request->gender,
                'language' => $request->language ?? $user->language,
                'time_zone' => $request->time_zone ?? $user->time_zone,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            $roles = Role::whereIn('id', $request->roles)->get();
            foreach ($roles as $role) {
                $user->syncRoles($role);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete previous images
                $user->clearMediaCollection('images');
                
                // Add new image
                $user->addMedia($request->file('image'))
                    ->toMediaCollection('images');
            }

            DB::commit();

            return redirect()
                ->route('admin.user.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('admin.user.edit', $user->id)
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Check if user is deletable
            if (!$user->isDeletable) {
                return redirect()
                    ->route('admin.user.index')
                    ->with('error', 'This user cannot be deleted due to system restrictions.');
            }
            
            // Delete the user (soft delete via SoftDeletes trait)
            $user->delete();
            
            return redirect()
                ->route('admin.user.index')
                ->with('success', 'User deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.user.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
