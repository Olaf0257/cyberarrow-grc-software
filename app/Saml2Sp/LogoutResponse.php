<?php

namespace App\Saml2Sp;
use OneLogin\Saml2\LogoutResponse as OneLogin_Saml2_LogoutResponse;
use App\Saml2Sp\Utils;
use OneLogin\Saml2\ValidationError;
use DOMDocument;
use DOMNodeList;
use Exception;


class LogoutResponse extends OneLogin_Saml2_LogoutResponse
{
        /**
     * Determines if the SAML LogoutResponse is valid
     *
     * @param string|null $requestId                    The ID of the LogoutRequest sent by this SP to the IdP
     * @param bool        $retrieveParametersFromServer True if we want to use parameters from $_SERVER to validate the signature
     *
     * @return bool Returns if the SAML LogoutResponse is or not valid
     * 
     * @throws ValidationError
     */
    public function isValid($requestId = null, $retrieveParametersFromServer = false)
    {
        $this->_error = null;
        try {
            $idpData = $this->_settings->getIdPData();
            $idPEntityId = $idpData['entityId'];

            if ($this->_settings->isStrict()) {
                $security = $this->_settings->getSecurityData();

                if ($security['wantXMLValidation']) {
                    $res = Utils::validateXML($this->document, 'saml-schema-protocol-2.0.xsd', $this->_settings->isDebugActive());
                    if (!$res instanceof DOMDocument) {
                        throw new ValidationError(
                            "Invalid SAML Logout Response. Not match the saml-schema-protocol-2.0.xsd",
                            ValidationError::INVALID_XML_FORMAT
                        );
                    }
                }

                // Check if the InResponseTo of the Logout Response matchs the ID of the Logout Request (requestId) if provided
                if (isset($requestId) && $this->document->documentElement->hasAttribute('InResponseTo')) {
                    $inResponseTo = $this->document->documentElement->getAttribute('InResponseTo');
                    if ($requestId != $inResponseTo) {
                        throw new ValidationError(
                            "The InResponseTo of the Logout Response: $inResponseTo, does not match the ID of the Logout request sent by the SP: $requestId",
                            ValidationError::WRONG_INRESPONSETO
                        );
                    }
                }

                // Check issuer
                $issuer = $this->getIssuer();
                if (!empty($issuer) && $issuer != $idPEntityId) {
                    throw new ValidationError(
                        "Invalid issuer in the Logout Response",
                        ValidationError::WRONG_ISSUER
                    );
                }

                $currentURL = Utils::getSelfRoutedURLNoQuery();

                // Check destination
                if ($this->document->documentElement->hasAttribute('Destination')) {
                    $destination = $this->document->documentElement->getAttribute('Destination');
                    if (!empty($destination) && strpos($destination, $currentURL) === false) {
                        throw new ValidationError(
                            "The LogoutResponse was received at $currentURL instead of $destination",
                            ValidationError::WRONG_DESTINATION
                        );
                    }
                }

                if ($security['wantMessagesSigned'] && !isset($_REQUEST['Signature'])) {
                    throw new ValidationError(
                        "The Message of the Logout Response is not signed and the SP requires it",
                        ValidationError::NO_SIGNED_MESSAGE
                    );
                }
            }

            if (isset($_REQUEST['Signature'])) {
                $signatureValid = Utils::validateBinarySign("SAMLResponse", $_REQUEST, $idpData, $retrieveParametersFromServer);
                if (!$signatureValid) {
                    throw new ValidationError(
                        "Signature validation failed. Logout Response rejected",
                        ValidationError::INVALID_SIGNATURE
                    );
                }
            }
            return true;
        } catch (Exception $e) {
            $this->_error = $e;
            $debug = $this->_settings->isDebugActive();
            if ($debug) {
                echo htmlentities($this->_error->getMessage());
            }
            return false;
        }
    }
    
}
