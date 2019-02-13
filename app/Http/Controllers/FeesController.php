<?php

namespace App\Http\Controllers;

use App\Repositories\Fee;
use Illuminate\Http\Request;

class FeesController extends Controller
{
    protected $data = [];
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    function add()
    {

        if ($this->request->developer_id == '') {
            $this->data['msg'] = 'Developer id required';
            $this->data['status'] = 0;
            return $this->response($this->data);
        }


       $fee = Fee::whereSchoolId($this->request->schoolID)->whereName($this->request->feeName)->first();

        try {
            if (!$fee) {
                $fee = new Fee();
                $fee->name = $this->request->feeName;
                $fee->school_id = $this->request->schoolID;
                $fee->school_name = $this->request->schoolName;
                $fee->created_at = now();
                $fee->updated_at = now();

                $fee->save();

                $this->data['msg'] = 'Fee Created Successfully';
                $this->data['status'] = 1;
                $this->data['feeID'] = $fee->id;
            } else {
                $this->data['msg'] = 'Fee already exist';
                $this->data['status'] = 0;
            }
        } catch (\Exception $exception) {
            $this->data['msg'] = 'Failed to create fee ' . $exception->getMessage();
            $this->data['status'] = 0;
        }

        return $this->response($this->data);
    }

    function update()
    {
        if ($_POST) {
            $id = $this->request->feeID;
            $schoolID = $this->request->schoolID;

            if ($id != '' && $schoolID != '') {
                $feeID = $this->request->feeID;
                $schoolID = $this->request->schoolID;

                $fee = (new Fee)->getFee($feeID, $schoolID);

                if (true == $fee) {

                    $fee->name = $this->request->feeName;
                    $fee->school_name = $this->request->schoolName;
                    if ($fee->save()) {
                        $this->data['msg'] = 'Fee Updated Successfully';
                        $this->data['status'] = 1;
                        $this->data['feeID'] = $fee->id;
                    } else {
                        $this->data['msg'] = 'Fee not found';
                        $this->data['status'] = 0;
                    }
                } else {
                    $this->data['msg'] = 'Failed to update fee';
                    $this->data['status'] = 0;
                }
            } else {
                $this->data['msg'] = 'Invalid request detected';
                $this->data['status'] = 0;
            }
        }

        return $this->response($this->data);
    }

    public function delete()
    {
        if ($_POST) {
            $id = $this->request->feeID;
            $schoolID = $this->request->schoolID;

            if ($id != '' && $schoolID != '') {
                $feeID = $this->request->feeID;
                $schoolID = $this->request->schoolID;

                $fee = (new Fee())->getFee($feeID, $schoolID);

                if (true == $fee) {
                    if ($fee->delete()) {
                        $this->data['msg'] = 'Fee Deleted Successfully';
                        $this->data['status'] = 1;
                    } else {
                        $this->data['msg'] = 'Fee not deleted';
                        $this->data['status'] = 0;
                    }
                } else {
                    $this->data['msg'] = 'Fee not found!';
                    $this->data['status'] = 0;
                }
            } else {
                $this->data['msg'] = 'Invalid request detected';
                $this->data['status'] = 0;
            }

        }

        return $this->response($this->data);
    }

    public function response(array $data = [])
    {
        return response()->json($data);
    }
}
