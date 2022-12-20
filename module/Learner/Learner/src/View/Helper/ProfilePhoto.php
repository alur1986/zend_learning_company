<?php
/**
 * @deprecated
 * Retained for reference only
 */
namespace Learner\View\Helper;

use Learner\Service\Options;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\View\Helper\AbstractViewHelper;

class ProfilePhoto extends AbstractViewHelper
{

    protected $options;

    /**
     * Constructor
     *
     * @param Options $options
     */
    public function __construct (Options $options)
    {
        $this->options = $options;
    }

    /**
     * Invoke the view helper
     *
     * @param Entity\Learner $learner
     */
    public function __invoke ($learner = null, $type = null)
    {
        if (null == $learner) {
            return $this;
        }

        switch (true) {
            case strtolower($type) == 'url':
                $profile = $this->url($learner);
                break;

            default:
            case strtolower($type) == 'base64':
                $profile = $this->base64($learner);
                break;
        }

        return $profile;
    }

    /**
     * Returns the full URL of the learner's profile photo
     *
     * @param Entity\Learner $learner
     * @return string
     */
    public function url ($learner)
    {
        // using the router for displaying the images
        if (isset($learner['profile_picture']) || $learner['profile_picture']) {
            $filename = $learner['profile_picture'];
            $userId = $learner['user_id'];
            $serviceManager = $this->getServiceManager();
            $router = $serviceManager->get('Router');

            // assemble the URL of the profile photo
            $routeName = 'learner/photo/show';
            $routeOptions = ['name' => $routeName];
            $routeParams = ['user_id' => $learner['user_id'], 'filename' => $filename];
            $url  = $router->assemble($routeParams, $routeOptions);

            return $url;
        }

        // if the URL is set outside the module
        elseif (isset($learner['profile_picture_uri'])) {
            $baseUri = $learner['profile_picture_uri'];
            $url = $this->serverUrl($baseUri);

            return $url;
        }

        // full path fallback
        elseif (isset($learner['profile_picture']) && (isset($learner['user_id']) && !empty($learner['user_id']))) {
            $userId = $learner['user_id'];
            $serviceManager = $this->getServiceManager();
            $router = $serviceManager->get('Router');

            /* @var $options \Learner\Service\Options */
            $options = $serviceManager->get('Learner\Options');
            $baseUri = $options->getBaseUri() . '/' . $userId . '/photos/' . $learner['profile_picture'];

            $url = $this->serverUrl($baseUri);
            return $url;
        }

        // if no profile picture uploaded, return nothing
        return null;
    }

    /**
     * Get the BASE64 string of the learner's profile photo
     *
     * @param Entity\Learner $learner
     * @return string
     */
    public function base64 ($learner)
    {
        $serviceManager = $this->getServiceManager();

        /* @var $options \Learner\Service\Options */
        $options = $serviceManager->get('Learner\Options');

        /* @var $service \Learner\Service\LearnerService */
        $service = $serviceManager->get('Learner\Service');

        // the base64 string of the image
        $base64String = null;
        $filename = null;

        // if there is a profile picture, then return the profile picture
        if ((isset($learner['profile_picture']) && $learner['profile_picture']) && (isset($learner['user_id']) && !empty($learner['user_id']))) {
            $userId = $learner['user_id'];
            $directory = $options->getUploadPath() . DIRECTORY_SEPARATOR . $userId;
            $filename = $directory . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $learner['profile_picture'];
        }

        // if profile picture is not available in the learner entity, then manually find it in the repository
        elseif (isset($learner['user_id']) && !empty($learner['user_id'])) {
            $userId = $learner['user_id'];
            $setting = $service->findOneSettingByUserId($userId, 'profile_picture');
            if ($setting && ($profilePicture = $setting['value'])) {
                $directory = $options->getUploadPath() . DIRECTORY_SEPARATOR . $userId;
                $filename = $directory . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $profilePicture;
            }
        }

        // check if file exists
        if (Stdlib\FileUtils::fileExists($filename)) {
            $base64String = Stdlib\ImageUtils::base64($filename);
        }

        // if no profile picture uploaded, use a default image placeholder
        if (!$base64String) {
            $base64String = $this->placeholder();
        }

        // override to support IE9 and below
        /* @var $browser \Savve\Browser\Service\Browser */
        $browser = $this->plugin('browser');
        if ($browser->getName() === $browser::IE) {
            $version = $browser->getVersion();
            if ($version <= 12) {
                if (preg_match('/^data\:(?P<content_type>.*)\;base64/i', $base64String, $matches)) {
                    $contentType = $matches['content_type'];
                }

                $base64String = $this->url($learner);
            }
        }

        return $base64String;
    }

    /**
     * Returns the placeholder image
     *
     * @return string
     */
    public function placeholder ()
    {
        $options = $this->options;
        $defaultProfilePhoto = $options->getProfilePhotoPlaceholder();

        // check if file exists
        if (!($defaultProfilePhoto && Stdlib\FileUtils::fileExists($defaultProfilePhoto))) {
            return;
        }
        return Stdlib\ImageUtils::base64($defaultProfilePhoto);
    }
}