<?php

namespace GomaGaming\Logs\Http\Controllers;
ini_set('memory_limit', '-1');

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

use GomaGaming\Logs\Lib\JiraApi;

use GomaGaming\Logs\Models\Log;
use GomaGaming\Logs\Models\LogException;

class GomaGamingApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getLogs(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => Log::getFilteredLogs(request()->all())
        ], 200);
    }

    public function getLog($logId): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => Log::getLogInfo(request()->all(), $logId)
        ], 200);
    }

    public function getLogServices(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => Log::getLogServices()
        ], 200);
    }

    public function getLogExceptions(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => LogException::getFilteredLogExceptions(request()->all())
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
            'data' => LogException::getPaginatedLogsByException($logExceptionId, request()->all())
        ], 200);
    }

    public function postLogExceptionAssignee(JiraApi $jiraApi, $logExceptionId): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => LogException::assignLogException($jiraApi, $logExceptionId, request()->all())
        ], 200);
    }

    public function postLogExceptionArchive(JiraApi $jiraApi): JsonResponse
    {
        LogException::archiveLogException($jiraApi, request()->logExceptionsIds);

        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => []
        ], 200);   
    }

}
