<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function get_data(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // hrcm.com/api/company?id=1
        if ($id) {
            $company = Company::with(['users'])->find($id);

            if ($company) {
                return ResponseFormatter::success($company, 'Company found');
            }

            return ResponseFormatter::error('Company not found');
        }

        // hrcm.com/api/company
        $companies = Company::get();

        if ($name) {
            $companies->where('name', 'like', '%'.$name.'%');
        }

        return ResponseFormatter::success(
            $companies,
            'Companies found'
        );  
    }
}
