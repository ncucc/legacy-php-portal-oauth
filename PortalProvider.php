<?php

class PortalProvider extends \League\OAuth2\Client\Provider\GenericProvider
{
    /**
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
    }

    /**
     * Builds request options used for requesting an access token.
     *
     * @param array $params
     * @return array
     */
    protected function getAccessTokenOptions(array $params)
    {
        $options = [
            'headers' => [
                'Content-type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
            ]
        ];
        if ($this->getAccessTokenMethod() === self::METHOD_POST) {
            $options['body'] = $this->getAccessTokenBody($params);
        }

        return $options;
    }
}
