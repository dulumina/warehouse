<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with(['product', 'supplier', 'inventory'])
            ->paginate(15);
        return view('batches.index', compact('batches'));
    }

    public function show(Batch $batch)
    {
        $batch->load(['product', 'inventory', 'serialNumbers']);
        return view('batches.show', compact('batch'));
    }
}