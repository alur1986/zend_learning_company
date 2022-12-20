<?php

namespace Learner\View\Helper;

use Savvecentral\Entity;
use Savve\View\Helper\AbstractViewHelper;

class Learner extends AbstractViewHelper
{

    /**
     * Learner entity
     *
     * @var Entity\Learner
     */
    protected $learner;

    /**
     * Constructor
     *
     * @param Entity\Learner $learner
     */
    public function __construct (Entity\Learner $learner)
    {
        $this->learner = $learner;
    }

    /**
     * Invoke the view helper
     *
     * @param Entity\Learner $learner
     */
    public function __invoke ($learner = null)
    {
        if ($learner) {
            $this->learner = $learner;
        }

        return $this->learner;
    }
}