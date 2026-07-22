<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display the Admin Overview Dashboard.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        $ports = Port::with('country')->orderBy('created_at', 'desc')->get();
        $articles = Article::with('author')->orderBy('created_at', 'desc')->get();
        $countries = Country::orderBy('name', 'asc')->get();

        $totalUsers = $users->count();
        $totalAdmins = $users->where('role', 'admin')->count();
        $totalRegularUsers = $users->where('role', 'user')->count();

        $totalCountries = $countries->count();
        $totalPorts = $ports->count();
        $totalArticles = $articles->count();

        $recentUsers = $users->take(5);
        $recentArticles = $articles->take(5);

        return view('admin.dashboard', compact(
            'users',
            'ports',
            'articles',
            'countries',
            'totalUsers',
            'totalAdmins',
            'totalRegularUsers',
            'totalCountries',
            'totalPorts',
            'totalArticles',
            'recentUsers',
            'recentArticles'
        ));
    }

    // ==========================================
    // --- USER MANAGEMENT PAGE & CRUD ---
    // ==========================================

    public function usersIndex()
    {
        $users = User::orderBy('id', 'asc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,user'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', "Akun pengguna '{$request->name}' berhasil dibuat dengan role '{$request->role}'.");
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,user'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (Auth::id() === $user->id && $request->role !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri dari Admin.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    public function destroyUser(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akun pengguna '{$name}' telah dihapus.");
    }

    // ==========================================
    // --- PORT DATASET PAGE & CRUD ---
    // ==========================================

    public function portsIndex(\Illuminate\Http\Request $request)
    {
        $search = $request->input('search');
        $ports = Port::with('country')
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(100)
                ->appends(['search' => $search]);
        $countries = Country::orderBy('name', 'asc')->get();
        return view('admin.ports.index', compact('ports', 'countries', 'search'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        Port::create([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', "Pelabuhan '{$request->name}' berhasil ditambahkan ke dataset.");
    }

    public function updatePort(Request $request, Port $port)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $port->update([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', "Data pelabuhan '{$port->name}' berhasil diperbarui.");
    }

    public function destroyPort(Port $port)
    {
        $name = $port->name;
        $port->delete();

        return back()->with('success', "Pelabuhan '{$name}' telah dihapus dari dataset.");
    }

    // ==========================================
    // --- ARTICLES PAGE & CRUD ---
    // ==========================================

    public function articlesIndex()
    {
        $articles = Article::with('author')->orderBy('created_at', 'desc')->get();
        return view('admin.articles.index', compact('articles'));
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'content.required' => 'Isi artikel wajib diisi.',
        ]);

        Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'category' => 'General',
            'sentiment' => 'neutral',
            'author_id' => Auth::id(),
        ]);

        return back()->with('success', "Artikel '{$request->title}' berhasil disimpan.");
    }

    public function updateArticle(Request $request, Article $article)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'content.required' => 'Isi artikel wajib diisi.',
        ]);

        $article->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', "Artikel '{$article->title}' berhasil diperbarui.");
    }

    public function destroyArticle(Article $article)
    {
        $title = $article->title;
        $article->delete();

        return back()->with('success', "Artikel '{$title}' telah dihapus.");
    }
}
