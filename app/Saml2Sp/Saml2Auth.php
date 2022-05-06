<?php

namespace App\Saml2Sp;

use App\Events\Auth\Saml2\Saml2LogoutEvent;
use App\Saml2Sp\Saml2SpAuth;
use App\Saml2Sp\Saml2User;
use OneLogin\Saml2\Error as OneLogin_Saml2_Error;
use Log;
use Psr\Log\InvalidArgumentException;
use URL;


class Saml2Auth {
    protected $auth;

    public function __construct()
    {
        $this->auth = $this->init();
    }

    private function init(){
        $config = config('saml2.ebdaa_idp_settings');

        if (empty($config['sp']['entityId'])) {
            $config['sp']['entityId'] = URL::route('saml2.metadata');
        }
        if (empty($config['sp']['assertionConsumerService']['url'])) {
            $config['sp']['assertionConsumerService']['url'] = URL::route('saml2.acs');
        }
        if (!empty($config['sp']['singleLogoutService']) &&
            empty($config['sp']['singleLogoutService']['url'])) {
            $config['sp']['singleLogoutService']['url'] = URL::route('saml2.sls');
        }
        if (strpos($config['sp']['privateKey'], 'file://')===0) {
            $config['sp']['privateKey'] = static::extractPkeyFromFile($config['sp']['privateKey']);
        }
        if (strpos($config['sp']['x509cert'], 'file://')===0) {
            $config['sp']['x509cert'] = static::extractCertFromFile($config['sp']['x509cert']);
        }
        if (strpos($config['idp']['x509cert'], 'file://')===0) {
            $config['idp']['x509cert'] = static::extractCertFromFile($config['idp']['x509cert']);
        }

        // dd($config);

        return new Saml2SpAuth($config);
    }

        /**
     * Show metadata about the local sp. Use this to configure your saml2 IDP
     * @return mixed xml string representing metadata
     * @throws \InvalidArgumentException if metadata is not correctly set
     */
    public function getMetadata()
    {
        $auth = $this->auth;
        $settings = $auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (empty($errors)) {

            return $metadata;
        } else {

            throw new InvalidArgumentException(
                'Invalid SP metadata: ' . implode(', ', $errors),
                OneLogin_Saml2_Error::METADATA_SP_INVALID
            );
        }
    }

    /**
     * Initiate a saml2 login flow. It will redirect! Before calling this, check if user is
     * authenticated (here in saml2). That would be true when the assertion was received this request.
     *
     * @param string|null $returnTo        The target URL the user should be returned to after login.
     * @param array       $parameters      Extra parameters to be added to the GET
     * @param bool        $forceAuthn      When true the AuthNReuqest will set the ForceAuthn='true'
     * @param bool        $isPassive       When true the AuthNReuqest will set the Ispassive='true'
     * @param bool        $stay            True if we want to stay (returns the url string) False to redirect
     * @param bool        $setNameIdPolicy When true the AuthNReuqest will set a nameIdPolicy element
     *
     * @return string|null If $stay is True, it return a string with the SLO URL + LogoutRequest + parameters
     */
    function login($returnTo = null, $parameters = array(), $forceAuthn = false, $isPassive = false, $stay = false, $setNameIdPolicy = true)
    {
        $auth = $this->auth;

        return $auth->login($returnTo, $parameters, $forceAuthn, $isPassive, $stay, $setNameIdPolicy);
    }

    /**
     * Process a Saml response (assertion consumer service)
     * When errors are encountered, it returns an array with proper description
     */
    function acs()
    {
        $auth = $this->auth;

        $auth->processResponse();

        $errors = $auth->getErrors();

        if (!empty($errors)) {
            return array('error' => $errors, 'last_error_reason' => $auth->getLastErrorReason());
        }

        if (!$auth->isAuthenticated()) {
            return array('error' => 'Could not authenticate', 'last_error_reason' => $auth->getLastErrorReason());
        }

        return null;

    }

    /**
     * The user info from the assertion
     * @return Saml2User
     */
    function getSaml2User()
    {
        return new Saml2User($this->auth);
    }

    /**
     * The ID of the last message processed
     * @return String
     */
    function getLastMessageId()
    {
        return $this->auth->getLastMessageId();
    }

    /**
     * Initiate a saml2 logout flow. It will close session on all other SSO services. You should close
     * local session if applicable.
     *
     * @param string|null $returnTo            The target URL the user should be returned to after logout.
     * @param string|null $nameId              The NameID that will be set in the LogoutRequest.
     * @param string|null $sessionIndex        The SessionIndex (taken from the SAML Response in the SSO process).
     * @param string|null $nameIdFormat        The NameID Format will be set in the LogoutRequest.
     * @param bool        $stay            True if we want to stay (returns the url string) False to redirect
     * @param string|null $nameIdNameQualifier The NameID NameQualifier will be set in the LogoutRequest.
     *
     * @return string|null If $stay is True, it return a string with the SLO URL + LogoutRequest + parameters
     *
     * @throws OneLogin_Saml2_Error
     */
    function logout($returnTo = null, $nameId = null, $sessionIndex = null, $nameIdFormat = null, $stay = false, $nameIdNameQualifier = null)
    {
        $auth = $this->auth;

        return $auth->logout($returnTo, [], $nameId, $sessionIndex, $stay, $nameIdFormat, $nameIdNameQualifier);
    }

    /**
     * Process a Saml response (assertion consumer service)
     * returns an array with errors if it can not logout
     */
    function sls($retrieveParametersFromServer = false)
    {
        $auth = $this->auth;

        // destroy the local session by firing the Logout event
        $keep_local_session = false;
        $session_callback = function () {
            event(new Saml2LogoutEvent());
        };

        $auth->processSLO($keep_local_session, null, $retrieveParametersFromServer, $session_callback);

        $errors = $auth->getErrors();

        if (!empty($errors)) {
            return array('error' => $errors, 'last_error_reason' => $auth->getLastErrorReason());
         }

        return null;
    }

    /**
     * Get the last error reason from \OneLogin_Saml2_Auth, useful for error debugging.
     * @see \OneLogin_Saml2_Auth::getLastErrorReason()
     * @return string
     */
    function getLastErrorReason() {
        return $this->auth->getLastErrorReason();
    }

    protected static function extractPkeyFromFile($path) {
        $res = openssl_get_privatekey($path);
        if (empty($res)) {
            throw new \Exception('Could not read private key-file at path \'' . $path . '\'');
        }
        openssl_pkey_export($res, $pkey);
        openssl_pkey_free($res);
        return static::extractOpensslString($pkey, 'PRIVATE KEY');
    }

    protected static function extractCertFromFile($path) {
        $res = openssl_x509_read(file_get_contents($path));
        if (empty($res)) {
            throw new \Exception('Could not read X509 certificate-file at path \'' . $path . '\'');
        }
        openssl_x509_export($res, $cert);
        openssl_x509_free($res);
        return static::extractOpensslString($cert, 'CERTIFICATE');
    }

    protected static function extractOpensslString($keyString, $delimiter) {
        $keyString = str_replace(["\r", "\n"], "", $keyString);
        $regex = '/-{5}BEGIN(?:\s|\w)+' . $delimiter . '-{5}\s*(.+?)\s*-{5}END(?:\s|\w)+' . $delimiter . '-{5}/m';
        preg_match($regex, $keyString, $matches);
        return empty($matches[1]) ? '' : $matches[1];
    }
}