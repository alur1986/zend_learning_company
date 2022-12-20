<?php

namespace Learner\View\Helper;

use Savve\Image\View\Helper\Image as AbstractViewHelper;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Profile extends AbstractViewHelper implements
        ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Returns the placeholder image
     *
     * @return string
     */
    public function placeholder ()
    {
        $pluginManager = $this->getServiceLocator();
        $serviceManager = $pluginManager->getServiceLocator();

        $options = $serviceManager->get('Learner\Options');
        $placeholder = $options->getProfilePhotoPlaceholder();
        $this->source($placeholder);

        return $placeholder;
    }
}