<?php
namespace AppBundle\Services;

use AppBundle\Entity\User;
use Redmine\Client;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


/**
 * Class RedmineManager
 * @package AppBundle\Services
 */
class RedmineManager
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

    public function getProject($id, $page)
    {
        $project = $this->redmineApiClient->api('project')->show($id);
        $this->checkState();
        $issues = $this->getIssuesByProjectId($project['project']['identifier'], $page);
        $this->checkState();
        $countPages = $this->getPagination($issues['total_count'], $this->pageLimit);
        if($page > $countPages || $page < 1)
            $this->checkState(404);

        return [
            'project' => $project['project'],
            'issues' => $issues['issues'],
            'countPages' => $countPages
        ];
    }

    private function getIssuesByProjectId($id, $page)
    {
        $offset = ($page-1)*$this->pageLimit;
        return $this->redmineApiClient->api('issue')->all([
            'project_id' => $id,
            'offset' => $offset,
            'limit' => $this->pageLimit,
        ]);
    }

    private function getPagination($all, $limit)
    {
        return (int)ceil($all/$limit);
    }



    public function checkState($code = null)
    {
        if(!$code)
            $code = $this->redmineApiClient->getResponseCode();
        switch ($code) {
            case 401:
                throw new HttpException(401,'Invalid cridentionals, Please check parameters');
                break;
            case 403:
                throw new HttpException(403,'Access denied');
                break;
            case 404:
                throw new HttpException(404,'Page not Found');
                break;
            default: ;

        }

    }

}