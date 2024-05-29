<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Throwable;

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

    public function create(CreateCompanyRequest $request)
    {
        try {
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }
    
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path
            ]);

            if (!$company) {
                throw new Exception("Company not created");
            }
    
            return ResponseFormatter::success($company, 'Company Successfully Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(),500);
        }
        

    }
}
