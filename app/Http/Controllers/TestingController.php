<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class TestingController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function addFeeForm()
    {
        return view('tests.add-fee');
    }

    public function storeFee()
    {
        $data = [
            'developer_id' => 'default',
            'feeName' => $this->request->feeName,
            'schoolID' => $this->request->schoolID,
            'schoolName' => $this->request->schoolName
        ];

        try {
            $req = (new VersionsController($this->request))->httpRequest('http://localhost/ischoolapi/v1/fees/add', 'POST', $data);
            dd($req);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
