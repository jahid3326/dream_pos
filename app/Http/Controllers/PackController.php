<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pack;
use Illuminate\Support\Facades\DB;

class PackController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Pack,read')->only('index');
        $this->middleware('action.permission:Pack,create')->only(['create', 'store']);
        $this->middleware('action.permission:Pack,show')->only('show');
        $this->middleware('action.permission:Pack,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Pack,delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packs = Pack::latest()->paginate(15);
        return view('packs.index', compact('packs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pack = new Pack();
        return view('packs.create', compact('pack'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pack $pack)
    {
        $pack->load('groups.options');
        return view('packs.show', compact('pack'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pack $pack)
    {
        $pack->load('groups.options');
        return view('packs.edit', compact('pack'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pack $pack)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'groups' => 'sometimes|array',
            'groups.*.surface' => 'required|string',
            'groups.*.options' => 'sometimes|array',
            'groups.*.options.*.option' => 'required|integer',
            'groups.*.options.*.price' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request, $pack) {
            $pack->update(['name' => $request->name]);

            $submittedGroupIds = [];
            if ($request->has('groups')) {
                foreach ($request->groups as $groupData) {
                    $group = $pack->groups()->updateOrCreate(
                        ['id' => $groupData['id'] ?? null],
                        ['surface' => $groupData['surface']]
                    );
                    $submittedGroupIds[] = $group->id;

                    $submittedOptionIds = [];
                    if (isset($groupData['options'])) {
                        foreach ($groupData['options'] as $optionData) {
                            $option = $group->options()->updateOrCreate(
                                ['id' => $optionData['id'] ?? null],
                                ['option' => $optionData['option'], 'price' => $optionData['price']]
                            );
                            $submittedOptionIds[] = $option->id;
                        }
                    }
                    // Delete options that were removed from this group
                    $group->options()->whereNotIn('id', $submittedOptionIds)->delete();
                }
            }
            // Delete groups that were removed from the form
            $pack->groups()->whereNotIn('id', $submittedGroupIds)->delete();
        });

        return redirect()->route('packs.index')->with('success', 'Pack updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pack $pack)
    {
        $pack->delete();
        return redirect()->route('packs.index')->with('success', 'Pack deleted successfully.');
    }
}
