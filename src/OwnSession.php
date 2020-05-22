<?php

namespace Laminas\Session\Validator;

use Jenssegers\Agent\Agent;
use Laminas\Session\Validator\ValidatorInterface as SessionValidator;

class OwnSession implements SessionValidator
{
    /**
     * Internal data.
     *
     * @var string
     */
    protected $data;

    /**
     * Constructor
     * get the current user data and store it in the session as 'valid data'
     *
     * @param null|string $data
     */
    public function __construct($data = null)
    {
        if (empty($data)) {
            $data = $this->createData();
        }
        $this->data = $data;
    }

    /**
     * Creates data for further comparation
     */
    protected function createData()
    {
        $agent = new Agent();
        return md5(session_id() . '|' . $agent->device() . '|' . $agent->browser() . '|' . $agent->platform() . '|' . implode(',', $agent->languages()));
    }

    /**
     * isValid() - this method will determine if the current session ID matches the
     * ID we stored when we initialized this variable.
     *
     * @return bool
     */
    public function isValid()
    {
        $data = $this->createData();
        return ($data === $this->getData());
    }

    /**
     * Retrieve token for validating call
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return validator name
     *
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }
}
