<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Read dengan pagination
    public function index(Request $request)
    {
        // Ambil parameter 'per_page' dari query string, default 10
        $perPage = $request->input('per_page', 10);

        // Mengambil data pengguna dengan pagination
        $users = User::paginate($perPage);

        // Mengembalikan data dalam format JSON
        return response()->json($users);
    }
    

    public function search(Request $request)
    {
        // Validasi input
        $request->validate([
            'search' => 'required|string|max:255', 
        ]);

        $searchTerm = $request->input('search');
        Log::info('Search term: ' . $searchTerm);
        
        // Melakukan pencarian dengan pagination
        $users = User::where('name', 'like', '%' . $searchTerm . '%')->paginate(10); 

        // Log jumlah hasil
        Log::info('Query result count:', ['count' => $users->count()]); 
        Log::info('Query result data:', ['data' => $users]); 

        return response()->json($users, 200);
    }

    // Create 
    public function store(Request $request)
    {
        Log::info('Store user request:', $request->all());

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|min:3|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
                
            ]);
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
    
            return response()->json($user, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        }
    }

    // Get by ID 
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'string|min:6',
        ]);

        $user = User::findOrFail($id);

        // Enkripsi password kalau perlu
        if ($request->has('password')) {
            $request->merge(['password' => bcrypt($request->password)]);
        }

        // Update data user
        $user->update($request->only(['name', 'email', 'password']));

        return response()->json($user);
    }

    // Delete 
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}