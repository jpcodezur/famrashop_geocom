<?php
require_once(Mage::getBaseDir('lib') . '/Httpful/Request.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Http.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Bootstrap.php');
use \Httpful\Request;
use \Httpful\Http;

require_once "Mage/Customer/controllers/AccountController.php";
require_once Mage::getModuleDir('controllers','Cualit_UruguayanCustomer').'/Customer/AccountController.php';

class Geocom_UruguayanCustomer_Customer_AccountController extends Cualit_UruguayanCustomer_Customer_AccountController
{
    /*
     * Admin or Callcenter logged
     */
//    public function _isAdminLoggedIn() {
//        $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')
//            ->getSessionData('adminhtml');
//        $adminUser = $adminhtml['admin']['user'];
//        return isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//    }
//
//
//    /*
//     * Custom login, accepts either email or document number.
//     * Password required.
//     */
//    private function UYCustomerLogin()
//    {
//        if (!$this->_validateFormKey()) {
//            $this->_redirect('*/*/');
//            return;
//        }
//        if ($this->_getSession()->isLoggedIn()) {
//            $this->_redirect('*/*/');
//            return;
//        }
//        $session = $this->_getSession();
//        if ($this->getRequest()->isPost()) {
//            $login = $this->getRequest()->getPost('login');
//            if (!empty($login['username']) && !empty($login['password'])) {
//                $emailOrDocument = $login['username'];
//                $geoSignedIn = $this->signInGeoloyatyCustomer($emailOrDocument, false, $login['password']);
//                if ($geoSignedIn) {
//                    $customer = Mage::getModel('uruguayancustomer/customer')->findByEmailOrDocument(trim($emailOrDocument));
//                    if (!$customer->getId()) {
//                        //user is not in magento -> sinc.
//                        $created = Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty();
//                        if (!$created) {
//                            $session->addError($this->__('Error al inciar sesión.'));
//                            $this->_loginPostRedirect();
//                            return;
//                        }
//
//                    } else {
//                        $updated = Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty($customer->getId());
//                        if (!$updated) {
//                            $session->addError($this->__('Error al inciar sesión.'));
//                            $this->_loginPostRedirect();
//                            return;
//                        }
//                    }
//                    $customer = Mage::getModel('uruguayancustomer/customer')->findByEmailOrDocument(trim($emailOrDocument));
//                    try {
//                        //login
//                        Mage::getSingleton('customer/session')->loginById($customer->getId());
//                        //save encrypted password
//                        $encryptedPass = Mage::helper('uruguayancustomer')->c_encrypt($login['password'], PASS_ENCRYPT_SALT);
//                        Mage::getSingleton('customer/session')->setData('encrypted_password', $encryptedPass);
//                    } catch (Exception $e) {
//                        $session->addError($this->__('Error al inciar sesión.'));
//                        $session->setUsername($login['username']);
//                    }
//
//                } else {
//                    $session->addError($this->__('Login and password are required.'));
//                }
//            }
//            //redirect to index in _loginPostRedirect()
//            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getBaseUrl());
//
//            $this->_loginPostRedirect();
//        }
//    }
//
//    /**
//     * Reset forgotten password
//     * Used to handle data recieved from reset forgotten password form
//     */
//    public function resetPasswordPostAction()
//    {
//        $resetPasswordLinkToken = (string)$this->getRequest()->getQuery('token');
//        $customerId = (int)$this->getRequest()->getQuery('id');
//        $password = (string)$this->getRequest()->getPost('password');
//        $passwordConfirmation = (string)$this->getRequest()->getPost('confirmation');
//        $errorMessages = array();
//        if (iconv_strlen($password) <= 0) {
//            array_push($errorMessages, $this->_getHelper('customer')->__('New password field cannot be empty.'));
//        }
//        /** @var $customer Mage_Customer_Model_Customer */
//        $customer = $this->_getModel('customer/customer')->load($customerId);
//        $customer->setPassword($password);
//        $customer->setConfirmation($passwordConfirmation);
//        $validationErrorMessages = $customer->validate();
//        if (is_array($validationErrorMessages)) {
//            $errorMessages = array_merge($errorMessages, $validationErrorMessages);
//        }
//        if (!empty($errorMessages)) {
//            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
//            foreach ($errorMessages as $errorMessage) {
//                $this->_getSession()->addError($errorMessage);
//            }
//            $this->_redirect('*/*/resetpassword', array(
//                'id' => $customerId,
//                'token' => $resetPasswordLinkToken
//            ));
//            return;
//        }
//        try {
//            //retrieve geo reset token, associated to magento token
//            $resetTokenCompatibleModel = Mage::getModel("ResetTokenCompatible/ResetTokenCompatible");
//            $result = $resetTokenCompatibleModel->getCollection()->addFieldToFilter('mage_token', $resetPasswordLinkToken)->getFirstItem();
//            if (!$result || !$result->getData('geo_token')) {
//                throw new Exception('No hay token de geoloyalty asociado a este token de Magento');
//            }
//            $geoToken = $result->getData('geo_token');
//            if ($this->resetPasswordGeoloyaltyCustomer($geoToken, $password)) {
//                // Empty current reset password token i.e. invalidate it
//                $customer->setRpToken(null);
//                $customer->setRpTokenCreatedAt(null);
//                $customer->setConfirmation(null);
//                $customer->save();
//                $this->_getSession()->addSuccess($this->_getHelper('customer')->__('Your password has been updated.'));
//                $this->_redirect('*/*/login');
//            } else {
//                $this->_getSession()->addException($exception, $this->__('Cannot save a new password.'));
//                $this->_redirect('*/*/resetpassword', array(
//                    'id' => $customerId,
//                    'token' => $resetPasswordLinkToken
//                ));
//                return;
//            }
//        } catch (Exception $exception) {
//            $this->_getSession()->addException($exception, $this->__('Cannot save a new password.'));
//            $this->_redirect('*/*/resetpassword', array(
//                'id' => $customerId,
//                'token' => $resetPasswordLinkToken
//            ));
//            return;
//        }
//    }
//
//    /**
//     * Login post action
//     */
//    public function loginPostAction()
//    {
//
//        $isAdminLoggedIn = $this->_isAdminLoggedIn();
//
//        if (!$isAdminLoggedIn) {
//            //call custom login
//            $this->UYCustomerLogin();
//            //parent::loginPostAction();
//            return;
//        }
//
//        if (!$this->_validateFormKey()) {
//            $this->_redirect('*/*/');
//            return;
//        }
//
//        if ($this->_getSession()->isLoggedIn()) {
//            $this->_redirect('*/*/');
//            return;
//        }
//
//        $session = $this->_getSession();
//
//        if ($this->getRequest()->isPost()) {
//            $login = $this->getRequest()->getPost('login');
//            $emailOrDocument = $login['username'];
//            if (!empty($emailOrDocument)) {
//                //callcenter logged in
//                //try login
//                $geoSignedIn = $this->signInGeoloyatyCustomer($emailOrDocument, true);
//                if ($geoSignedIn) {
//                    $customer = Mage::getModel('uruguayancustomer/customer')->findByEmailOrDocument(trim($emailOrDocument));
//                    if (!$customer->getId()) {
//                        //user is not in magento -> sinc.
//                        $created = Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty(null, true);
//                        if (!$created) {
//                            $session->addError($this->__('Error al inciar sesión.'));
//                            $this->_loginPostRedirect();
//                            return;
//                        }
//                    } else {
//                        $updated = Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty($customer->getId(), true);
//                        if (!$updated) {
//                            $session->addError($this->__('Error al inciar sesión.'));
//                            $this->_loginPostRedirect();
//                            return;
//                        }
//                    }
//                    $customer = Mage::getModel('uruguayancustomer/customer')->findByEmailOrDocument(trim($emailOrDocument));
//                    try {
//                        //login
//                        Mage::getSingleton('customer/session')->loginById($customer->getId());
//                        // Clear cart for logged customer
//                        Mage::getSingleton('checkout/cart')->truncate();
//                        Mage::getSingleton('checkout/cart')->save();
//                    } catch (Exception $e) {
//                        $session->addError($this->__('Error al inciar sesión.'));
//                        $session->setUsername($login['username']);
//                    }
//                } else {
//                    $session->addError($this->__('Login and password are required.'));
//                }
//            } else {
//                $session->addError($this->__('Email or Document number is required.'));
//            }
//        }
//        //redirect to index in _loginPostRedirect()
//        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getBaseUrl());
//
//        $this->_loginPostRedirect();
//    }
//
//    /**
//     * Change customer password action
//     */
//    public function editPostAction()
//    {
//
//        if (!$this->_validateFormKey()) {
//            return $this->_redirect('*/*/edit');
//        }
//        if ($this->getRequest()->isPost()) {
//            /** @var $customer Mage_Customer_Model_Customer */
//            $customer = $this->_getSession()->getCustomer();
//            /** @var $customerForm Mage_Customer_Model_Form */
//            $customerForm = $this->_getModel('customer/form');
//            $customerForm->setFormCode('customer_account_edit')
//                ->setEntity($customer);
//            $customerData = $customerForm->extractData($this->getRequest());
//            $errors = array();
//
//            $customerErrors = $customerForm->validateData($customerData);
//            if ($customerErrors !== true) {
//                $errors = array_merge($customerErrors, $errors);
//            } else {
//                $customerForm->compactData($customerData);
//                $errors = array();
//                // Validate account and compose list of errors if any
//                $customerErrors = $customer->validate();
//                if (is_array($customerErrors)) {
//                    $errors = array_merge($errors, $customerErrors);
//                }
//            }
//            if (!empty($errors)) {
//                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
//                foreach ($errors as $message) {
//                    $this->_getSession()->addError($message);
//                }
//                $this->_redirect('*/*/edit');
//                return $this;
//            } else {
//                $token = Mage::getSingleton('customer/session')->getData('session_token');
//                $customerData = Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token);
//                $geoUpdated = Mage::getModel('uruguayancustomer/customer')->updateAccountInfo($token, $customerData, $customer->getData('firstname'), $customer->getData('middle_name'), $customer->getData('lastname'), $customer->getData('dob'), $customer->getData('gender'));
//                if (!$geoUpdated) {
//                    Mage::getSingleton('core/session')->getMessages(true);
//                    $this->_getSession()->addError($this->__('Cannot save the customer.'));
//                    $this->_redirect('*/*/edit');
//                    return $this;
//                }
//            }
//            try {
//                $customer->setConfirmation(null);
//
//                if ($this->getRequest()->getParam('change_password')) {
//                    //magento validations are ok, and password changed is requested
//                    $isPassChanged = $this->changePasswordGeoloyaltyCustomer($customer->getEmail(), $this->getRequest()->getPost('current_password'), $this->getRequest()->getPost('password'), $this->getRequest()->getPost('confirmation'));
//                    if (!$isPassChanged) {
//                        $this->_getSession()->addError($this->__('Cannot save the customer.'));
//                        $this->_redirect('*/*/edit');
//                        return $this;
//                    } else {
//                        //en caso que se haya cambiado la contraseña, también encriptar
//                        $encryptedPass = Mage::helper('uruguayancustomer')->c_encrypt($this->getRequest()->getPost('password'), PASS_ENCRYPT_SALT);
//                        Mage::getSingleton('customer/session')->setData('encrypted_password', $encryptedPass);
//                    }
//                }
//                $customer->save();
//                $this->_getSession()->setCustomer($customer)
//                    ->addSuccess($this->__('The account information has been saved.'));
//                $this->_redirect('customer/account');
//                return;
//            } catch (Mage_Core_Exception $e) {
//                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
//                    ->addError($e->getMessage());
//            } catch (Exception $e) {
//                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
//                    ->addException($e, $this->__('Cannot save the customer.'));
//            }
//        }
//        $this->_redirect('*/*/edit');
//    }
//
//    /**
//     * Display reset forgotten password form
//     *
//     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
//     *
//     */
//    public function resetPasswordAction()
//    {
//        $resetPasswordLinkToken = (string)$this->getRequest()->getQuery('token');
//        $customerId = (int)$this->getRequest()->getQuery('id');
//        try {
//            $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
//            $this->loadLayout();
//            // Pass received parameters to the reset forgotten password form
//            $this->getLayout()->getBlock('resetPassword')
//                ->setCustomerId($customerId)
//                ->setResetPasswordLinkToken($resetPasswordLinkToken);
//            $this->renderLayout();
//        } catch (Exception $exception) {
//            $this->_getSession()->addError($this->_getHelper('customer')->__('Your password reset link has expired.'));
//            $this->_redirect('*/*/forgotpassword');
//        }
//    }
//
//    /**
//     * Forgot customer password page
//     */
//    public function forgotPasswordAction()
//    {
//        //check if callcenter user is present
//        $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')
//            ->getSessionData('adminhtml');
//        $adminUser = $adminhtml['admin']['user'];
//        $isAdminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//        //and redirect
//        if ($isAdminLoggedIn) {
//            $this->_redirectReferer();
//            return;
//        }
//
//
//        $this->loadLayout();
//
//        $this->getLayout()->getBlock('forgotPassword')->setEmailValue(
//            $this->_getSession()->getForgottenEmail()
//        );
//        $this->_getSession()->unsForgottenEmail();
//
//        $this->_initLayoutMessages('customer/session');
//        $this->renderLayout();
//    }
//
//    /**
//     * change action Forgot customer password
//     */
//    public function forgotPasswordPostAction()
//    {
//        //check if callcenter user is present
//        $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')
//            ->getSessionData('adminhtml');
//        $adminUser = $adminhtml['admin']['user'];
//        $isAdminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//        //and redirect
//        if ($isAdminLoggedIn) {
//            $this->_redirectReferer();
//            return;
//        }
//
//        $customer_email_or_doc = (string)$this->getRequest()->getPost('email_or_doc');
//
//        if ($customer_email_or_doc) {
//            if (!Zend_Validate::is($customer_email_or_doc, 'EmailAddress')) {
//                $this->_getSession()->setForgottenEmail($customer_email_or_doc);
//                $customer = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('document_number', $customer_email_or_doc)->getFirstItem();
//            } else {
//                $customer = $this->_getModel('customer/customer')
//                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
//                    ->loadByEmail($customer_email_or_doc);
//            }
//            /** @var $customer Mage_Customer_Model_Customer */
//            if ($customer->getId()) {
//                //user exists
//                $sToken = $this->forgotPasswordGeoloyaltyCustomer($customer->getEmail());
//                if (!$sToken) {
//                    $this->_getSession()->addError($this->__('Ha ocurrido un error.'));
//                    $this->_redirect('*/*/forgotpassword');
//                    return;
//                } else {
//                    try {
//                        $newResetPasswordLinkToken = $this->_getHelper('customer')->generateResetPasswordLinkToken();
//                        //save both magento and geo tokens in new table;
//                        $resetTokenCompatibleModel = Mage::getModel("ResetTokenCompatible/ResetTokenCompatible");
//                        $resetTokenCompatibleModel->setGeoToken($sToken)->setCreatedTime(date("Y-m-d H:i:s"));
//                        $resetTokenCompatibleModel->setData("mage_token", $newResetPasswordLinkToken);
//                        $resetTokenCompatibleModel->save();
//
//                        $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
//
//                        $customer->sendPasswordResetConfirmationEmail();
//                    } catch (Exception $exception) {
//                        $this->_getSession()->addError($exception->getMessage());
//                        $this->_redirect('*/*/forgotpassword');
//                        return;
//                    }
//                }
//            }
//            $this->_getSession()
//                ->addSuccess($this->_getHelper('customer')
//                    ->__('Si hay una cuenta asociada con %s recibirás un correo electrónico con un enlace para reiniciar tu contraseña.',
//                        $this->_getHelper('customer')->escapeHtml($customer_email_or_doc)));
//            $this->_redirect('*/*/');
//            return;
//        } else {
//            $this->_getSession()->addError($this->__('Please enter your email or document number.'));
//            $this->_redirect('*/*/forgotpassword');
//            return;
//        }
//    }
//
//    /**
//     * Create customer account action
//     */
//    public function createPostAction()
//    {
//
//        /** @var $session Mage_Customer_Model_Session */
//        $session = $this->_getSession();
//        if ($session->isLoggedIn()) {
//            $this->_redirect('*/*/');
//            return;
//        }
//        $session->setEscapeMessages(true); // prevent XSS injection in user input
//        if (!$this->getRequest()->isPost()) {
//            $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
//            $this->_redirectError($errUrl);
//            return;
//        }
//
//        //
//        $city = $this->getRequest()->getPost('city');
//        if (empty($city))
//            $this->getRequest()->setPost('city', '00');
//        //
//
//        $customer = $this->_getCustomer();
//        try {
//            $errors = $this->_getCustomerErrors($customer);
//
//            if(!$this->checkUniqueDocument($this->getRequest()->getPost('document_number'))){
//                $errors[] = ERROR_DOCUMENT_IN_USE;
//            }
//
//            /*if($this->getRequest()->getPost('latitude') && $this->getRequest()->getPost('longitude')) {
//                $json_position=array();
//                $json_position['latitude'] = $this->getRequest()->getPost('latitude');
//                $json_position['longitude'] = $this->getRequest()->getPost('longitude');
//                $customer->setData('position_json',json_encode($json_position));
//            }*/
//            if (empty($errors)) {
//                //if no errors, try creating geoloyaltyCustomer before saving magento customer
//                if ($this->createGeoloyaltyCustomer()) {
//                    $customer->save();
//
//                    //save encrypted password in session
//                    $encryptedPass = Mage::helper('uruguayancustomer')->c_encrypt($this->getRequest()->getPost('password'), PASS_ENCRYPT_SALT);
//                    Mage::getSingleton('customer/session')->setData('encrypted_password', $encryptedPass);
//
//                    $this->_dispatchRegisterSuccess($customer);
//                    $this->_successProcessRegistration($customer);
//                    //sync user data.
//                    $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
//                    $adminUser = $adminhtml['admin']['user'];
//                    $isAdminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//                    $updated = Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty($customer->getId(), $isAdminLoggedIn);
//                    if (!$updated) {
//                        throw new Exception();
//                    }
//                    return;
//                } else {
//                    $errors[] = $this->__('Cannot save the customer.');
//                    $this->_addSessionError($errors);
//                }
//            } else {
//                $this->_addSessionError($errors);
//            }
//        } catch (Mage_Core_Exception $e) {
//            $session->setCustomerFormData($this->getRequest()->getPost());
//            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
//                //$url = $this->_getUrl('customer/account/forgotpassword');
//                //$message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
//                $message = $e->getMessage();
//                $session->setEscapeMessages(false);
//            } else {
//                $message = $e->getMessage();
//            }
//            $session->addError($message);
//        } catch (Exception $e) {
//            $session->setCustomerFormData($this->getRequest()->getPost())
//                ->addException($e, $this->__('Cannot save the customer'));
//        }
//        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
//        $this->_redirectError($errUrl);
//    }
//
//
//    function checkUniqueDocument($documentNumber)
//    {
//        $collection = Mage::getModel('customer/customer')
//            ->getCollection();
//
//        $collection->addAttributeToSelect('document_number')
//            ->addAttributeToFilter('document_number', $documentNumber);
//        $result = $collection->load();
//        if (is_object($result)) {
//            if (count($result->getItems()) != 0 && ($this->_getSession() == null || $this->_getSession()->getCustomer() == null || $this->_getSession()->getCustomer()->getId() == null)) {
//                return false;
//            }
//            if (count($result->getItems()) != 0 && $this->_getSession() != null && $this->_getSession()->getCustomer() != null && $this->_getSession()->getCustomer()->getId() != null) {
//                $sameCustomer = false;
//                if (count($result->getItems()) == 1 && $result->getFirstItem()->getId() == $this->_getSession()->getCustomer()->getId()) {
//                    $sameCustomer = true;
//                }
//                return $sameCustomer;
//            }
//        }
//        return true;
//    }
//
//    /*
//     *
//     */
//    private function signInGeoloyatyCustomer($emailOrDoc, $callcenter = false, $password = '')
//    {
//        $uri = WS_BASE_URL . WS_SIGN_IN;
//        if (strpos($emailOrDoc, '@') !== false) {
//            $data = array('email' => $emailOrDoc, 'password' => $password, "sourceType" => "ECOMMERCE");
//        } else {
//            $data = array('docNumber' => $emailOrDoc, 'docType' => 'CI', 'email' => '', 'password' => $password, "sourceType" => "ECOMMERCE");
//        }
//        $data['sourceType'] = ($callcenter) ? "DELIVERY" : "ECOMMERCE";
//        $data['password'] = ($callcenter) ? "" : $password;
//        try {
//            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
//            if (($response->body) && ($response->body->response->responseCode === 0)) {
//                //set token
//                $stoken = $response->body->sessionToken;
//                Mage::getSingleton('customer/session')->setData('session_token', $stoken);
//                return true;
//            }
//            return false;
//        } catch (Exception $e) {
//            return false;
//        }
//    }
//
//    /*
//     * returns session_token or false
//     */
//    private function forgotPasswordGeoloyaltyCustomer($email)
//    {
//        $uri = WS_BASE_URL . WS_FORGOT_PASSWORD;
//        //{"email":"pepe@gmail.com", "sourceType":"PORTAL_WEB"}
//        $data = array('email' => $email, "sourceType" => "PORTAL_WEB");
//        try {
//            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
//
//            return (($response->body) ? ($response->body->sessionToken ? $response->body->sessionToken : false) : false);
//        } catch (Exception $e) {
//            return false;
//        }
//    }
//
//    private function resetPasswordGeoloyaltyCustomer($sToken, $newPass)
//    {
//        $uri = WS_BASE_URL . WS_RESET_PASSWORD;
//        //{"sessionToken":"1234ASDV", "newPassword":"newPass1234", "sourceType":"PORTAL_WEB"}
//        $data = array('sessionToken' => $sToken, 'newPassword' => $newPass, "sourceType" => "PORTAL_WEB");
//        try {
//            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
//            return (($response->body) ? ($response->body->response->responseCode === 0) : false);
//        } catch (Exception $e) {
//            return false;
//        }
//    }
//
//    private function changePasswordGeoloyaltyCustomer($email, $oldPass, $newPass, $newPassConfirmation)
//    {
//        //{"email":"pepe@gmail.com", "oldPassword":"password1234","newPassword":"newPass1234", "retryNewPassword":"newPass1234","sourceType":"PORTAL_WEB"}
//        $uri = WS_BASE_URL . WS_CHANGE_PASSWORD;
//        $data = array('email' => $email, "oldPassword" => $oldPass,
//            "newPassword" => $newPass, "retryNewPassword" => $newPassConfirmation,
//            "sourceType" => "PORTAL_WEB");
//        try {
//            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
//
//            return (($response->body) ? ($response->body->response->responseCode === 0) : false);
//        } catch (Exception $e) {
//            return false;
//        }
//    }
//
//    private function createGeoloyaltyCustomer()
//    {
//
//        $uri = WS_BASE_URL . WS_SIGN_UP;
//        $complement = ($this->getRequest()->getPost('complement')) ? $this->getRequest()->getPost('complement') : "";
//        $latitude = '';
//        $longitude = '';
//        if ($this->getRequest()->getPost('latitude') && $this->getRequest()->getPost('longitude')) {
//            $latitude = $this->getRequest()->getPost('latitude');
//            $longitude = $this->getRequest()->getPost('longitude');
//        }
//        $docNumber = $this->getRequest()->getPost('document_number');
//        $docType = $this->getRequest()->getPost('document_type');
//        $mname = $this->getRequest()->getPost('middle_name');
//        $email = $this->getRequest()->getPost('email');
//        $fname = $this->getRequest()->getPost('firstname');
//        $lname = $this->getRequest()->getPost('lastname');
//        $day = $this->getRequest()->getPost('day');
//        $month = $this->getRequest()->getPost('month');
//        $year = $this->getRequest()->getPost('year');
//        $dob = $year . "-" . $month . "-" . $day;
//        $gender = $this->getRequest()->getPost('gender');
//        $gender_char = ($gender == 1) ? "M" : "F";
//        $country = $this->getRequest()->getPost('country_id');
//        $street = $this->getRequest()->getPost('street')[0];
//        $addressNumber = $this->getRequest()->getPost('address_number');
//        //$addressCorner= $this->getRequest()->getPost('address_corner');
//        $password = $this->getRequest()->getPost('password');
//        $telephone = $this->getRequest()->getPost('telephone');
//
//        //$neighborhood=$this->getRequest()->getPost('neighborhood');
//        $cityId = $this->getRequest()->getPost('city_id');
//        //$city=$this->getRequest()->getPost('city');
//        $departmentId = $this->getRequest()->getPost('geo_region_id');
//        //$department=$this->getRequest()->getPost('region');
//        $data = array(
//            'docType' => $docType,
//            'docNumber' => $docNumber,
//            'firstName' => $fname,
//            'firstSurname' => $lname,
//            'gender' => $gender_char,
//            'country' => $country,
//            'secondName' => $mname,
//            'email' => $email,
//            'birthdate' => $dob,
//            'telephones' => array(array("type" => "CELL", "number" => $telephone)),
//            'businessUnitId' => "1",
//            "requiredOutlineProcess" => "false",
//            "password" => $password,
//            "sourceType" => "ECOMMERCE"
//        );
//        $address = (array("longitude" => $longitude, "latitude" => $latitude, "complement" => $complement, "streetName" => $street, "streetNumber" => $addressNumber, "city" => array("id" => $cityId), "department" => array("id" => $departmentId)));
//        $neighborhoodId = $this->getRequest()->getPost('neighborhood_id');
//        if ($neighborhoodId) $address['neighborhood'] = array("id" => $neighborhoodId);
//        $data['addresses'] = array($address);
//        try {
//            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
//            if ($response->body && $response->body->response->responseCode == 0) {
//                //if user afilliated OK then get Session token
//                $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
//                $adminUser = $adminhtml['admin']['user'];
//                $isAdminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//                $geoSignedIn = $this->signInGeoloyatyCustomer($email, $isAdminLoggedIn, $password);
//                return ($geoSignedIn);
//            }else if ($response->body && $response->body->response->responseCode == 36){
//                throw Mage::exception('Mage_Customer', ERROR_EMAIL_IN_USE ,3);
//            }
//        } catch (Mage_Core_Exception $e) {
//            throw $e;
//        }
//        return false;
//    }
//
//    /**
//     * Forgot customer account information page
//     */
//    public function editAction()
//    {
//        $this->loadLayout();
//        $this->_initLayoutMessages('customer/session');
//        $this->_initLayoutMessages('catalog/session');
//
//        $block = $this->getLayout()->getBlock('customer_edit');
//        if ($block) {
//            $block->setRefererUrl($this->_getRefererUrl());
//        }
//        $data = $this->_getSession()->getCustomerFormData(true);
//        $customer = $this->_getSession()->getCustomer();
//        if (!empty($data)) {
//            $customer->addData($data);
//        }
//        if ($this->getRequest()->getParam('changepass') == 1) {
//            $customer->setChangePassword(1);
//        }
//        $c = Mage::getSingleton('customer/session')->getCustomer();
//        //$encryptedPass=Mage::getSingleton('customer/session')->getData('encrypted_password');
//        //$decrytedPass=Mage::helper('uruguayancustomer')->c_decrypt($encryptedPass,PASS_ENCRYPT_SALT);
//        //$customerData=Mage::getModel('uruguayancustomer/customer')->getGeoLoyaltyCustomerData($c->getData("email"),$decrytedPass);
//        $token = Mage::getSingleton('customer/session')->getData('session_token');
//        $customerData = Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token,$this->_isAdminLoggedIn());
//        if ($customerData) {
//            //preload de datos de customer desde geo
//            //$customer->setData("document_number",$customerData->clientId->number);
//            //$customer->setData("email",$customerData->clientId->number);
//            $customer->setData("firstname", $customerData->name->firstName);
//            $customer->setData("middle_name", $customerData->name->secondName);
//            //$customer->setData("telephone",$customerData->name->firstName);
//            $customer->setData("lastname", $customerData->name->firstSurname);
//            //$customer->setData("document_type",$customerData->clientId->type);
//            $gender = ($customerData->gender === "M") ? 1 : 2;
//            $customer->setData("gender", $gender);
//            $dob = Date("Y-m-d H:i:s", $customerData->birthdate / 1000);
//            $customer->setData("dob", $dob);
//            try {
//                $customer->save();
//            } catch (Exception $e) {
//                //log($e);
//            }
//            $block->setGeoLoyaltyCustomerDataLoaded(true);
//        } else {
//            $customer = null;
//            $this->_getSession()->addError("Ha ocurrido un error. Intentelo de nuevo más tarde.");
//            $block->setGeoLoyaltyCustomerDataLoaded(false);
//            $this->_redirect('*/*');
//        }
//
//        $block->getCustomer()->getPrimaryBillingAddress();
//
//        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Information'));
//        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
//        $this->renderLayout();
//
//
//    }
//
//    /**
//     * Customer logout action
//     */
//    public function logoutAction()
//    {
//        parent::logoutAction();
//
//        $session = Mage::getSingleton('customer/session');
//        $isLocalDefined = $session->getData('is_local_defined');
//
//        if (isset($isLocalDefined)) {
//            $session->unsetData('is_local_defined');
//            $session->unsetData('delivery_local_id');
//            $session->unsetData('delivery_address_id');
//        }
//    }

}
				