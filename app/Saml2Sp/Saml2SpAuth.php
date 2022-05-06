<?php

namespace App\Saml2Sp;

use OneLogin\Saml2\Auth as OneLogin_Saml2_Auth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\LogoutRequest;
use App\Saml2Sp\LogoutResponse;
use OneLogin\Saml2\Constants;
use App\Saml2Sp\Utils;
use OneLogin\Saml2\Settings;


class Saml2SpAuth extends OneLogin_Saml2_Auth {
    /**
     * Settings data.
     *
     * @var Settings
     */
    private $_settings;



    /**
     * Initializes the SP SAML instance.
     *
     * @param array|null $settings Setting data
     *
     * @throws Exception
     * @throws Error
     */
    public function __construct(array $settings = null)
    {
        parent::__construct($settings);
        
        $this->_settings = new Settings($settings);
    }

    /**
     * Process the SAML Logout Response / Logout Request sent by the IdP.
     *
     * @param bool        $keepLocalSession             When false will destroy the local session, otherwise will keep it
     * @param string|null $requestId                    The ID of the LogoutRequest sent by this SP to the IdP
     * @param bool        $retrieveParametersFromServer True if we want to use parameters from $_SERVER to validate the signature
     * @param callable    $cbDeleteSession              Callback to be executed to delete session
     * @param bool        $stay                         True if we want to stay (returns the url string) False to redirect
     *
     * @return string|null
     *
     * @throws Error
     */
    public function processSLO($keepLocalSession = false, $requestId = null, $retrieveParametersFromServer = false, $cbDeleteSession = null, $stay = false)
    {
        $this->_errors = array();
        $this->_lastError = $this->_lastErrorException = null;
        if (isset($_REQUEST['SAMLResponse'])) {
            $logoutResponse = new LogoutResponse($this->_settings, $_REQUEST['SAMLResponse']);
            $this->_lastResponse = $logoutResponse->getXML();
            if (!$logoutResponse->isValid($requestId, $retrieveParametersFromServer)) {
                $this->_errors[] = 'invalid_logout_response';
                $this->_lastErrorException = $logoutResponse->getErrorException();
                $this->_lastError = $logoutResponse->getError();

            } else if ($logoutResponse->getStatus() !== Constants::STATUS_SUCCESS) {
                $this->_errors[] = 'logout_not_success';
            } else {
                $this->_lastMessageId = $logoutResponse->id;
                if (!$keepLocalSession) {
                    if ($cbDeleteSession === null) {
                        Utils::deleteLocalSession();
                    } else {
                        call_user_func($cbDeleteSession);
                    }
                }
            }
        } else if (isset($_GET['SAMLRequest'])) {
            $logoutRequest = new LogoutRequest($this->_settings, $_GET['SAMLRequest']);
            $this->_lastRequest = $logoutRequest->getXML();
            if (!$logoutRequest->isValid($retrieveParametersFromServer)) {
                $this->_errors[] = 'invalid_logout_request';
                $this->_lastErrorException = $logoutRequest->getErrorException();
                $this->_lastError = $logoutRequest->getError();
            } else {
                if (!$keepLocalSession) {
                    if ($cbDeleteSession === null) {
                        Utils::deleteLocalSession();
                    } else {
                        call_user_func($cbDeleteSession);
                    }
                }

                $inResponseTo = $logoutRequest->id;
                $this->_lastMessageId = $logoutRequest->id;
                $responseBuilder = new LogoutResponse($this->_settings);
                $responseBuilder->build($inResponseTo);
                $this->_lastResponse = $responseBuilder->getXML();

                $logoutResponse = $responseBuilder->getResponse();

                $parameters = array('SAMLResponse' => $logoutResponse);
                if (isset($_GET['RelayState'])) {
                    $parameters['RelayState'] = $_GET['RelayState'];
                }

                $security = $this->_settings->getSecurityData();
                if (isset($security['logoutResponseSigned']) && $security['logoutResponseSigned']) {
                    $signature = $this->buildResponseSignature($logoutResponse, isset($parameters['RelayState'])? $parameters['RelayState']: null, $security['signatureAlgorithm']);
                    $parameters['SigAlg'] = $security['signatureAlgorithm'];
                    $parameters['Signature'] = $signature;
                }

                return $this->redirectTo($this->getSLOResponseUrl(), $parameters, $stay);
            }
        } else {
            $this->_errors[] = 'invalid_binding';
            throw new Error(
                'SAML LogoutRequest/LogoutResponse not found. Only supported HTTP_REDIRECT Binding',
                Error::SAML_LOGOUTMESSAGE_NOT_FOUND
            );
        }
    }

} 