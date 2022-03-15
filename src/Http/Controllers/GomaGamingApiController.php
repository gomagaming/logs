<?php

namespace GomaGaming\Logs\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use GomaGaming\Logs\Models\Log;
use GomaGaming\Logs\Models\LogException;
use GomaGaming\Logs\Models\LogMetaData;

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
        $logException = LogException::find($logExceptionId);

        return response()->json([
            'status' => $logException ? 'success' : 'error',
            'message' => $logException ? 'Success' : 'LogException not found!',
            'data' => $logException ? $logException : collect()->paginate(10)
        ], $logException ? 200 : 404);
    }

    public function getLogsByException($logExceptionId): JsonResponse
    {
        $logExceptionLogs = LogException::getPaginatedLogsByException($logExceptionId);

        return response()->json([
            'status' => $logExceptionLogs ? 'success' : 'error',
            'message' => $logExceptionLogs ? 'Success' : 'LogException not found!',
            'data' => $logExceptionLogs ? $logExceptionLogs : collect()->paginate(10)
        ], $logExceptionLogs ? 200 : 404);
    }
}