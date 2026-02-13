<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Reset demo data
     */
    public function reset()
    {
        try {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

            return response()->json([
                'message' => 'Demo data reset successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reset data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
