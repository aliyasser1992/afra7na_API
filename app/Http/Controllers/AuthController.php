<?php

namespace App\Http\Controllers;

use App\Model\country;
use App\Model\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Response;
use Storage;
use Validator;
//use Twilio\Rest\Client;
use Unifonic;
use GuzzleHttp\Client;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'check_code', 'resendCode', 'send_code_reset_password', 'reset_password']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function SendSMSWithVcode($phone, $vcode)
    {


        $phone = substr($phone, 1);

        $http = new Client([
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        try {
            $response = $http->post("http://basic.unifonic.com/rest/SMS/messages?AppSid=4JlNv1EgSRL4IPClSB9qm7SmL7ptMe&SenderID=Afrahna&Body=$vcode&Recipient=$phone&responseType=JSON&CorrelationID=%22%22&baseEncode=true&statusCallback=sent&async=false");

        } catch (\Exception $exception) {
            throw new  $exception;
        }


        return $response;


    }

    public function register(Request $request)
    {
        $verification_code = $request['verification_code'] = rand(1000, 10000);
        $credentials = $request->only('name', 'phone', 'password', 'verification_code', 'region_id', 'country_id');
        $input = Request()->all();

        $input['phone'] = country::where('id', $input['country_id'])->value('code') . $input['phone'];
//        $checkVerify = User::where('phone', $input['phone'])->where('state', 0)->get();
//        if (count($checkVerify) > 0) {
//            User::where('phone', $input['phone'])->delete();
//        }

        $rules = [
            'phone' => 'required',
//            'name' => 'required|String',
            //    'region_id' => 'required|Integer',
            'country_id' => 'required|Integer',
            'password' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        $input['password'] = Hash::make($request['password']);

        $input['state'] = 0;

        $output = User::updateOrCreate(['phone' => $input['phone']], $input);

        $message = "    رمز التفعيل الخاص بك هو    $verification_code  ";
        $this->SendSMSWithVcode($input['phone'], $message);
//        return Response()->json(['success' => true ,'code'=> 202]);

        return response()->json(['success' => true, 'message' => 'تم تسجيل بنجاح انتظر كود التفعيل'], 202);

    }


    public function resendCode(Request $request)
    {
        $verification_code = $request['verification_code'] = rand(1000, 10000);
        $credentials = $request->only('phone', 'verification_code');
        $user = User::where('phone', $request->phone)->where('state', 0)->count();
        if ($user > 0) {
            User::where('phone', $request->phone)->update(['verification_code' => $verification_code]);
//            return $this->SendSMSWithrand($request->phone, $verification_code);
            $message = "    رمز التفعيل الخاص بك هو    $verification_code  ";

            return $this->SendSMSWithVcode($request->phone, $message);


        } else {
            return Response()->json(['error' => array('user' => ['unauthorized'])], 401);
        }


    }


    public function SendSMSWithrand($phone, $verification_code)
    {

        $msg = "افراحنا+-+كود+التفعيل+الخاص+بك:+$verification_code";
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );
        $url = "http://www.kwtsms.com/API/send/?username=afrahna&password=9866504&sender=96566880003&mobile=$phone&lang=3&message=$msg";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
//            return Response()->json(['error' => array('message' => [curl_error($ch)])], 401);
        }
        if (substr($response, 0, 3) != 'ERR') {
//            return Response::json($response);
        } else {
//            return Response()->json(['error' => array('message' => [$response])], 401);

        }


        \Log::info($response);
        curl_close($ch);


    }

    private function sendMessage($message, $recipients)
    {
//        return $recipients;
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_AUTH_TOKEN");
        $twilio_number = env("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients,
            ['from' => $twilio_number, 'body' => $message]);
    }

    public function check_code(Request $request)
    {
        $input = Request()->all();
        $rules = [
            'phone' => 'required',
            'password' => 'required',
            'verification_code' => 'required'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        $user = User::where('phone', $input['phone'])->first();
        if ($input['verification_code'] == $user['verification_code']) {
            User::where('phone', $input['phone'])->update(['state' => 1]);
            $credentials = $request->only('phone', 'password');
            if ($token = JWTAuth::attempt($credentials)) {
                return $this->respondWithToken($token);
            } else {
                return response()->json(['success' => false, 'error' => array('user' => ['unauthorized'])], 401);
            }
        } else {
            return response()->json(['success' => false, 'error' => array('user' => ['Verification code you entered not valid'])], 401);
        }


    }


    public function login(Request $request)
    {

        $credentials = $request->only('phone', 'password');

        $rules = [
            'phone' => 'required|exists:users',
            'password' => 'required'
        ];

        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {

            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        $user = User::where('phone', $request->phone)->first();

        if ($user != null) {
//            if ($user['id'] == 48) {
////                return 'a7a';
//                $verification_code = "1111";
//            } else {
////                return 'sha7raa';
//                $verification_code = rand(1000, 10000);
//            }
//            $verification_code = "1111";
//return $user->state;
            if ($user->state == 0) {
//                return 2;
//                $user->update(['state' => 0, 'verification_code' => $verification_code]);
//                return $this->SendSMSWithrand($request->phone, $verification_code);
//                User::where('phone', $request->phone)->delete();
                return response()->json(['success' => false, 'error' => array('message' => ['this account not verify yet'])], 401);
            } else {
                if ($token = JWTAuth::attempt($credentials)) {
//                return 3;
                    $token = JWTAuth::fromUser($user);
                    return $this->respondWithToken($token);
                } else {
                    return response()->json(['success' => false, 'error' => array('message' => ['unauthorized'])], 401);

                }
            }
        } else {

            return response()->json(['success' => false, 'error' => array('message' => ['unauthorized'])], 401);

        }

    }

    public function update_profile(Request $request)
    {
        $input = Request()->all();
        $rules = [
            'name' => 'String',
            // 'region_id' => 'Integer',
            'country_id' => 'Integer',
        ];
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        $user = User::where('id', Auth::id())->first();
//        $user = User::where('id', auth()->user()->id)->first();
        $user->update($input);
        return ['state' => 202];


    }


    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Instate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }


    public function send_code_reset_password(Request $request)
    {
        $input = Request()->all();
        $rules = [
            'phone' => 'required|exists:users',
        ];
//        $input['phone'] = country::where('id', $input['country_id'])->value('code') . $input['phone'];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }
        $verification_code = rand('1000', '10000');
        User::where('phone', $input['phone'])->update(['verification_code' => $verification_code]);
        $message = "    رمز التفعيل الخاص بك هو    $verification_code  ";
        $this->SendSMSWithVcode($request->phone, $message);
        return response()->json(['success' => true, 'message' => ['send success']], 202);
    }


    public function reset_password()
    {
        $input = Request()->all();
        $rules = [
            'phone' => 'required|exists:users',
            'verification_code' => 'required',
            'new_password' => 'required'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }
        $user = User::where('phone', $input['phone'])->first();
        if ($user['verification_code'] == $input['verification_code']) {
            $user->update(['password' => Hash::make($input['new_password']), 'state' => 1]);
            return ['state' => 202];
        } else {
            return response()->json(['success' => false, 'error' => array('user' => ['Verification code you entered not valid'])], 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
