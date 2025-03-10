<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;

class CountController extends Controller
{
    private $params;
    
    public function __construct(Request $request){
        $this->params = json_decode($request->getContent(), true);
    }
    
    public function postCount(Request $request){
        
        $company_id = $request->has('company_id') ? $request->company_id : (isset($this->params['company_id']) ? $this->params['company_id'] : 0);
        $variable = $request->has('variable') ? $request->variable : (isset($this->params['variable']) ? $this->params['variable'] : 0);

        $company = Company::find($company_id);
        if(!is_null($company)){
            switch($variable){
                case 'phone':
                    $company->phone_counter++;
                    $company->save();
                    return $company->phone_counter;
                break;
                case 'email':
                    $company->email_counter++;
                    $company->save();
                    return $company->email_counter;
                break;
                case 'map':
                    $company->map_counter++;
                    $company->save();
                    return $company->map_counter;
                break;
                case 'web':
                    $company->web_counter++;
                    $company->save();
                    return $company->web_counter;
                break;
            }
        }

        abort(404);

    }

}
