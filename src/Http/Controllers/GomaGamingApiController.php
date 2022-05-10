<?php

namespace GomaGaming\Logs\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

use GomaGaming\Logs\Lib\JiraApi;

use GomaGaming\Logs\Models\LogException;

class GomaGamingApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
