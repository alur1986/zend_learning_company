<?php

namespace LearningPlan\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ActivitiesController extends AbstractActionController
{

    /**
     * Display ALL Learning Plans
     *
     * @return array
     * @throws \Exception
     */
    public function activitiesAction ()
    {
        $siteId  = $this->params('site_id');
        $planId  = $this->params('plan_id');
        $request = $this->getRequest();
        $message = false;
        /* @service LearningPlan\Service\LearningPlan */
        $service = $this->learningPlanService();

        // form
        $form = $this->form('LearningPlan\Form\Activities');

        if ($post = $this->post(false)) {
            try {
                // form validation
                $data = $form->validate($post);

                // save in repository
                $service->saveActivities($data);

                // success
                $message['success'] = $this->translate('The Learning Playlist activities have been saved successfully.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot save the Learning Playlist activities. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        $plan    = $service->findAllByPlanId($planId);
        $currentActivities = false;
        if (isset($plan[0]['hasActivities'])) {
            $currentActivities = $plan[0]['hasActivities'];
        } else {
            $plan = $service->findOneLearningPlanById($planId);
        }

        // get all activities not including any 'currently' within the learning plan
        $service    = $this->service('Learning\Service');
        $activities = $service->findAllLearningActivitiesOrderByTitle($siteId, false);
        #$activities = $service->findAllLearningActivitiesBySiteId($siteId, false);

        $valueOptions = [];
        foreach ($activities as $activity) {
            $distribution = $activity['distribution'];
            $valueOptions[] = [
                'label' => $activity['title'] . ' - ' . $activity['activity_type'] . ' - distributions: ' . count($distribution),
                'value' => $activity['activity_id']
            ];
        }
        $form->get('available_columns')
            ->setValueOptions($valueOptions);


        if (is_object($currentActivities) && is_array(Stdlib\ObjectUtils::toArray($currentActivities)) && count($currentActivities)) {
            // setup the array so its sorted by the 'ordering' value
            $activities = array();
            $order = 0;
            $lastOrder = -1;
            foreach ($currentActivities as $activity) {
                $order = $activity['ordering'];
                if ($order > $lastOrder) {
                    $activities[$order] = $activity;
                } else {
                    $activities[] = $activity;
                }
                $lastOrder = $order;
            }

            ksort($activities);
            $arr = array();
            foreach ($activities as $activity) {
                $arr[] = array($activity['activities']['activity_id'] => $activity['activities']['title']);
            }
            $form->get('config')
                ->setValue(json_encode($arr));
        }

        // if a success message is passed
        $updateMessage = false;
        if (isset($this->getRequest()->getCookie()->updateMessage) ) {
            $updateMessage = $this->getRequest()->getCookie()->updateMessage;
        }
        if ($updateMessage) {
            $message['success'] = $this->translate($updateMessage);
        }

        $form->get('plan_id')
            ->setValue($planId);

        return [
            'form'       => $form,
            'plan'       => $plan,
            'activities' => $activities,
            'current'    => $currentActivities,
            'message'    => $message
        ];
    }

    /**
     * Learning Plan Service
     *
     * @return \LearningPlan\Service\LearningPlan
     * @throws \Exception
     */
    public function learningPlanService ()
    {
        try {
            return $this->service('LearningPlan\Service');
        } catch (\Exception $e) {
            throw $e;
        }

    }
}
