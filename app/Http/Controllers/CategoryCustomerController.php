<?php

namespace App\Http\Controllers;

use App\Models\CategoryCustomer;
use Illuminate\Http\Request;

class CategoryCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoryCustomers = CategoryCustomer::select('*')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('backend.category-customers.index', compact('categoryCustomers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.category-customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        CategoryCustomer::create($validated);

        return redirect()->route('backend.category-customers.index')
            ->with('success', 'Category Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryCustomer $categoryCustomer)
    {
        return view('backend.category-customers.show', compact('categoryCustomer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryCustomer $categoryCustomer)
    {
        return view('backend.category-customers.edit', compact('categoryCustomer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoryCustomer $categoryCustomer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $categoryCustomer->update($validated);

        return redirect()->route('backend.category-customers.index')
            ->with('success', 'Category Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryCustomer $categoryCustomer)
    {
        $categoryCustomer->delete();

        return redirect()->route('backend.category-customers.index')
            ->with('success', 'Category Customer deleted successfully.');
    }
}