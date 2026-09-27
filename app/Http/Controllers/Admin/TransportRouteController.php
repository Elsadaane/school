<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportRoute;
use App\Models\Branche;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransportRoute::with('branch')->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $routes = $query->paginate(15)->withQueryString();
        $branches = Branche::where('is_active', 1)->get();

        return view('backend.transport_routes.index', compact('routes', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branche::where('is_active', 1)->get();
        return view('backend.transport_routes.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'monthly_fare_both' => 'required|numeric|min:0',
            'monthly_fare_single' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        TransportRoute::create($request->all());

        return redirect()->route('admin.transport-routes.index')
            ->with('success', 'تم إضافة خط السير بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $route = TransportRoute::findOrFail($id);
        $branches = Branche::where('is_active', 1)->get();
        return view('backend.transport_routes.edit', compact('route', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $route = TransportRoute::findOrFail($id);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'monthly_fare_both' => 'required|numeric|min:0',
            'monthly_fare_single' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $route->update($request->all());

        return redirect()->route('admin.transport-routes.index')
            ->with('success', 'تم تحديث خط السير بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $route = TransportRoute::findOrFail($id);
        
        // التحقق من وجود حافلات مرتبطة بهذا الخط
        if ($route->buses()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'لا يمكن حذف خط السير لأنه مرتبط بحافلات. يرجى إزالة الحافلات أولاً.']);
        }

        $route->delete();

        return redirect()->route('admin.transport-routes.index')
            ->with('success', 'تم حذف خط السير بنجاح.');
    }
}