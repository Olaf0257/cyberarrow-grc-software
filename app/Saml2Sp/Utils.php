<?php

namespace App\Saml2Sp;

use OneLogin\Saml2\Utils as  OneLogin_Saml2_Utils;
use OneLogin\Saml2\Error;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecEnc;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DomNode;
use DOMXPath;
use Exception;

class Utils extends OneLogin_Saml2_Utils
{
        /**
     * Validates a binary signature
     *
     * @param string $messageType                    Type of SAML Message
     * @param array  $getData                        HTTP GET array
     * @param array  $idpData                        IdP setting data
     * @param bool   $retrieveParametersFromServer   Indicates where to get the values in order to validate the Sign, from getData or from $_SERVER
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function validateBinarySign($messageType, $getData, $idpData, $retrieveParametersFromServer = false)
    {
        if (!isset($getData['SigAlg'])) {
            $signAlg = XMLSecurityKey::RSA_SHA1;
        } else {
            $signAlg = $getData['SigAlg'];
        }

        if ($retrieveParametersFromServer) {
            $signedQuery = $messageType.'='.Utils::extractOriginalQueryParam($messageType);
            if (isset($getData['RelayState'])) {
                $signedQuery .= '&RelayState='.Utils::extractOriginalQueryParam('RelayState');
            }
            $signedQuery .= '&SigAlg='.Utils::extractOriginalQueryParam('SigAlg');
        } else {
            $signedQuery = $messageType.'='.urlencode($getData[$messageType]);
            if (isset($getData['RelayState'])) {
                $signedQuery .= '&RelayState='.urlencode($getData['RelayState']);
            }
            $signedQuery .= '&SigAlg='.urlencode($signAlg);
        }

        if ($messageType == "SAMLRequest") {
            $strMessageType = "Logout Request";
        } else {
            $strMessageType = "Logout Response";
        }
        $existsMultiX509Sign = isset($idpData['x509certMulti']) && isset($idpData['x509certMulti']['signing']) && !empty($idpData['x509certMulti']['signing']);
        if ((!isset($idpData['x509cert']) || empty($idpData['x509cert'])) && !$existsMultiX509Sign) {
            throw new Error(
                "In order to validate the sign on the ".$strMessageType.", the x509cert of the IdP is required",
                Error::CERT_NOT_FOUND
            );
        }

        if ($existsMultiX509Sign) {
            $multiCerts = $idpData['x509certMulti']['signing'];
        } else {
            $multiCerts = array($idpData['x509cert']);
        }

        $signatureValid = false;
        foreach ($multiCerts as $cert) {
            $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'public'));
            $objKey->loadKey($cert, false, true);

            if ($signAlg != XMLSecurityKey::RSA_SHA1) {
                try {
                    $objKey = Utils::castKey($objKey, $signAlg, 'public');
                } catch (Exception $e) {
                    $ex = new ValidationError(
                        "Invalid signAlg in the recieved ".$strMessageType,
                        ValidationError::INVALID_SIGNATURE
                    );
                    if (count($multiCerts) == 1) {
                        throw $ex;
                    }
                }
            }

            if ($objKey->verifySignature($signedQuery, base64_decode($_REQUEST['Signature'])) === 1) {
                $signatureValid = true;
                break;
            }
        }
        return $signatureValid;
    }
    
}
