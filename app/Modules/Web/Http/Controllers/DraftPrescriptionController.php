<?php
namespace App\Modules\Web\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\DraftMedicine;
use App\Modules\Web\Models\ClientMaster;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DraftPrescriptionController extends Controller
{

    public function getDraftToken(Request $request)
    {

        if ($request->isMethod("POST")) {
            $validator = Validator::make($request->all(), [
                'client_id' => 'required',
                'client_secret_key' => 'required',
                'client_name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'type' => "error",
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                try {
                    $client_credential = ClientMaster::query()
                        ->where('client_id', $request->client_id)
                        ->where('client_secret_key', $request->client_secret_key)
                        ->where('client_name', $request->client_name)
                        ->first();
                    if (!empty($client_credential)) {
                        if ($client_credential->status === 1) {
                            $tokenUrl = env('API_URL') . "/api/getToken";
                            $credential = [
                                'clientid' => env('CLIENT_ID'),
                                'username' => env('CLIENT_USER_NAME'),
                                'password' => env('CLIENT_PASSWORD')
                            ];

                            $data = CommonFunction::getApiToken($tokenUrl, $credential);
                            return response()->json([
                                'message' => "Success!",
                                'token' => $data,
                                'type' => "success",
                                'status' => Response::HTTP_OK,
                            ], Response::HTTP_OK);
                        } else {
                            return response()->json([
                                'message' => "Client information is inactive",
                                'type' => "warning",
                                'status' => Response::HTTP_NOT_FOUND,
                            ], Response::HTTP_NOT_FOUND);
                        }

                    } else {
                        return response()->json([
                            'message' => "Invalid client credential",
                            'type' => "error",
                            'status' => Response::HTTP_NOT_FOUND,
                        ], Response::HTTP_NOT_FOUND);
                    }
                } catch (QueryException $exception) {
                    return response()->json([
                        'message' => $exception->getMessage(),
                        'type' => "error",
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }

    }
    public function storeDraftPescription(Request $request){
        if ($request->isMethod("POST")) {

            $validator = Validator::make($request->all(), [
                'image' => 'required',
                'company_working_id' => 'required',
                'pid'=>'nullable'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'type' => "error",
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                try {
                    $token = $request->header('APIAuthorization');
                    if($token == null || $token == ''){
                        return response()->json([
                            'message' => "Please ensure token in header",
                            'type' => "error",
                            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }else {
                        $response = CommonFunction::checkTokenVerification($request->headers->all());
                        $token_verify = $response->getData()->data;
                        if ($token_verify) {
                            $base64Image = $request->input('image');
                            $ObjectURL = CommonFunction::getMinioUploadedFileURL($base64Image);
                            if (!$ObjectURL) {
                                return response()->json([
                                    'message' => "Image validation is failed",
                                    'type' => "error",
                                    'status' => Response::HTTP_BAD_REQUEST,
                                ], Response::HTTP_BAD_REQUEST);
                            }
                            $hajjSessionId = HajjSessions::where(['state' => 'active'])->value('id');
                            DB::beginTransaction();

                            $insert = DraftMedicine::create([
                                'pharmacy_id' => $request->company_working_id,
                                'is_draft' => 4,
                                'pid' => $request->pid,
                                'session_id' => $hajjSessionId,
                                'image_url' => $ObjectURL,
                                'created_at' => Carbon::now(),
                                'created_by' => 0,
                                'updated_at' => Carbon::now(),
                                'updated_by' => 0
                            ]);
                            DB::commit();
                            return response()->json([
                                'message' => "Prescription drafted successfully",
                                'type' => "success",
                                'status' => Response::HTTP_OK,
                            ], Response::HTTP_OK);
                        } else {
                            return response()->json([
                                'message' => "Invalid token",
                                'type' => "error",
                                'status' => Response::HTTP_BAD_REQUEST,
                            ], Response::HTTP_BAD_REQUEST);
                        }
                    }
                } catch (QueryException $exception) {
                    DB::rollBack();
                    return response()->json([
                        'message' => $exception->getMessage(),
                        'type' => "error",
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }
    }


    public function storeDraftPescriptionv2(Request $request){
        if ($request->isMethod("POST")) {

            $validator = Validator::make($request->all(), [
                'image' => 'required',
                'company_working_id' => 'required',
                'pid'=>'nullable'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'type' => "error",
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                try {
                    $token = $request->header('APIAuthorization');
                    if($token == null || $token == ''){
                        return response()->json([
                            'message' => "Please ensure token in header",
                            'type' => "error",
                            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }else {
                        $response = CommonFunction::checkTokenVerification($request->headers->all());
                        $token_verify = $response->getData()->data;
                        if ($token_verify) {

                            $hajjSessionId = HajjSessions::where(['state' => 'active'])->value('id');
                            DB::beginTransaction();

                            $insert = DraftMedicine::create([
                                'pharmacy_id' => $request->company_working_id,
                                'is_draft' => 4,
                                'pid' => $request->pid,
                                'session_id' => $hajjSessionId,
                                'image_url' => $request->image,
                                'created_at' => Carbon::now(),
                                'created_by' => 0,
                                'updated_at' => Carbon::now(),
                                'updated_by' => 0
                            ]);
                            DB::commit();
                            return response()->json([
                                'message' => "Prescription drafted successfully",
                                'type' => "success",
                                'status' => Response::HTTP_OK,
                            ], Response::HTTP_OK);
                        } else {
                            return response()->json([
                                'message' => "Invalid token",
                                'type' => "error",
                                'status' => Response::HTTP_BAD_REQUEST,
                            ], Response::HTTP_BAD_REQUEST);
                        }
                    }
                } catch (QueryException $exception) {
                    DB::rollBack();
                    return response()->json([
                        'message' => $exception->getMessage(),
                        'type' => "error",
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }
    }


}
