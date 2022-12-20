<?php

namespace Report\Interactions\Service;

use Savve\Stdlib;
use Savve\Doctrine\Service\AbstractService;
use TinCan;

class ReportService extends AbstractService
{
    /**
     * Create an interactions report
     *
     * @param array $filter Array of filters to use to generate an interactions report
     * @return array $report Array containing the data of the interactions report
     */
    public function report (array $filter)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        // create query
        $dql = "SELECT 
            employment.employmentId AS employment_id,
            CONCAT(learner.firstName, ' ', learner.lastName) AS fullname,
            learner.email,
            activity.title,
            trackerInteractions.identifier,
            SUBSTRING(trackerInteractions.studentResponse, LOCATE('[:]', trackerInteractions.studentResponse) + 3) AS response,
            trackerInteractions.result,
            trackerInteractions.lastAccessed AS last_accessed
        FROM Savvecentral\Entity\TrackerInteractions trackerInteractions
        LEFT JOIN trackerInteractions.trackerItem trackerItem
        LEFT JOIN trackerItem.distribution distribution
        LEFT JOIN distribution.activity activity
        LEFT JOIN distribution.learner learner
        LEFT JOIN learner.employment employment
        LEFT JOIN learner.site site
        LEFT JOIN learner.groupLearners groupLearners
        LEFT JOIN groupLearners.group groups
        WHERE
            site.siteId = :site_id AND
            trackerInteractions.lastAccessed >= :generate_from AND
            trackerInteractions.lastAccessed <= :generate_to 
        GROUP BY trackerItem.id, trackerInteractions.identifier
        ORDER BY fullname, trackerInteractions.identifier";

        // execute query
        return $entityManager->createQuery($dql)
            ->setParameters($filter)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();
    }
    
    /**
     * Fetch xAPI activities for the Admin report generator
     *
     * @param $siteId
     * @return array
     */
    public function getAllActivities($siteId)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $dql = [];
        $dql[] = "SELECT distribution, activity, tincanItems

            FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN learner.site site
                LEFT JOIN activity.tincanItems tincanItems
                WHERE site.siteId = :siteId
                AND activity.activityType IN (:activityType)
                AND activity.status IN (:activityStatus)
                AND distribution.status IN (:distributionStatus)";

        // site.site_id
        $params['siteId'] = $siteId;

        //learning_activity.status
        $params['activityStatus'] = ['active','inactive','new']; //@todo : Should inactive come up ?

        // activcity type (tincan only)
        $params['activityType'] = ['tincan'];

        // distribution
        $params['distributionStatus'] = ['active','new','expired','approved'];

        // group by
        $dql[] = "GROUP BY activity.activityId";

        // order by
        if (isset($orderBy)) {
            $orderBy = implode(", ", $orderBy);
            $dql[] = sprintf("ORDER BY %s", $orderBy);
        }

        // execute query
        $dql = implode(' ', $dql);
        $results = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        return $results;

    }
    
    /**
     * Determine and return the LRS credentials
     *
     * @param $optionService
     * @return array
     */
    private function getLrsCredentials( $optionService ) {

        // get the site -  we need the LRS username and password from ther Site entity
        $site = $this->getServiceLocator()->get('Site\Entity');

        // return an array
        $arr = array();

        // $lrsUsername: use options default if site value is empty
        if (empty($site['lrs_username'])) {
            $arr['lrsUsername'] = $optionService->getLrsUser();
        } else {
            $arr['lrsUsername'] = $site['lrs_username'];
        }

        // $lrsPassword: use options default if site value is empty
        if (empty($site['lrs_password'])) {
            $arr['lrsPassword'] = $optionService->getLrsPassword();
        } else {
            $arr['lrsPassword'] = $site['lrs_password'];
        }

        // $lrsUrl: use options default if site value is empty
        if (empty($site['lrs_url'])) {
            $arr['lrsUrl'] = $optionService->getLrsUrl();
        } else {
            $arr['lrsUrl'] = $site['lrs_url'];
        }

        return $arr;
    }
    

    private function __getMoreStatements($lrs, $response)
    {
        $statements = array();
        if (!empty($response)) {
            if (isset($response->content) && !is_string($response->content)) {
                $statements = $response->content->getStatements();
            }
            while(!empty($response->content) && !is_string($response->content) && !empty($response->content->getMore())) {
                $response = $lrs->moreStatements($response->content->getMore());
                if (isset($response->content) && !is_string($response->content)) {
                    $statements = array_merge($statements, $response->content->getStatements());
                }
            }
        }
        return $statements;
    }
    
    /**
     * Pad a string (integer) with leading zeros
     *
     * @param $input
     * @param $num
     * @return string
     */
    private function padZero($input, $num) {
        for ($i = 0; $i < $num; $i++) {
            if (strlen($input) == $num) { break; }
            $input = '0'.$input;
        }
        return $input;
    }

    /**
     * Convert a PT time to a visual hours:minutes:seconds output (0000:00:00)
     *
     * @param $input
     * @return string
     */
    private function extractTimeFromPTTime($input) {
        $str = str_replace("PT", "", $input);
        $arr = explode("M", $str);
        $minutes = $arr[0];
        $seconds = str_replace("S", "", $arr[1]);

        $hours = '0';
        if ($minutes >= 60) {
            $hours   = floor($minutes / 60);
            $minutes = $minutes - ($hours * 60);
        }
        $output = $this->padZero($hours, 4) . ':' . $this->padZero($minutes, 2) . ':' . $this->padZero($seconds, 2);
        return $output;
    }
    
    public function generateReport($activities, $learners, $optionService)
    {
        $interactionsReport = array();
        $maxChoices = 0;
        
        $lrsCredentials = $this->getLrsCredentials( $optionService );
        
        // intitalise the LRS
        $lrs = new TinCan\RemoteLRS(
            $lrsCredentials['lrsUrl'],
            '1.0.1',
            $lrsCredentials['lrsUsername'],
            $lrsCredentials['lrsPassword']
        );
        // we are looking for a 'scored' statements
        $verb = new TinCan\Verb(
            [ 'id' => 'http://adlnet.gov/expapi/verbs/scored' ]
        );
        $queryParams = [
            'verb' => $verb,
            'related_activities' => true
        ];
        $activityReports = array();
        foreach($activities as $activity) {
            $tcActivity = new TinCan\Activity(
                //[ 'id' => $activity['tincanItems_itemIri'] ]
                [ 'id' => 'http://' . $this->generateUUID($activity['activity_activityId']) ]
            );
            $queryParams['activity'] = $tcActivity;
            $activityReports +=  $this->convertReportToArray($activity['activity_title'], $activity['activity_version'], $activity['tincanItems_itemIri'], $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams)));           
        }
        if (!empty($activityReports)) {
            foreach($learners as $learner) {
                $learnerEmail = (isset($learner->email) && strpos($learner->email, "@") !== false) ? $learner->email : $learner->userId . "@savv-e.com.au";
                if (!empty($activityReports[$learnerEmail])) {
                    $learnerInfo = array();
                    $learnerInfo['learnerId'] = $learner->userId;
                    $learnerInfo['employeeId'] = $learner->employment->employmentId;
                    $learnerInfo['learnerName'] = $learner->firstName . ' ' . $learner->lastName;
                    $learnerInfo['location'] = $learner->employment->location;
                    $learnerInfo['position'] = $learner->employment->position;
                    $learnerInfo['employmentType'] = $learner->employment->employmentType;
                    $learnerInfo['costCentre'] = $learner->employment->costCentre;
                    if (!empty($learner->groups)) {
                        $groups = array();
                        foreach($learner->groups as $group) {
                            $groups[] = $group->name;
                        }
                        $learnerInfo['groups'] = implode(' | ', $groups);
                    } else {
                        $learnerInfo['groups'] = '';
                    }
                    foreach($activityReports[$learnerEmail] as $activityReport) {
                        $activityReport += $learnerInfo;
                        $choiceCount = count($activityReport['activityQuestionChoices']);
                        if ($choiceCount > $maxChoices) {
                            $maxChoices = $choiceCount;
                        }
                        $interactionsReport[] = $activityReport;
                    }
                }
            }
        }
        return array(
            'interactions' => $interactionsReport,
            'maxChoices' => $maxChoices
        );
    }
    
    public function convertReportToArray($activityTitle, $activityVersion, $activityIri, $statements)
    {
        $outputs = array();
        if (!empty($statements)) {
            foreach ($statements as $statement) {
                $actorObj = $statement->getActor();
                if (!empty($actorObj->getAccount())) {
                    $actor = $statement->getActor()->getAccount()->getName();
                } else {
                    $actor = $actorObj->getMbox();
                }
                if (!empty($actor)) {
                    $output = array();
                    $output['activityTitle'] = $activityTitle;
                    $output['activityVersion'] = $activityVersion;
                    $output['activityIri'] = $activityIri;
                    $groupingId = $statement->getContext()->getContextActivities()->getGrouping()[0]->getId();
                    $output['activityGroupIri'] = isset($groupingId) && $groupingId != null ? $groupingId : '';

                    $verb = $statement->getVerb()->getDisplay()->asVersion('en-US');
                    $output['activityVerb'] = Stdlib\StringUtils::capitalise($verb['en-US']);

                    $output['activityActor'] = $actor;

                    $timestamp = $statement->getTimestamp();
                    $output['activityTimestamp'] = isset($timestamp) ? date("Y-m-d h:i:s", strtotime($timestamp)) : '';

                    $page = $statement->getTarget()->getDefinition()->getName()->asVersion('en-US');
                    $output['activityDefinitionName'] = $page['en-US'];

                    $desc = $statement->getTarget()->getDefinition()->getDescription()->asVersion('en-US');
                    $output['activityDefinitionDescription'] = $desc['en-US'];

                    $id   = $statement->getTarget()->getId();
                    $output['assessment'] = strpos(strtolower($id), "assessment") ? 'Y' : 'N';
                    
                    $questions = array();
                    if (!empty($statement->getResult())) {
                        $group = $statement->getResult()->getExtensions()->asVersion($groupingId);
                        $output['activityResponseGroup'] = isset($group[$groupingId]) ? $group[$groupingId] : '';

                        $duration = $statement->getResult()->getDuration();
                        $output['activityDuration'] = isset($duration) ? $this->extractTimeFromPTTime($duration) : '';

                        $score = $statement->getResult()->getScore();
                        if (isset($score)) {
                            if (is_object($score)) {
                                $output['activityScore'] = $score->getScaled();
                            } else if (is_array($score)) {
                                if (!empty($score)) {
                                    $output['activityScore'] = implode(",", $score);
                                }
                            } else {
                                $output['activityScore'] = $score;
                            }
                        }
                        if (!isset($output['activityScore'])){
                            $output['activityVerb'] = 'Not Scored';
                        }

                        $response = $statement->getResult()->getResponse();
                        if (!empty($response)) {
                            $questions = $this->getQuestions($response);
                        }
                    }

                    if (empty($outputs[$actor])) {
                        $outputs[$actor] = array();
                    }
                    if (!empty($questions)) {
                        foreach($questions as $question) {
                            $outputs[$actor][] = $output + $question;
                        }
                    } else {
                        $outputs[$actor][] = $output;
                    }
                }
            }
        }
        return $outputs;
    }

    private function getQuestionInfo($responses, $index, $count, $pattern)
    {
        $data = '';
        for($pos = $index; $pos < $count; $pos++) {
            $res = trim($responses[$pos]);
            if ($pos != $index && preg_match($pattern, $res) !== 0) {
                return [$pos, trim($data)];
            } else {
                $data .= $res . '\n';
            }
        }
        return [$pos, $data];
    }
    
    
    private function getQuestions($response)
    {
        $outputs = array();
        $output = array();
        $pos = 0;
        $responses = explode("\n", trim($response));
        $count = count($responses);
        while($pos < $count) {
            if (empty($output['activityQuestionType'])) {
                list($pos, $type) = $this->getQuestionInfo($responses, $pos, $count, "/QuestionTitle:/");
                $output['activityQuestionType'] = trim(trim($type), "\\n\"");
            } else if (empty($output['activityQuestionTitle'])) {
                list($pos, $title) = $this->getQuestionInfo($responses, $pos, $count, "/AnswerOption\:|Reply\:/");
                $title = str_replace("\\n", "\n", trim(trim(str_replace('QuestionTitle:', '', $title)), "\\n\""));
                $output['activityQuestionTitle'] = empty($title) ? "N/A" : $title;
            } else {
                list($pos, $options) = $this->getQuestionInfo($responses, $pos, $count, "/QuestionTitle\:|AnswerOption\:|Reply\:/");
                if (empty($output['activityQuestionChoices'])) {
                    $output['activityQuestionChoices'] = array();
                }
                $options = explode("---", trim(str_replace('----', '---', str_replace('Reply:', '', str_replace('AnswerOption:', '', $options)))));
                if (!empty($options)) {
                    $selected = !empty($options[1]) && strpos($options[1], "not selected") === false ? 'S' : '';
                    $answer = !empty($options[2]) ? (strpos($options[2], "incorrect") === false ? 'C' : 'I') : '';
                    $output['activityQuestionChoices'][] = array(
                        'option' => str_replace("\\n", "\n", trim($options[0], "\\n\"")),
                        'selected' => $selected,
                        'answer' => $answer  
                    );
                    if ($answer === "C" && $selected === "S") {
                        $output['activityAnsweredCorrect'] = 'C';
                    } else if ($answer === "I" && $selected === "S") {
                        $output['activityAnsweredCorrect'] = 'I';
                    }
                }
                if ($pos < $count && strpos($responses[$pos], "QuestionTitle:") !== false) {
                    $output['activityQuestionTitle'] = $output['activityQuestionTitle'] === 'N/A' ? '' : $output['activityQuestionTitle'];
                    $outputs[] = $output;
                    $output = array(
                        'activityQuestionType' => $output['activityQuestionType']
                    );
                }
            }
        }
        $output['activityQuestionTitle'] = $output['activityQuestionTitle'] === 'N/A' ? '' : $output['activityQuestionTitle'];
        $outputs[] = $output;
        return $outputs;
    }

        private function generateUUID($data)
        {
            $uniqueId = md5($data);
            return substr($uniqueId, 0, 8 ) .'-'.
                substr($uniqueId, 8, 4) .'-'.
                substr($uniqueId, 12, 4) .'-'.
                substr($uniqueId, 16, 4) .'-'.
                 substr($uniqueId, 20);
        }
}
