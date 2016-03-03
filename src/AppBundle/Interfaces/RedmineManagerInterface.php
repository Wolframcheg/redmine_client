<?php

namespace AppBundle\Interfaces;

interface RedmineManagerInterface
{
    public function getProjects();

    public function getProjectWithIssues($identifier, $page);

    public function getProject($identifier);

    public function checkState($code);

    public function getActivitiesPairs();

    public function trackTime($data);

}