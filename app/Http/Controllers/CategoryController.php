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
    public function index(Request $request)
    {
        // Fetch ONLY top-level categories and eager-load their children.
        // The view will handle the recursion.
        $query = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->with('children')->orderBy('name'); // Load children of children
            }])
            ->orderBy('name');

        // Handle Status Filter
        if ($request->has('status') && $request->status !== '') {
            // This filter will now only apply to top-level categories.
            // A more complex filter would require a different approach.
            $query->where('status', $request->status);
        }

        $categories = $query->get();

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
            'status' => 'required|boolean',
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
        $category->load('parent');
        // Get potential parents, excluding the current category and its descendants
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {

        // Check if the user is trying to activate a child of an inactive parent.
        if ($request->status == 1 && $category->parent_id) {
            $parent = Category::find($category->parent_id);
            if ($parent && !$parent->status) { // If parent exists and is inactive (status == 0)
                return back()->with('error', 'Cannot activate a child category while its parent is inactive. Please activate the parent category first.');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        $data = $request->except('logo');

        // Prevent a category from being its own parent
        if ($request->parent_id == $category->id) {
            return back()->with('error', 'A category cannot be its own parent.');
        }

        // Get the original status before any changes are made
        $originalStatus = $category->status;

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

        // Check if the status was changed in the form submission
        $newStatus = (bool) $request->status;
        if ($newStatus !== (bool) $originalStatus) {
            // If the status has changed, call our new recursive method
            $category->load('children'); // Ensure children are loaded before recursion
            $category->updateStatusRecursively($newStatus);
        }

        return redirect()->route('categories.index')->with('success', 'Category and its sub-categories updated successfully.');
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
