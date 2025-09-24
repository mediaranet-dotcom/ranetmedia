<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        // Redirect langsung ke URL yang benar
        return redirect('/admin/resources/customer-report-resources');
    }
}
