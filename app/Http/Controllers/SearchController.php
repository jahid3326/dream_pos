<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Sale;
use App\Models\Quote;
use App\Models\Purchase;
use App\Models\Shipment;

class SearchController extends Controller
{
    /**
     * Display search results grouped by type.
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $table = $request->get('table', 'all');

        $results = [];

        if ($q === '') {
            return view('search.results', compact('results', 'q', 'table'));
        }

        $like = "%" . $q . "%";

        $map = [
            'sales' => ['model' => Sale::class, 'column' => 'invoice_number', 'route' => 'sales.show'],
            'quotes' => ['model' => Quote::class, 'column' => 'quote_number', 'route' => 'quotes.show'],
            'purchases' => ['model' => Purchase::class, 'column' => 'purchase_number', 'route' => 'purchases.show'],
            'shipments' => ['model' => Shipment::class, 'column' => 'shipment_number', 'route' => 'shipments.show'],
        ];

        $tablesToSearch = $table && $table !== 'all' ? [$table] : array_keys($map);

        foreach ($tablesToSearch as $t) {
            if (!isset($map[$t])) continue;
            $info = $map[$t];
            $model = $info['model'];
            $col = $info['column'];

            // limit results to reasonable number
            $rows = $model::where($col, 'like', $like)
                ->limit(50)
                ->get();

            $results[$t] = $rows;
        }

        return view('search.results', compact('results', 'q', 'table'));
    }

    /**
     * Return autocomplete suggestions (JSON) for the selected table or across all.
     */
    public function suggest(Request $request)
    {
        $q = trim($request->get('q', ''));
        $table = $request->get('table', 'all');

        $map = [
            'sales' => ['model' => Sale::class, 'column' => 'invoice_number'],
            'quotes' => ['model' => Quote::class, 'column' => 'quote_number'],
            'purchases' => ['model' => Purchase::class, 'column' => 'purchase_number'],
            'shipments' => ['model' => Shipment::class, 'column' => 'shipment_number'],
        ];

        $suggestions = [];

        if ($q === '') {
            return response()->json(['success' => true, 'suggestions' => $suggestions]);
        }

        $tablesToSearch = $table && $table !== 'all' ? [$table] : array_keys($map);

        foreach ($tablesToSearch as $t) {
            if (!isset($map[$t])) continue;
            $info = $map[$t];
            $model = $info['model'];
            $col = $info['column'];

            $rows = $model::where($col, 'like', $q . '%')
                ->distinct()
                ->limit(10)
                ->pluck($col)
                ->map(function ($v) use ($t) {
                    return ['type' => $t, 'value' => $v];
                })
                ->toArray();

            $suggestions = array_merge($suggestions, $rows);
        }

        return response()->json(['success' => true, 'suggestions' => $suggestions]);
    }
}
