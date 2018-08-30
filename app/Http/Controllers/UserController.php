<?php

namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function RegisterUser(Request $request){

        $user = new User();

        $user->mobile_no = $request->input('mobile_no');

        $string = str_random(15);
        $user->name = $string;

        $user->save();
        $url = 'https://widget.ideabiz.lk/web/reg/initiate/TutoHub?request-ref='.$string;

        return redirect($url);
    }

    public function getHome(){

        return view("home");
    }

    public function getCallback(Request $request){

        //Don't need to Change
        $target1 = "https://ideabiz.lk/apicall/token?grant_type=password&username=Dilanka&password=dilanka@sanjaya1997&scope=PRODUCTION";
        //Checking the Subscription Status
        $target2 = "https://ideabiz.lk/apicall/widget/pin/subscription/v1/status/";
        //Payment API link
        $target3 = "https://ideabiz.lk/apicall/payment/v4/";


        $ref = $request->input('ref');
        $data[] = explode('?', $ref);
        $req_ref_array = explode('=', $data[0][1]);
        $req_ref = $req_ref_array[1];
      //  return response($data[0][0]." ".$req_ref);

        $client = new Client([
            'base_uri' => '',
            'timeout' => 10.0,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic alo1RUJoOFR5Y1hTUnM5MjdkeU9pazVTUWpjYTppQU9BRmhmSVFkOFliRHN3c3dwS3Y1dFhsbmNh'
            ],]);

        $response = $client->post($target1, []);

        $array = $response->getBody()->getContents();
        $json = json_decode($array);
        //$collection = collect($json);

        $access = $json->access_token;
        //$access = $collection->get('access_token');

        $client2 = new Client([
            'base_uri' => '',
            'timeout' => 10.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$access
            ],]);

        $res = $client2->get($target2.$req_ref);

        $array2 = $res->getBody()->getContents();
        $json2 = json_decode($array2);
        //$collection = collect($json);

        $msisdn = $json2->msisdn;
        $subscription = $json2->status;

        if($subscription == 'SUBSCRIBED'){

            $client3 = new Client([
                'base_uri' => '',
                'timeout' => 10.0,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$access,
                    'Accept' => 'application/json'
                ],]);

            $respo = $client3->request('POST',$target3.$msisdn."/transactions/amount", [

                'json' => [
                'amountTransaction' => [
                    'endUserId' => 'tel:+'.$msisdn,
                    'paymentAmount' => [
                        'chargingInformation' => [
                            'amount' => 50,
                            'currency' => 'LKR',
                            'description' => 'Charge for TutoHub'
                        ]
                    ],
                    'referenceCode' => 'Add Some Ref to Identify the Transactiom'
            ]
            ]]);

            $status_code = $respo->getStatusCode();

            if($status_code == 201){
                //Redirect to the Home Page after Login
                return response("Success");
            }else{
                //Show Error
                return response("Error");
            }




        }elseif ($subscription == 'ALREADY_SUBSCRIBED'){

            //Redirect to the Home Page after Login
            return response("Already");

        }





    }
}
