<?php 

namespace App\Http\Library;

Class Responses
{
	public static function sendResponse($data, $message)
	{
		$response['status']  = 'Success';
		$response['message'] = $message;
		$response['data']    = $data;

		return response()->json($response, 200);
	}

	public static function sendError($errorMessage = [], $message)
	{
		$response['status']  = 'Fail';
		$response['message'] = $message;
		
		if (!empty($errorMessage)) {
			$response['data'] = $errorMessage;
		}

		return response()->json($response, 404);
	}
}