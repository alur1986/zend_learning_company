<?php

namespace Elucidat\Elucidat\Client;

interface ClientInterface
{

    public function connect ();

    public function check ();

    public function get ($endpoint, array $parameters = []);

    public function post ($endpoint, array $parameters = []);

    public function put ($endpoint, array $parameters = []);

    public function delete ($endpoint, array $parameters = []);

    public function setUsername ($username);

    public function setPassword ($password);

    public function setUrl ($url);

    public function setBaseUrl ($url);

    public function setPlatform ($platform);

    public function getLaunchLink($authorEmail, $companyName, $publicKey);
}