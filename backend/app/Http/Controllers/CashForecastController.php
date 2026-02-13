<?php

namespace App\Http\Controllers;

use App\Services\CashForecastService;
use Illuminate\Http\Request;

class CashForecastController extends Controller
{
    private CashForecastService $cashService;

    public function __construct(CashForecastService $cashService)
    {
        $this->cashService = $cashService;
    }

    public function index()
    {
        $forecast = $this->cashService->getForecast([30, 60, 90]);

        return response()->json($forecast);
    }
}
