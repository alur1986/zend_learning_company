<?php

namespace Report\XAPIInteractions\Service;

use Report\EventManager\Event;
use Report\Service\ReportService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Repository\AbstractRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use TinCan;

class ReportService extends AbstractService
{

    /**
     * Create the report
     *
     * @param array $filter Array of filters to use to generate the report
     * @param int $siteId Id of site
     * @return array $report Array containing the data of the report
     */
    public function report (array $filter, $siteId)
    {
        $filter['site_id'] = $siteId;
        $activities = $this->getActivitiesByFilter($filter);
        $learners = $this->getLearnersByFilter($filter);
        $results = $this->generateReport($filter, $activities, $learners);
    
        // trigger event listeners
        $eventManager = $this->getEventManager();
        $eventResults = $eventManager->trigger(new Event(Event::EVENT_REPORT_POST, $this, [ 'result' => $results ]), function  ($items) { return is_array($items) || $items instanceof \Traversable; });
        if ($eventResults->stopped()) {
            $results = $eventResults->last();
        }
        return $results;
    }

    /**
     * Get all activities based on filters
     *
     * @param array $filter Array of filters to use to generate the report
     * @return array $activities Array containing the data of the activity
     */    
    private function getActivitiesByFilter(array $filter)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $siteId = array_key_exists('site_id', $filter) ? $filter['site_id'] : null;
        $activityIds = array_key_exists('activityId', $filter) ? (array) $filter['activityId'] : null;
        $groupIds = array_key_exists('group_id', $filter) ? (array) $filter['group_id'] : null;
        $learnerIds = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : null;
        $learnerStatus = array_key_exists('learner_status', $filter) ? $filter['learner_status'] : null;

        // if there are no activity IDs selected, throw an error
        $params = [];

        // create the DQL
        $dql = [];
        $dql[] = "SELECT
                    activity.activityId AS activity_id,
                    activity.activityType AS activity_type,
                    activity.title AS activity_title,
                    activity.keywords AS activity_keyword,
                    activity.code AS activity_code,
                    activity.version AS activity_version,
                    activity.cpd AS activity_cpd,
                    activity.duration AS activity_duration,
                    activity.directCost AS activity_direct_cost,
                    activity.indirectCost AS activity_indirect_cost,
                    category.term AS activity_category,
                    tincanItems.itemIri as item_iri
                FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN activity.category category
                LEFT JOIN learner.site site
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups
                LEFT JOIN activity.tincanItems tincanItems
                WHERE site.siteId = :siteId AND activity.activityType = :activityType";

        $params['siteId'] = $siteId;
        $params['activityType'] = 'tincan';

        // activity status
        $dql[] = "AND activity.status IN (:activityStatus) ";
        $params['activityStatus'] = ['active','inactive','new'];

        // distribution status
        $dql[] = "AND distribution.status IN (:distributionStatus) ";
        $params['distributionStatus'] = [
            'new',
            'active',
            'approved',
            'inactive',
            'expired'
        ];

        // if activity IDs is provided, filter results by activity IDs
        if ($activityIds) {
            $dql[] = "AND activity.activityId IN (:activityId)";
            $params['activityId'] = $activityIds;
        }

        // if group IDs is provided, filter results by group IDs
        if ($groupIds) {
            $dql[] = "AND groups.groupId IN (:groupId)";
            $params['groupId'] = $groupIds;
        }

        // learner IDs
        if ($learnerIds) {
            $dql[] = "AND learner.userId IN (:learnerId) ";
            $params['learnerId'] = $learnerIds;
        }

        // if learner status is defined, filter results by learner status
        $dql[] = "AND learner.status IN (:learnerStatus)";
        // learner status
        if ($learnerStatus) {
            $params['learnerStatus'] = $learnerStatus;
        }
        else{
            $params['learnerStatus'] = ['active','inactive','new'];
        }

        // group by
        $dql[] = "GROUP BY activity.activityId";

        // execute query
        $dql = implode(' ', $dql);
        $activities = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();
        return $activities;
    }
    
    /**
     * Get all learners based on filters
     *
     * @param array $filter Array of filters to use to generate the report
     * @return array $learners Array containing the data of the learner
     */    
    private function getLearnersByFilter(array $filter)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $siteId = array_key_exists('site_id', $filter) ? $filter['site_id'] : null;
        $groupIds = array_key_exists('group_id', $filter) ? (array) $filter['group_id'] : null;
        $learnerIds = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : null;
        $learnerStatus = array_key_exists('learner_status', $filter) ? $filter['learner_status'] : null;

        // if there are no activity IDs selected, throw an error
        $params = [];

        // create the DQL
        $dql = [];
        $dql[] = "SELECT
                    learner.userId AS learner_id,
                    learner.firstName AS learner_first_name,
                    learner.lastName AS learner_last_name,
                    CONCAT(learner.firstName,' ',learner.lastName) AS learner_name,
                    learner.telephone AS learner_telephone,
                    learner.mobileNumber AS learner_mobile_number,
                    learner.email AS learner_email,
                    learner.streetAddress AS learner_street_address,
                    learner.suburb AS learner_suburb,
                    learner.postcode AS learner_postcode,
                    learner.state AS learner_state,
                    learner.country AS learner_country,
                    learner.gender AS learner_gender,
                    learner.cpdId AS learner_cpd_id,
                    learner.cpdNumber AS learner_cpd_number,
                    learner.status AS learner_status,
                    learner.agent AS agent_code,

                    employment.employmentId AS employment_id,
                    employment.employmentType AS employment_type,
                    employment.position AS employment_position,
                    employment.startDate AS employment_start_date,
                    employment.endDate AS employment_end_date,
                    employment.costCentre AS employment_cost_centre,
                    employment.manager AS employment_manager,
                    employment.location AS employment_location,
                    groups.name AS group_name
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.site site
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups
                WHERE site.siteId = :siteId";

        $params['siteId'] = $siteId;

        // if group IDs is provided, filter results by group IDs
        if ($groupIds) {
            $dql[] = "AND groups.groupId IN (:groupId)";
            $params['groupId'] = $groupIds;
        }

        // learner IDs
        if ($learnerIds) {
            $dql[] = "AND learner.userId IN (:learnerId) ";
            $params['learnerId'] = $learnerIds;
        }

        // if learner status is defined, filter results by learner status
        $dql[] = "AND learner.status IN (:learnerStatus)";
        // learner status
        if ($learnerStatus) {
            $params['learnerStatus'] = $learnerStatus;
        }
        else{
            $params['learnerStatus'] = ['active','inactive','new'];
        }

        // group by
        $dql[] = "GROUP BY learner.userId";

        // execute query
        $dql = implode(' ', $dql);
        $learners = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();
        return $learners;
    }
    
    /**
     * Get all tincan activities
     *
     * @param integer $siteId
     * @return mixed
     */
    public function findAllTincanActivities ($siteId)
    {
        $entityManager = $this->getEntityManager();
        $params = [];
        $dql = "SELECT
                activity.activityId AS value,
                activity.title AS label,
                activity.activityId AS activity_id,
                activity.title AS title,
                activity.description AS description,
                activity.activityType AS activity_type,
                activity.status AS status

                ,(SELECT COUNT(l1.userId)
                    FROM Savvecentral\Entity\Learner l1
                    LEFT JOIN l1.distribution d1
                    LEFT JOIN d1.activity a1
                    WHERE a1.activityId = activity.activityId AND d1.status NOT IN ('deleted','disapproved','enrolled')
                    AND l1.status IN ('new', 'active')) AS num_learners

                ,(SELECT COUNT(e2.eventId)
                    FROM Savvecentral\Entity\Event e2
                    LEFT JOIN e2.activity a2
                    WHERE a2.activityId = activity.activityId AND e2.status IN ('enabled')) AS num_events

                FROM Savvecentral\Entity\LearningActivity activity
                LEFT JOIN activity.site site
                LEFT JOIN activity.distribution distribution
                LEFT JOIN distribution.learner learner
                WHERE activity.status NOT IN ('deleted') AND
                      site.siteId = :siteId AND
                      activity.activityType = :activityType
                GROUP BY activity.activityId
                ORDER BY activity.title ASC";
        $params['siteId'] = $siteId;
        $params['activityType'] = 'tincan';
        $query = $entityManager->createQuery($dql)
            ->setParameters($params);
        $results = $query->getArrayResult();
        return $results ?  : [];
    }
   
    /**
     * Determine and return the LRS Client
     *
     * @return TinCan\RemoteLRS
     */
    private function getLrsClient() {

        // get the site -  we need the LRS username and password from ther Site entity
        $site = $this->getServiceLocator()->get('Site\Entity');
        $optionService = $this->getServiceLocator()->get('Tincan\Options');

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
        
        if (empty($site['lrs_version'])) {
            $arr['lrsVersion'] = '1.0.1';
        } else {
            $arr['lrsVersion'] = $site['lrs_version'];
        }

        return new TinCan\RemoteLRS(
            $arr['lrsUrl'],
            $arr['lrsVersion'],
            $arr['lrsUsername'],
            $arr['lrsPassword']
        );
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
    
    private function generateReport(array $filter, $activities, $learners)
    {
        $interactionsReport = array();
        $maxOptionCount = 0;

        $showFrom = array_key_exists('show_from', $filter) && $filter['show_from'] ? new \DateTime($filter['show_from']) : null;
        $showTo = array_key_exists('show_to', $filter) && $filter['show_from'] ? new \DateTime($filter['show_to']) : null;
        $allDates = array_key_exists('all_dates', $filter) ? (bool) $filter['all_dates'] : null;
        $showAssessmentOnly = array_key_exists('show_assessment_only', $filter) ? (bool) $filter['show_assessment_only'] : null;
        $actionVerbs = array_key_exists('action_verb', $filter) ? $filter['action_verb'] : null;
        $filterByDates = ($showFrom || $showTo) ? true : false;
        
        $lrs = $this->getLrsClient();
        
        $queryParams = [
            'verb' => new TinCan\Verb([ 'id' => 'http://adlnet.gov/expapi/verbs/scored' ]),
            'related_activities' => true
        ];
        if (!$allDates && $filterByDates) {
            if ($showFrom) {
                $queryParams['since'] = $showFrom->format("Y-m-d\TH:i:s\Z");
            }
            if ($showTo) {
                $queryParams['since'] = $showTo->format("Y-m-d\TH:i:s\Z");
            }
        }
        $reports = array();
        $activityGroupIri = array();
        foreach($activities as $activity) {
            if (empty($activityGroupIri[$activity['activity_id']])) {
                $activityGroupIri[$activity['activity_id']] = 'http://' . $this->generateUUID($activity['activity_id']);
                $queryParams['activity'] = new TinCan\Activity([ 'id' => $activityGroupIri[$activity['activity_id']] ]);
                $report =  $this->convertReportToArray($activity, $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams)), $showAssessmentOnly, $actionVerbs);
                foreach ($report as $learnerEmail => $learnerReports) {
                    if (isset($reports[$learnerEmail])) {
                        foreach($learnerReports as $learnerReport) {
                            $reports[$learnerEmail][] = $learnerReport;
                        }
                    } else {
                        $reports[$learnerEmail] = $learnerReports;
                    }
                }
                //$queryParams['activity'] = new TinCan\Activity([ 'id' => $activity['item_uri'] ]);
                //$reports +=  $this->convertReportToArray($activity, $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams)));
            }
        }
        if (!empty($reports)) {
            foreach($learners as $learner) {
                $learnerEmail = (isset($learner['learner_email']) && strpos($learner['learner_email'], "@") !== false) ? $learner['learner_email'] : $learner['learner_id'] . "@savv-e.com.au";
                if (!empty($reports[$learnerEmail])) {
                    foreach($reports[$learnerEmail] as $report) {
                        $report += $learner;
                        $optionCount = count($report['xapi_question_options']);
                        if ($optionCount > $maxOptionCount) {
                            $maxOptionCount = $optionCount;
                        }
                        $interactionsReport[] = $report;
                    }
                }
            }
        }
        return array(
            'interactions' => $this->arrayOrderBy($interactionsReport, 'activity_title', SORT_ASC, 'learner_last_name', SORT_ASC, 'xapi_activity_timestamp', SORT_ASC),
            'maxOptionCount' => $maxOptionCount
        );
    }
       
    public function convertReportToArray($activity, $statements, $showAssessmentOnly, $actionVerbs)
    {
        $outputs = array();
        if (!empty($statements)) {
            foreach ($statements as $statement) {
                $actorObj = $statement->getActor();
                if (!empty($actorObj->getAccount())) {
                    $actor = $statement->getActor()->getAccount()->getName();
                } else {
                    $actor = trim(str_replace('mailto:', '', $actorObj->getMbox()));
                }
                if (!empty($actor)) {
                    $output = array();
                    $output += $activity;
                    $groupingId = $statement->getContext()->getContextActivities()->getGrouping()[0]->getId();
                    $output['xapi_activity_group_iri'] = isset($groupingId) && $groupingId != null ? $groupingId : '';

                    $verb = $statement->getVerb()->getDisplay()->asVersion('en-US');
                    $output['xapi_activity_verb'] = Stdlib\StringUtils::capitalise($verb['en-US']);

                    $output['xapi_activity_actor'] = $actor;

                    $timestamp = $statement->getTimestamp();
                    $output['xapi_activity_timestamp'] = isset($timestamp) ? date("Y-m-d h:i:s", strtotime($timestamp)) : '';

                    $target = $statement->getTarget();
                    if (!empty($target)) {
                        $definition = $target->getDefinition();
                        $page = $definition->getName()->asVersion('en-US');
                        $output['xapi_activity_definition'] = $page['en-US'];

                        $desc = $definition->getDescription()->asVersion('en-US');
                        $output['xapi_activity_definition_desc'] = $desc['en-US'];
                        
                        if (empty($output['xapi_activity_definition'])) {
                            $output['xapi_activity_definition'] = $output['xapi_activity_definition_desc'];
                        }
                        $output['xapi_activity_definition'] = preg_replace('/\s*Answered:\s*/', '', $output['xapi_activity_definition']);
                        if (stripos($target->getId(), "assessment") !== false) {
                            $output['xapi_question_assessment'] = 'Y';
                        } else if (stripos($definition->getType(), "assessment") !== false) {
                            $output['xapi_question_assessment'] = 'Y';
                        } else {
                            $output['xapi_question_assessment'] = 'N';
                        }
                        if ($output['xapi_question_assessment'] === 'Y' &&
                            $output['xapi_activity_definition'] === 'Finish the assessment') {
                            continue;
                        }
                    }
                    
                    $questions = array();
                    $statementResult = $statement->getResult();
                    if (!empty($statementResult)) {

                        $duration = $statementResult->getDuration();
                        $output['xapi_activity_duration'] = isset($duration) ? $this->extractTimeFromPTTime($duration) : '';

                        $score = $statementResult->getScore();
                        if (isset($score)) {
                            if (is_object($score)) {
                                $output['xapi_activity_score'] = $score->getScaled();
                            } else if (is_array($score)) {
                                if (!empty($score)) {
                                    $output['xapi_activity_score'] = implode(",", $score);
                                }
                            } else {
                                $output['xapi_activity_score'] = $score;
                            }
                        }
                        /*if (empty($output['xapi_activity_score'])){
                            $output['xapi_activity_verb'] = 'Not Scored';
                            $output['xapi_question_answered_correct'] = '';
                        } else {
                            $output['xapi_question_answered_correct'] = 'C';
                        }*/

                        $response = $statementResult->getResponse();
                        if (!empty($response)) {
                            $questions = $this->getQuestions($response);
                        }
                    }

                    if (empty($outputs[$actor])) {
                        $outputs[$actor] = array();
                    }
                    if (!empty($questions)) {
                        foreach($questions as $question) {
                            $newOutput = $question + $output;
                            if ((empty($showAssessmentOnly) || ($showAssessmentOnly === true && $newOutput['xapi_question_assessment'] === 'Y')) && (empty($actionVerbs) || in_array($newOutput['xapi_activity_verb'], $actionVerbs))) {
                                $outputs[$actor][] = $newOutput;
                            }
                        }
                    } else {
                        if ((empty($showAssessmentOnly) || ($showAssessmentOnly === true && $output['xapi_question_assessment'] === 'Y')) && (empty($actionVerbs) || in_array($output['xapi_activity_verb'], $actionVerbs))) {
                            $outputs[$actor][] = $output;
                        }
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
        $answerOptions = [];
        $correctCount = 0;
        $selectedCount = 0;
        $isRearrange = false;
        while($pos < $count) {
            if (empty($output['xapi_question_type'])) {
                list($pos, $type) = $this->getQuestionInfo($responses, $pos, $count, "/QuestionTitle:/");
                $type = trim(trim($type), "\\n\"");
                if (strpos($type, "IsPartOfAssessment:") !== false) {
                    list($assessment, $type) = explode("\\n", $type, 2);
                    $output['xapi_question_assessment'] = stripos($assessment, "yes") === false ? 'N' : 'Y';
                    if (strpos($type, "AnswerOptions:") !== false) {
                        list($type, $answerOptions) = explode("\\n", $type, 2);
                        $answerOptions = explode("|", trim(trim(str_replace('AnswerOptions:', '', $answerOptions)), "\\n\""));
                    }
                }
                $output['xapi_question_type'] = trim(trim(str_replace("Type:", "", $type)), "\\n\"");
            } else if (empty($output['xapi_question_title'])) {
                list($pos, $title) = $this->getQuestionInfo($responses, $pos, $count, "/DragItem\:|AnswerOption\:|Reply\:/");
                $answerPos = strpos($title, "AnswerOptions:");
                if ($answerPos !== false) {
                    $answerOption = substr($title, $answerPos);
                    $title = substr($title, 0, $answerPos);
                    $questionPos = strpos($answerOption, "QuestionTitle:");
                    if ($questionPos !== false) {
                        $title = substr($answerOption, $questionPos);
                        $answerOption = substr($answerOption, 0, $questionPos);
                    } else {
                        $title = substr($title, 0, $answerPos);
                    }
                    $answerOptions = explode("|", trim(trim(str_replace('AnswerOptions:', '', $answerOption)), "\\n\""));
                }
                $title = str_replace("\\n", "\n", trim(trim(str_replace('QuestionTitle:', '', $title)), "\\n\""));
                $output['xapi_question_title'] = empty($title) ? "N/A" : $title;
            } else {
                list($pos, $options) = $this->getQuestionInfo($responses, $pos, $count, "/QuestionTitle\:|DragItem\:|AnswerOption\:|Reply\:/");
                if (empty($output['xapi_question_options'])) {
                    $output['xapi_question_options'] = array();
                }
                $options = explode("---", trim(str_replace('----', '---', str_replace('DragItem:', '', str_replace('Reply:', '', str_replace('AnswerOption:', '', $options))))));
                if (!empty($options)) {
                    $option = str_replace("\\n", "\n", trim($options[0], "\\n\""));                
                    if (stripos($output['xapi_question_type'], "rearrange") !== false || stripos($output['xapi_question_type'], "drag") !== false) {
                        if (count($options) < 5) {
                            $altTextPos = stripos($options[2], "Alt text:");
                            if ($altTextPos != false) {
                                $options[2] = substr($options[2], 0, $altTextPos);
                                $options[4] = $options[3];
                                $options[3] = substr($options[2], $altTextPos);
                            }
                        }
                        $match = trim(str_replace("Correct Order:", "", str_replace("Alt text:", "", $options[3])), ", \"\\n");
                        if (stripos($output['xapi_question_type'], "drag") !== false) {
                            $optionTitle = $option;
                            $option = $match;
                            $match = $optionTitle;
                        }
                        $answer = 'C';
                        $selected = strpos($options[2], "incorrect") !== false ? '' : 'S';
                        $isRearrange = true;
                    } else {
                        $answer = !empty($options[3]) ? (strpos($options[3], "incorrect") === false ? 'C' : 'I') : (!empty($options[2]) ? (strpos($options[2], "incorrect") === false ? 'C' : 'I') : '');
                        $match = '';
                        $selected = !empty($options[1]) && strpos($options[1], "not selected") === false ? 'S' : '';
                    }
                    if (!empty($answerOptions)) {
                        foreach($answerOptions as $answerOption) {
                            if (strpos($answerOption, $option) !== false) {
                                $output['xapi_question_options'][] = array(
                                    'option' => $answerOption,
                                    'selected' => $selected,
                                    'answer' => $answer,
                                    'match' => $match
                                );
                            } else {
                                $output['xapi_question_options'][] = array(
                                    'option' => $answerOption,
                                    'selected' => '',
                                    'answer' => '',
                                    'match' => $match
                                );
                            }
                        }               
                    } else {
                        $output['xapi_question_options'][] = array(
                            'option' => $option,
                            'selected' => $selected,
                            'answer' => $answer,
                            'match' => $match
                        );
                    }
                    if ($answer === "C") {
                        if ($isRearrange) {
                            if ($selected === "S") {
                                $correctCount++;
                            }
                        } else {
                            if ($selected === "S" && empty($output['xapi_question_answered_correct'])) {
                                $output['xapi_question_answered_correct'] = 'C';
                                $selectedCount++;
                            }
                            $correctCount++;
                        }
                    } else if ($answer === "I" && $selected === "S") {
                        $output['xapi_question_answered_correct'] = 'I';
                    }
                }
                if ($pos < $count && strpos($responses[$pos], "QuestionTitle:") !== false) {
                    if ($correctCount > 0) {
                        $output['xapi_activity_verb'] = 'Scored';
                        if (empty($output['xapi_question_answered_correct'])) {
                            $output['xapi_question_answered_correct'] = $correctCount != count($output['xapi_question_options']) ? 'I' : 'C';
                        } else if (stripos($output['xapi_question_type'], "multiplechoice") !== false && $correctCount != $selectedCount) {
                            $output['xapi_question_answered_correct'] = 'I';
                        }
                    } else {
                        $output['xapi_activity_verb'] = 'Not Scored';
                        $output['xapi_question_answered_correct'] = '';
                    }
                    $output['xapi_question_title'] = $output['xapi_question_title'] === 'N/A' ? '' : $output['xapi_question_title'];
                    $correctCount = 0;
                    $selectedCount = 0;
                    $isRearrange = false;
                    $outputs[] = $output;
                    $output = array(
                        'xapi_question_type' => $output['xapi_question_type'],
                        'xapi_question_assessment' => $output['xapi_question_assessment']
                    );
                }
            }
        }
        if ($correctCount > 0 || $isRearrange) {
            $output['xapi_activity_verb'] = 'Scored';
            if (empty($output['xapi_question_answered_correct'])) {
                $output['xapi_question_answered_correct'] = $correctCount != count($output['xapi_question_options']) ? 'I' : 'C';
            } else if (stripos($output['xapi_question_type'], "multiplechoice") !== false && $correctCount != $selectedCount) {
                $output['xapi_question_answered_correct'] = 'I';
            }
        } else {
            $output['xapi_activity_verb'] = 'Not Scored';
            $output['xapi_question_answered_correct'] = '';
        }
        $output['xapi_question_title'] = $output['xapi_question_title'] === 'N/A' ? '' : $output['xapi_question_title'];
        $outputs[] = $output;
        return $outputs;
    }
    
    private function arrayOrderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row) {
                    $tmp[$key] = $row[$field];
                }
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
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
