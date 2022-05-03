<?php

namespace GomaGaming\Logs\Lib;

class JiraApi extends HttpApi
{
    private function generateApiToken($params = [])
    {
        return $this->newRequest('GET', 'issue', $params);
    }

    public function createIssue($params = [])
    {
        return $this->newRequest('POST', 'issue', $params);
    }

    public function updateIssueAssignee($issueKey, $params = [])
    {
        return $this->newRequest('PUT', 'issue/' . $issueKey . '/assignee', $params);
    }

    public function updateIssueStatus($issueKey, $params = [])
    {
        return $this->newRequest('POST', 'issue/' . $issueKey . '/transitions', $params);
    }

}
