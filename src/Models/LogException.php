<?php

namespace GomaGaming\Logs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogException extends Model
{
    use HasFactory;

    protected $fillable = ['hash', 'hits', 'sent', 'status', 'message', 'exception', 'file', 'line', 'trace', 'env', 'service', 'assigned_to', 'jira_issue_key'];

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function scopeHash($query, $hash)
    {
        return $query->where('hash', $hash);
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function hasBeenSent()
    {
        return $this->sent;
    }

    public function isStatus($status)
    {
        return $this->status == $status;
    }

    public function reopen()
    {
        $this->status = 'pending';
        $this->save();

        return $this;
    }

    public function setSent()
    {
        $this->sent = 1;
        $this->save();

        return $this;
    }        

    public function incrementHits()
    {
        $this->hits++;
        $this->save();

        return $this;
    }

    public function updateTrace($trace)
    {
        $this->trace = $trace;
        $this->save();
    
        return $this;
    }

    public function findByHash($hash)
    {
        return self::hash($hash)->first();
    }

    public static function getPaginatedLogsByException($logExceptionId, $filters = [])
    {
        $logException = self::find($logExceptionId);

        if (!$logException){
            return;
        }

        $query = $logException->logs()->with('metadata');

        $query = $query->skip(($filters['page'] ?? 1) * 10);

        return $query->paginate(10);
    }

    public static function getFilteredLogExceptions($filters = [])
    {
        $query = self::query();

        $query = self::applyFilters($query, $filters['orders'] ?? [], 'orderBy');
    
        $query = self::applyFilters($query, $filters['filters'] ?? [], 'where');

        $query = $query->skip(($filters['page'] ?? 1) * 10);

        return $query->paginate(10);
    }

    private static function applyFilters($query = null, $filters = [], $queryClause = 'where')
    {
        foreach($filters as $filterKey => $filterValue)
        {
            $query = $query->$queryClause($filterKey, $filterValue);
        }

        return $query;
    }

    public static function assignLogException($jiraApi, $logExceptionId, $data)
    {
        $logException = self::find($logExceptionId);

        if (!$logException){
            return;
        }

        $data['user_id'] = $data['user_id'] == 'null' ? null : $data['user_id'];

        $logException->assigned_to = $data['user_id'];
        $logException->save();

        if (config('gomagaminglogs.jira.create_issues')) 
        {
            $issue = $logException->jira_issue_key
                ? 
                    (isset($jiraApi->getIssue($logException->jira_issue_key)['key']) 
                        ? $jiraApi->updateIssueAssignee($logException->jira_issue_key, ['accountId' => $data['user_id'] ? config('gomagaminglogs.jira.account_ids')[(int)$data['user_id'] - 1] : null])
                        : $jiraApi->createIssue(self::generateJiraIssueData($data))
                    )
                : $jiraApi->createIssue(self::generateJiraIssueData($data));
    
            if (isset($issue['key']))
            {
                $logException->jira_issue_key = $issue['key'];
                $logException->save();
            }
        }

        return $logException;
    }

    public static function archiveLogException($jiraApi, $logExceptionsIds): void
    {
        foreach ($logExceptionsIds as $logExceptionId) 
        {
            $logException = self::find($logExceptionId);
    
            if (!$logException){
                continue;
            }
    
            $logException->status = 'archived';
            $logException->save();
            
            if (config('gomagaminglogs.jira.create_issues') && $logException->jira_issue_key)
            {
                $jiraApi->updateIssueStatus($logException->jira_issue_key, ['transition' => ['id' => '31']]);
            }
        }

    }

    public static function generateJiraIssueData($data): array
    {
        $createJiraIssueData = self::getCreateJiraIssueDefaultStructure();

        $createJiraIssueData['fields']['summary'] .= $data['issue_message'];

        $createJiraIssueData['fields']['summary'] = 
            (strlen($createJiraIssueData['fields']['summary']) > 254) 
            ? substr($createJiraIssueData['fields']['summary'], 0, 251) . '...' 
            : $createJiraIssueData['fields']['summary'];
        
        $createJiraIssueData['fields']['summary'] = preg_replace('~[\r\n]+~', '', $createJiraIssueData['fields']['summary']);

        $createJiraIssueData['fields']['parent']['key'] = config('gomagaminglogs.jira.parent_issue');

        $createJiraIssueData['fields']['description']['content'][0]['content'][1]['text'] = $data['issue_crawler_link'];
        $createJiraIssueData['fields']['description']['content'][0]['content'][1]['marks'][0]['attrs']['href'] = $data['issue_crawler_link'];
        
        $createJiraIssueData['fields']['assignee']['id'] = config('gomagaminglogs.jira.account_ids')[(int)$data['user_id'] - 1];

        return $createJiraIssueData;
    }

    public static function getCreateJiraIssueDefaultStructure()
    {
        return [
            'fields' => [
                'summary' => '[Backend] - ',
                'parent' => [
                    'key' => '',
                ],
                'issuetype' => [
                    'id' => '10005', // sub-task
                ],
                'project' => [
                    'id' => '10000', // Sportsbook
                ],
                'description' => [
                    'type' => 'doc',
                    'version' => 1,
                    'content' => [
                        0 => [
                            'type' => 'paragraph',
                            'content' => [
                                0 => [
                                    'type' => 'text',
                                    'text' => 'See: ',
                                ],
                                1 => [
                                    'type' => 'text',
                                    'text' => 'https://sportsbook-crawler.gomagaming.com/logs/issues/71',
                                    'marks' => [
                                        0 => [
                                            'type' => 'link',
                                            'attrs' => [
                                                'href' => 'https://sportsbook-crawler.gomagaming.com/logs/issues/71'
                                            ]
                                        ]
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
                'labels' => [
                    0 => 'back-end',
                ],
                'duedate' => null,
                'assignee' => [
                    'id' => '',
                ],
            ],
        ];
    }
}
