<?php

namespace GomaGaming\Logs\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use GomaGaming\Logs\Models\LogException;

class GomaGamingApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getLogExceptions(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => LogException::getFilteredLogExceptions($this->request->all())
        ], 200);
    }

    public function getLogException($logExceptionId): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => LogException::find($logExceptionId)
        ], 200);
    }

    public function getLogsByException($logExceptionId): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => LogException::getPaginatedLogsByException($logExceptionId, $this->request->all())
        ], 200);
    }
}
