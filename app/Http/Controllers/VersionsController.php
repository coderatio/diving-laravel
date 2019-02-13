<?php

namespace App\Http\Controllers;

use App\iSchool\Str;
use App\Repositories\Access;
use App\Repositories\Fee;
use Exception;
use Illuminate\Http\Request;

class VersionsController extends Controller
{

    protected $data = array();
    protected $ip = '';
    protected $ref = '';
    protected $agent = '';
    protected $key = '';
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index() {
        $this->demoPage();
    }

    public function getRef() {
        return Str::random_string(30);
    }

    /**
     * @throws Exception
     */
    public function validateStudent() {

        $this->getHeader();

        $mat_no = $this->request->paycode;
        $dbStatus = '';
        $schoolID = '';
        $schoolName = '';

        if($mat_no != '') {
            $developerId = $this->request->developer_id;
            $developer_id = '';

            if(isset($developerId)) {
                $developer_id = $developerId;
            }

            $nkstSonUrl = "http://ischools.valuebeam.com.ng/nursing/api/request";
            $nkstSomUrl = "http://ischools.valuebeam.com.ng/nursing/api/request/";
            $nkstChtUrl = "http://localhost/ischool-apis/health-tech/api/request/";
            $nkstLabsUrl = "http://ischools.valuebeam.com.ng/vb/api/request/";


            $sonParam = array(
                'key' => 't1g83ws6cg0a91ko2hfwh421pufrhx1319un27aison',
                'type' => 'get-student',
                'id' => $mat_no,
                'developer_id' => $developer_id
            );
            $somParam = array(
                'key' => 't1g83ws6cg0a91ko2hfwh421pufrhx1319un27aisom',
                'type' => 'get-student',
                'id' => $mat_no,
                'developer_id' => $developer_id
            );
            $chtParam = array(
                'key' => 't1g83ws6cg0a91ko2hfwh421pufrhx1319un27aicht',
                'type' => 'get-student',
                'id' => $mat_no,
                'developer_id' => $developer_id
            );
            $labsParam = array(
                'key' => 't1g83ws6cg0a91ko2hfwh421pufrhx1319un27ai',
                'type' => 'get-student',
                'id' => $mat_no,
                'developer_id' => $developer_id
            );

            $sonStudentData = $this->httpRequest($nkstSonUrl, 'POST', $sonParam);
            $somStudentData = $this->httpRequest($nkstSomUrl, 'POST', $somParam);
            $labsStudentData = $this->httpRequest($nkstLabsUrl, 'POST', $labsParam);

            $sonStudentJson = json_decode($sonStudentData);
            $somStudentJson = json_decode($somStudentData);
            $labsStudentJson = json_decode($labsStudentData);
            if(isset($sonStudentJson->result[0]) && count($sonStudentJson->result[0]) > 0) {
                $studentJson = $sonStudentJson->result[0];
            } elseif(isset($somStudentJson->result[0]) && count($somStudentJson->result[0]) > 0) {
                $studentJson = $somStudentJson->result[0];
            } elseif(isset($labsStudentJson->result[0]) && count($labsStudentJson->result[0]) > 0) {
                $studentJson = $labsStudentJson->result[0];
            } else {
                $studentJson = array();
            }

            if(count($studentJson) == 0) {
                $dbStatus = 'failed';
                $this->data['status'] = 0;
                $this->data['msg'] = 'No matching student found for this matric number';
                $this->data['ref'] = '';
                $this->response($this->data);
            } else {
                $student = $studentJson;
                $name = explode(' ', $student->name);
                if(count($name) > 2) {
                    $surName = $name[2];
                } elseif(count($name) == 2) {
                    $surName = $name[1];
                } else {
                    $surName = $name[1];
                }
                $this->data['status'] = 1;
                $this->data['matric_no'] = strtoupper($student->matNo);
                $this->data['school_name'] = $student->school_id;
                $this->data['school_fullname'] = $student->school_name;
                $this->data['first_name'] = ucfirst($name[0]);
                $this->data['surname'] = ucfirst($surName);
                $this->data['email_address'] = $student->email;
                // Db Data
                $db['status'] = 'success';
                $db['schoolID'] = $student->school_id;
                $db['schoolName'] = $student->school_name;
                $this->saveApiAccess($db);

                $this->response($this->data);
            }

        } else {
            $this->data['status'] = 0;
            $this->data['msg'] = 'Matric Number is required';
            $this->data['ref'] = '';
            $this->response($this->data);
        }


    }

    /**
     * @throws Exception
     */
    public function validatePaycode() {
        //$this->getHeader();
        //set_time_limit(3000);

        if(isset($this->request->paycode)) {
            $paycode = $this->request->paycode;

            //$pc = $this->db->table('sys_paycodes')->where('code', $paycode)->find_one();
            $developer_id = '';
            $developerId = $this->request->developer_id;
            if(isset($developerId)) {
                $developer_id = $developerId;
            }

            $nkstSonUrl = "http://localhost/ischoolpro/api/";
            $nkstSomUrl = "http://localhost/ischoolpro/api/";
            $nkstChtUrl = "http://localhost/ischoolpro/api/request";
            $nkstLabsUrl = "http://localhost/ischoolpro/api/request";


            $sonParam = array(
                'key' => 't1g83ws6cg0a91ko2hfwh421pufrhx1319un27aison',
                'paycode' => $paycode,
                'developer_id' => $developer_id
            );
            $somParam = array(
                'key' => 't1g83ws6cg0a91ko2hfwh421pufrhx1319un27aisom',
                'paycode' => $paycode,
                'developer_id' => $developer_id
            );
            $labsParam = array(
                'key' => 't1g83ws6cg0a91ko2hfwh421pufrhx1319un27ai',
                'paycode' => $paycode,
                'developer_id' => $developer_id
            );

            $pathUrl = 'getStudentByPayCode';

            $sonStudentData = $this->httpRequest($nkstSonUrl.$pathUrl, 'POST', $sonParam);
            $somStudentData = $this->httpRequest($nkstSomUrl.$pathUrl, 'POST', $somParam);
            $labsStudentData = $this->httpRequest($nkstLabsUrl.$pathUrl, 'POST', $labsParam);


            $sonStudentJson = json_decode($sonStudentData);
            $somStudentJson = json_decode($somStudentData);
            $labsStudentJson = json_decode($labsStudentData);

            if (isset($somStudentJson->SchoolID) && $somStudentJson->SchoolID == 3) {
                $db['status'] = 'success';
                $db['schoolID'] = $somStudentJson->SchoolID;
                $db['schoolName'] = 'School Of Midwifery, Mkar';
                $this->saveApiAccess($db);

                return $sonStudentData;
            }elseif (isset($sonStudentJson->SchoolID) && $sonStudentJson->SchoolID == 2) {
                $db['status'] = 'success';
                $db['schoolID'] = $sonStudentJson->SchoolID;
                $db['schoolName'] = 'School Of Nursing, Mkar';
                $this->saveApiAccess($db);

                return $sonStudentData;
            }  elseif (isset($labsStudentJson->SchoolID) && $labsStudentJson->SchoolID == 1) {
                $db['status'] = 'success';
                $db['schoolID'] = $labsStudentJson->SchoolID;
                $db['schoolName'] = 'ValueBeam University of Technology';
                $this->saveApiAccess($db);

                return $sonStudentData;
            }  else {
                $msg = json_encode(['Message' => 'No matching records found. Possibly, paycode already expired or misspelled', 'Status' => 0]);

                return $msg;
            }
        } else {
            if(!$_POST) {
                $msg = ['msg' => 'Invalid request detected', 'Status' => 0];
                $this->response($msg);
            }
            $msg = 'Paycode not provided';
        }

        $this->data['msg'] = $msg;
        $this->data['Status'] = 0;

        return $this->response($this->data);
    }

    public function saveApiAccess($data=array()) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $checkExisting = Access::where('ip_address', $ipAddress)->where('school_id', $data['schoolID'])->count();
        if($checkExisting == 0) {
            $apiAccess = new Access();
            $apiAccess->ip_address = $ipAddress;
            $apiAccess->school_id = $data['schoolID'];
            $apiAccess->school_name = $data['schoolName'];
            $apiAccess->access_times = 1;
            $apiAccess->ref_key = $this->getRef();
            $apiAccess->created_at = now();
            $apiAccess->status = $data['status'];
            $apiAccess->save();
        } else {
            $apiAccess = Access::where('ip_address', $ipAddress)->where('school_id', $data['schoolID'])->first();
            if ($apiAccess) {
                $apiAccess->status = $data['status'];
                $apiAccess->ref_key = $this->getRef();
                $apiAccess->access_times += 1;
                $apiAccess->updated_at = now();
                $apiAccess->save();
            }
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function getSchool($data=array()) {
        $school = $this->httpRequest($data['url'], 'POST', ['key' => $data['key']]);
        return json_decode($school);
    }

    public function response($data=array()){
        header('Content-type: application/json');
        echo json_encode( $data );
        exit;
    }

    public function getHeader() {
        if(isset($_SERVER['REMOTE_ADDR'])){
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }
        if(isset($_SERVER['HTTP_REFERER'])){
            $this->ref = $_SERVER['HTTP_REFERER'];
        }
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $this->agent = $_SERVER['HTTP_USER_AGENT'];
        }

        $this->data['ip'] = $this->ip;
        $this->data['ref'] = $this->getRef();
        //$this->data['agent'] = $this->agent;

        return $this->data;
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $headers
     * @param bool $resp_header
     * @param bool $follow_redirect
     * @return mixed|string
     * @throws Exception
     */
    function httpRequest($url, $method='GET', $params=array(), $headers=array(), $resp_header=false, $follow_redirect=false){

        $response = '';

        if (!is_callable('curl_init')) {

            throw new Exception('CURL Not available in this Server.');

        }

        switch ($method) {

            case 'GET':

                $q = '';
                foreach($params as $key=>$value) {

                    $value = urlencode($value);

                    $q .= $key.'='.$value.'&';

                }

                $req = $url;

                if($q != ''){
                    $q = rtrim($q, '&');

                    $req = $url.'?'.$q;

                }

                try {
                    $ch = curl_init();

                    if (FALSE === $ch)
                        throw new Exception('failed to initialize');

                    if (!empty($headers)) {

                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    }

                    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $req);

                    if($follow_redirect){
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                    }

                    if($resp_header){
                        curl_setopt($ch, CURLOPT_HEADER, 1);
                    }

                    $response = curl_exec($ch);

                    if (FALSE === $response)
                        throw new Exception(curl_error($ch), curl_errno($ch));
                    curl_close($ch);

                } catch(Exception $e) {

                    throw new Exception($e->getCode(). ' '. $e->getMessage());

                }

                break;

            case 'POST':

                try {
                    $ch = curl_init();

                    if (FALSE === $ch)
                        throw new Exception('failed to initialize');

                    if (!empty($headers)) {

                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    }

                    curl_setopt($ch, CURLOPT_URL,$url);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    if($resp_header){
                        curl_setopt($ch, CURLOPT_HEADER, 1);
                    }

                    $response = curl_exec($ch);

                    if (FALSE === $response)
                        throw new Exception(curl_error($ch), curl_errno($ch));
                    curl_close($ch);

                } catch(Exception $e) {

                    throw new Exception($e->getCode(). ' '. $e->getMessage());

                }

                break;
        }

        return $response;

    }

    public function demoPage() {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>iSchool Api v1 Access Demo Page</title>
            <meta content="width=device-width, initial-scale=1" name="viewport">
<!--            <link rel="stylesheet" href="--><?//=theme_url()?><!--assets/css/app.min.1.css">-->
<!--            <link rel="stylesheet" href="--><?//=theme_url()?><!--assets/css/app.min.2.css">-->
<!--            <link rel="stylesheet" href="--><?//=theme_url()?><!--assets/css/ischool.css">-->
            <link rel="stylesheet" href="<?=asset('assets/vendors/uikit/css/uikit.min.css')?>">
            <style>
                @media(max-width: 1000px) {
                    .col-sm-6.col-sm-offset-3 {
                        width: 90% !important;
                    }
                    .response {
                        overflow-x: scroll;
                    }
                }
                input[type=text], textarea, select {
                    -webkit-transition: all 0.30s ease-in-out;
                    -moz-transition: all 0.30s ease-in-out;
                    -ms-transition: all 0.30s ease-in-out;
                    -o-transition: all 0.30s ease-in-out;
                    outline: none;
                    margin: 5px 1px 3px 0px;
                    border: 1px solid #DDDDDD;
                }
                input{
                    border: 1px solid #ddd;
                    background: #fff;
                    box-shadow: none;
                    height: 35px;
                }
                select {
                    height: 45px;
                    padding: 3px 10px 3px 10px;
                }
                input, textarea {
                    width: 95%;
                    padding: 3px 10px 3px 10px;
                }
                select {
                    width: 100%;
                }
                input:focus {
                    border-color: #bbb;
                    box-shadow: none !important;
                }
                input[type=text]:focus, textarea:focus, select:focus {
                    box-shadow: 0 0 5px #ddd;
                    border: 1px solid #ddd;
                }
                input.uk-input,
                select.uk-select {
                    margin-bottom: 5px;
                    margin-top: 5px;
                    font-size: 16px;
                }
                .col-sm-6.col-sm-offset-3 {
                    width: 30%;
                    backgroun: #f1f1f1;
                    display: block;
                    padding: 15px 15px;
                    margin: 0 auto;
                }
                body,
                html,
                p,
                h1, h2, h3, h4, h5, h6, input, select, button {
                    font-family: 'Mallanna' !important;
                }
                html, body {
                    background: #f5f5f5 !important;
                }
                .uk-text-uppercase {
                    text-transform: uppercase;
                }
                .text-center {
                    text-align: center;
                }
                .uk-button {
                    height: 35px;
                    border: 0px solid;
                    padding-left: 15px;
                    padding-right: 15px;
                    cursor: pointer;
                    background: tomato;
                    text-transform: uppercase;
                }
                .uk-button:hover {
                    transition: 0.5s ease-in;
                    background: #333;
                }
                .content-center {
                    display: block;
                    margin: 0 auto;
                    text-align: center;
                }
                span.help-block {
                    line-height: 1em;
                    display: block;
                    border: 1px solid #5c8cdb;
                    padding: 5px 10px;
                    background: #bdd4f9;
                    margin-bottom: 10px;
                }
                .version {
                    display: block;
                    text-align: right;
                    margin-bottom: 10px;
                }
                .text-muted {
                    color: #bbb;
                }
            </style>
            <link href='https://fonts.googleapis.com/css?family=Signika+Negative:400,300,600,700' rel='stylesheet' type='text/css'>
            <link href="https://fonts.googleapis.com/css?family=Mallanna" rel="stylesheet">
            <!-- UIkit CSS -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.30/css/uikit.min.css" />

        </head>
        <body class="palette-White bg">
        <div class="col-sm-12 m-t-25 p-20 " uk-grid>
            <div class="uk-card uk-card-default uk-card-body response" style="color: #444">
                <p class=""><b>Response</b></p>
                <?=session('notice')?>
            </div>
        </div>
        <div class="col-sm-6 col-sm-offset-3 m-t-25 p-20">
            <div class="uk-card uk-card-default uk-card-body" >
                <div class="uk-text-center">
                    <div class="">
                        <h3 class="uk-text-uppercase text-center uk-text-bold">
                            iSchool<br/> Rest Api Request Simulation.
                        </h3>
                    </div>

                    <form action="<?=route('api.validate.paycode')?>" method="post" class="row">
                        <?=csrf_field()?>
                        <input type="hidden" name="developer_id" value="043h884j">
                        <!--<div class="col-sm-12">-->
                        <!--	<div class="form-group">-->
                        <!--		<select name="request" id="" class="uk-select">-->
                        <!--			<option value="">Select request...</option>-->
                        <!--			<option value="get-student">Get Student By Matric No</option>-->
                        <!--			<option value="get-studens">Get Students</option>-->
                        <!--		</select>-->
                        <!--	</div>-->
                        <!--</div>-->
                        <!--<div class="col-sm-10">-->
                        <!--	<div class="form-group">-->
                        <!--		<input type="text" name="key" value="" class="uk-input" placeholder="Api Key..">-->
                        <!--	</div>-->
                        <!--	<span class="help-block"><i>Enter Api Key provided to you by our Team</i></span>-->
                        <!--</div>-->
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" name="paycode" value="<?=old('paycode')?>" class="uk-input" placeholder="Enter paycode...">
                                <!--									<span class="help-block"><i>Change <b>SON</b> in the matric number to SOM, CHT, SCHOOL for different results</i></span>-->
                                <span class="help-block">
                                        Enter generated paycode to validate
                                    </span>
                            </div>
                        </div>
                        <p>
                            <button type="submit" class="uk-button palette-Green bg" style="color: #fff">Submit</button>
                        </p>
                        <small class="text-muted version"><i>Version 1.0.0</i></small>
                    </form>
                </div>
            </div>
        </div>
        </body>
        </html>

        <?php
    }
}
