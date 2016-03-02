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


    /**
     * @param Client $redmineApiClient
     */
    public function __construct(Client $redmineApiClient)
    {
        $this->redmineApiClient = $redmineApiClient;
    }

    public function getProjects()
    {
        $result = $this->redmineApiClient->api('project')->all();
        $this->checkState();

        return $result['projects'];
    }

    public function getProject($id)
    {
        $result = $this->redmineApiClient->api('project')->show($id);
        $this->checkState();

        return $result['project'];
    }

    public function getIssuesByProjectId($id)
    {
        $result = $this->redmineApiClient->api('issue')->all(array('project_id' => $id));
        var_dump($result);exit();
    }

    public function checkState()
    {
        $code = $this->redmineApiClient->getResponseCode();
        switch ($code) {
            case 401:
                throw new HttpException(401,'Invalid cridentionals, Please check parameters');
                break;
            case 403:
                throw new HttpException(403,'Access denied');
                break;
            default: ;

        }

    }

}