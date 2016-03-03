<?php
namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Interfaces\RedmineManagerInterface;
use Redmine\Client;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


/**
 * Class RedmineManager
 * @package AppBundle\Services
 */
class RedmineManager implements RedmineManagerInterface
{
    /**
     * @var RegistryInterface
     */
    private $redmineApiClient;

    private $pageLimit;


    /**
     * @param Client $redmineApiClient
     * @param int $pageLimit
     */
    public function __construct(Client $redmineApiClient, $pageLimit = 50)
    {
        $this->redmineApiClient = $redmineApiClient;
        $this->pageLimit = $pageLimit;
    }

    public function getProjects()
    {
        $result = $this->redmineApiClient->api('project')->all();
        $this->checkState();

        return $result['projects'];
    }

    public function getProjectWithIssues($identifier, $page)
    {
        $project = $this->getProject($identifier);
        $issues = $this->getIssuesByProjectId($project['identifier'], $page);
        $this->checkState();
        $countPages = $this->getPagination($issues['total_count'], $this->pageLimit);
        if ($page > $countPages || $page < 1)
            $this->checkState(404);

        return [
            'project' => $project,
            'issues' => $issues['issues'],
            'countPages' => $countPages
        ];
    }

    public function getProject($identifier)
    {
        $project = $this->redmineApiClient->api('project')->show($identifier);
        $this->checkState();
        return $project['project'];
    }

    private function getIssuesByProjectId($identifier, $page)
    {
        $offset = ($page - 1) * $this->pageLimit;
        return $this->redmineApiClient->api('issue')->all([
            'project_id' => $identifier,
            'offset' => $offset,
            'limit' => $this->pageLimit,
        ]);
    }

    private function getPagination($all, $limit)
    {
        return (int)ceil($all / $limit);
    }

    public function getActivities()
    {
        $activities = $this->redmineApiClient->api('time_entry_activity')->all();
        $this->checkState();
        return $activities['time_entry_activities'];
    }

    public function getActivitiesPairs()
    {
        $activities = $this->getActivities();
        $pairs = [];
        foreach ($activities as $activity) {
            $pairs[$activity['name']] = $activity['id'];
        }
        return $pairs;
    }

    public function trackTime($data)
    {
        $response = $this->redmineApiClient->api('time_entry')->create($data);
        $this->checkState();
        return $response;
    }


    public function checkState($code = null)
    {
        if (!$code)
            $code = $this->redmineApiClient->getResponseCode();
        switch ($code) {
            case 401:
                throw new HttpException(401, 'Invalid cridentionals, Please check parameters');
                break;
            case 403:
                throw new HttpException(403, 'Access denied');
                break;
            case 404:
                throw new HttpException(404, 'Page not Found');
                break;
            default:
                ;

        }

    }

}