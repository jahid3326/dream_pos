<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoryImport;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Category,read')->only('index');
        $this->middleware('action.permission:Category,create')->only(['create', 'store']);
        $this->middleware('action.permission:Category,show')->only('show');
        $this->middleware('action.permission:Category,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Category,delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')
                        ->with([
                            'parent', // Load parent relationship
                            'children' => function ($query) {
                                $query->with('parent', 'children')->orderBy('name');
                            },
                            'children.children' => function($query){
                                $query->with('parent')->orderBy('name');
                            }
                        ])
                        ->orderBy('name')->get();
                        
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'logo' => 'nullable|image|max:2048',
        ]);
    
        $data = $request->except('logo');
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('category_logos', 'public');
        }
    
        Category::create($data);
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Get potential parents, excluding the current category and its descendants
        $parentCategories = Category::whereNull('parent_id')
                                    ->where('id', '!=', $category->id)
                                    ->orderBy('name')->get();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'logo' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($category->logo) {
                Storage::disk('public')->delete($category->logo);
            }
            $data['logo'] = $request->file('logo')->store('category_logos', 'public');
        }

        // Prevent a category from being its own parent
        if ($request->parent_id == $category->id) {
            return back()->with('error', 'A category cannot be its own parent.');
        }

        $category->update($data);
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function showImportForm()
    {
        return view('categories.import');
    }

    public function import(Request $request)
    {
        $request->validate(['category_file' => 'required|mimes:xlsx,csv']);

        $import = new CategoryImport;
        Excel::import($import, $request->file('category_file'));

        if (!empty($import->errors)) {
            return redirect()->route('categories.import.show')
                             ->with('import_errors', $import->errors)
                             ->with('error', "Import finished with errors. {$import->importedCount} records imported.");
        }

        return redirect()->route('categories.index')
                         ->with('success', "All {$import->importedCount} categories imported successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Note: The onDelete('cascade') in the migration will handle deleting children.
        // We just need to delete the logo file from storage.
        if ($category->logo) {
            Storage::disk('public')->delete($category->logo);
        }
        
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category and all its sub-categories deleted successfully.');
    }
}
