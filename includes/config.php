<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once('messages.php');


#define('COMPILER_INCLUDE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'src');
#define('COMPILER_COLLECT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'stat');
//---------GUEST CHECKOUT-----------------------------//
define("GUEST_DEFAULT_LASTNAME","N/A");
define("GUEST_DEFAULT_ZIPCODE","N/A");
define("GUEST_DEFAULT_EMAIL","no-reply@farmashop.com");
define("GUEST_DEFAULT_DOCUMENT","N/A");
//---------callcenter constants----------------------//
define("CALLCENTER_ROLE_NAME","callcenter");
//----------pass encryption--------------------------//
define("PASS_ENCRYPT_SALT","&3(kDIMH7Xl^Dvh@");
//----------GeoLoyalty webservices constants--------//
define('WS_GET_STOCK','http://192.168.1.182:8080/articulosFarmashopWS/StockService');
define('WS_GEO_ALREADY_AFFILIATED_RESPONSE_CODE', 29);
define('WS_GEO_INVALID_DOC_NUMBER_RESPONSE_CODE', 26);
define('WS_GEO_ALREADY_SAME_EMAIL', 36);
define('WS_BASE_URL','http://www.geocom.com.uy:8991/');
define('WS_GET_DOC_TYPES','geoloyalty-mobile-service/mobile/entity/getDocTypes');
define('WS_UPDATE_CLIENT','geoloyalty-mobile-service/mobile/client/updateClient');
define('WS_GET_NATIONALITIES','geoloyalty-mobile-service/mobile/entity/getNationalities');
define('WS_GET_DEPARTMENTS','geoloyalty-mobile-service/mobile/entity/getDepartments');
define('WS_GET_CITIES','geoloyalty-mobile-service/mobile/entity/getCities');
define('WS_GET_NEIGHBORHOODS','geoloyalty-mobile-service/mobile/entity/getNeighborhoods');
define('WS_SIGN_IN','geoloyalty-mobile-service/mobile/authorization/login');
define('WS_SIGN_UP','geoloyalty-mobile-service/mobile/client/setCompleteSubscriptionToLoyaltyProgram');
define('WS_FORGOT_PASSWORD','geoloyalty-mobile-service/mobile/authorization/forgotPassword');
define('WS_CHANGE_PASSWORD','geoloyalty-mobile-service/mobile/authorization/updatePassword');
define('WS_RESET_PASSWORD','geoloyalty-mobile-service/mobile/authorization/resetPassword');
define('WS_BALANCE_QUERY','geoloyalty-mobile-service/mobile/account/balanceQuery');
define('WS_CLIENT_DATA','geoloyalty-mobile-service/mobile/client/getClient');
define('WS_REDEEM_POINTS','geoloyalty-mobile-service/mobile/account/redeemPoints');
//------Geo Address--------------------------------//
define('WS_CLOSEST_LOCALS','http://172.26.26.93:8080/farmashop_georeferenciacion/servlet/awsgetsucursalpordistancia?wsdl');
define('WS_GET_ADDRESS_LAT_LNG','http://172.26.26.93:8080/farmashop_georeferenciacion/servlet/awsgeocoding?wsdl');
//---------Custom service--------------------------//
define("WS_GEO_GET_SUG_ADDRESSES","geoproxy/index/getSuggestedAddresses");
define("WS_GEO_GET_GMAP_DATA","geoproxy/index/getGoogleMapsData");
define('WS_GEO_GET_DOC_TYPES','geoproxy/index/getDocTypes');
define('WS_GEO_GET_NATIONALITIES','geoproxy/index/getNationalities');
define('WS_GEO_GET_DEPARTMENTS','geoproxy/index/getDepartments');
define('WS_GEO_GET_CITIES','geoproxy/index/getCities/depId/');// + param
define('WS_GEO_GET_NEIGHBORHOODS','geoproxy/index/getNeighborhoods/cityId/');// + param
//---------info mail constants---------------------//
define("INFO_MAIL_TO","nicocarnebia@gmail.com");
define("INFO_NAME_TO","name_to");
define("INFO_MAIL_FROM","nicocarnebia@gmail.com");
define("INFO_NAME_FROM","info efarmashop");
//----------Payment button constants----------------/

define("KEYS_FOLDER_PATH","../keys/");
define("PUBLIC_KEY_FILE",KEYS_FOLDER_PATH."geoPublicKey.txt");
define("PRIVATE_KEY_FILE",KEYS_FOLDER_PATH."cualitPrivate.asc");
define("PRIVATE_KEY_PASS","cu4l1t0k");
define("GEO_DIGITAL_SIGN_PUBLIC_KEY_FILE",KEYS_FOLDER_PATH."geoDigitalSignPublicKey.txt");
define("CUALIT_DIGITAL_SIGN_PRIVATE_KEY_FILE",KEYS_FOLDER_PATH.'cualitDigitalSignPrivKey.pem');
define("PGENCRYPT_JAR","../PaymentButtonGatewayEncryptionProject/exported executables/pgpencrypt.jar");
define("PAYMENT_BUTTON_PURCHASE_URL","https://botondepago.geocom.com.uy:9443/paymentbutton/purchasedata");
define("PAYMENT_BUTTON_RESPONSE_URL","http://ecomercefarmashop.geocom.com.uy/index.php/paymentbuttongateway/index/response");
//--------------------------------------------------/


// API Servicios de Despacho
define("GEOAPI_TERMINAL_WEB", "term1");
define("GEOAPI_LOCAL_WEB", "local1");
// Tiempo de expiración en minutos para volver pedidos bloqueados a pending
define("GEOAPI_TIEMPO_EXPIRACION", 1);


//Local por default
define("DEFAULT_LOCAL_ID",1);
define("DEFAULT_MAP_LATITUDE",-34.9160151);
define("DEFAULT_MAP_LONGITUDE",-56.1604625);

// Locales de Farmashop
define("FARMASHOP_LOCALES", serialize(array(
    15 => "Punta carretas", 
    99 => "Testing GEOCOM", 
)));

// Convenios
define("FARMASHOP_CONVENIOS", serialize(array(
    '30' => array('descripcion' => "Blue Cross 40%", 'requiere_socio' => true, 'requiere_timbre' => true, 'precio_timbre' => 18, 'sku' => '11843'),
    '31' => array('descripcion' => "Blue Cross 100%", 'requiere_socio' => true, 'requiere_timbre' => true, 'precio_timbre' => 18, 'sku' => '11843'),
    '32' => array('descripcion' => "Blue Cross $240", 'requiere_socio' => true, 'requiere_timbre' => true, 'precio_timbre' => 18, 'sku' => '11843'),
    '312' => array('descripcion' => "MP", 'requiere_socio' => true, 'requiere_timbre' => true, 'precio_timbre' => 36, 'sku' => ''),
    '10' => array('descripcion' => "Farmadescuento", 'requiere_socio' => false, 'requiere_timbre' => true, 'precio_timbre' => 18, 'sku' => '11843')
)));
// Requerimiento Receta
define("FARMASHOP_REQUERIMIENTO_RECETA",serialize(array(
   5 => false, //1 - Sin receta
   4 => true,  //2 - Sin/Con receta
   3 => true   //3 - Con receta
)));

// Control de stock
define("STOCK_HELP_DESK", "fpinvidio@gmail.com");
define("STOCK_CHECK_URL", "http://192.168.248.115:8080/articulosFarmashopWS/StockService?wsdl");



//Tiempo minimo de entrega
define("MIN_DELIVERY_TIME_IN_DAYS",7);
// Horarios dispnobiles
//hora_inicio : int 0-23
//hora_fin : int 0-23
//minutos_inicio : int 0-59
//minutos_fin : int 0-59
//dias : string array -> "L"=>Lunes,"Ma"=>Martes, "Mi"=>Miercoles, "J"=>Jueves, "V"=>Viernes, "S"=>Sabado, "D"=>Domingo, "Todos" =>Todos los dias
define("HORARIOS_DISPONIBLES_LOCALES", serialize(
    array(11=>array(
        array("hora_inicio" => '00', "minutos_inicio"=>'30',"hora_fin" => '1',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '01', "minutos_inicio"=>'30',"hora_fin" => '2',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '10',"minutos_inicio"=>'00',"hora_fin" => '11',"minutos_fin"=>'00',"dias"=>array('L','Ma','Mi','J','V','S','D')),
        array("hora_inicio" => '11', "minutos_inicio"=>'00',"hora_fin" => '12',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '12', "minutos_inicio"=>'00',"hora_fin" => '13',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '13', "minutos_inicio"=>'00',"hora_fin" => '14',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '14', "minutos_inicio"=>'00',"hora_fin" => '15',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '15', "minutos_inicio"=>'00',"hora_fin" => '16',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '16', "minutos_inicio"=>'00',"hora_fin" => '17',"minutos_fin"=>'30',"dias"=>array('Todos')),
        array("hora_inicio" => '19', "minutos_inicio"=>'00',"hora_fin" => '20',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '20', "minutos_inicio"=>'00',"hora_fin" => '21',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '21', "minutos_inicio"=>'00',"hora_fin" => '22',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '22', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '23', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'30',"dias"=>array('V','S','D'))

    ),15=>array(
        array("hora_inicio" => '00', "minutos_inicio"=>'30',"hora_fin" => '1',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '01', "minutos_inicio"=>'30',"hora_fin" => '2',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '10',"minutos_inicio"=>'00',"hora_fin" => '11',"minutos_fin"=>'00',"dias"=>array('L','Ma','Mi','J','V','S','D')),
        array("hora_inicio" => '11', "minutos_inicio"=>'00',"hora_fin" => '12',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '12', "minutos_inicio"=>'00',"hora_fin" => '13',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '13', "minutos_inicio"=>'00',"hora_fin" => '14',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '14', "minutos_inicio"=>'00',"hora_fin" => '15',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '15', "minutos_inicio"=>'00',"hora_fin" => '16',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '16', "minutos_inicio"=>'00',"hora_fin" => '17',"minutos_fin"=>'30',"dias"=>array('Todos')),
        array("hora_inicio" => '19', "minutos_inicio"=>'00',"hora_fin" => '20',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '20', "minutos_inicio"=>'00',"hora_fin" => '21',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '21', "minutos_inicio"=>'00',"hora_fin" => '22',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '22', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '23', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'30',"dias"=>array('V','S','D'))
    ),22=>array(
        array("hora_inicio" => '00', "minutos_inicio"=>'30',"hora_fin" => '1',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '01', "minutos_inicio"=>'30',"hora_fin" => '2',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '10',"minutos_inicio"=>'00',"hora_fin" => '11',"minutos_fin"=>'00',"dias"=>array('L','Ma','Mi','J','V','S','D')),
        array("hora_inicio" => '11', "minutos_inicio"=>'00',"hora_fin" => '12',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '12', "minutos_inicio"=>'00',"hora_fin" => '13',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '13', "minutos_inicio"=>'00',"hora_fin" => '14',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '14', "minutos_inicio"=>'00',"hora_fin" => '15',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '15', "minutos_inicio"=>'00',"hora_fin" => '16',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '16', "minutos_inicio"=>'00',"hora_fin" => '17',"minutos_fin"=>'30',"dias"=>array('Todos')),
        array("hora_inicio" => '19', "minutos_inicio"=>'00',"hora_fin" => '20',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '20', "minutos_inicio"=>'00',"hora_fin" => '21',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '21', "minutos_inicio"=>'00',"hora_fin" => '22',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '22', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '23', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'30',"dias"=>array('V','S','D'))
    ),99=>array(
        array("hora_inicio" => '00', "minutos_inicio"=>'30',"hora_fin" => '1',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '01', "minutos_inicio"=>'30',"hora_fin" => '2',"minutos_fin"=>'30',"dias"=>array('L','Ma','Mi','J','V','S')),
        array("hora_inicio" => '10',"minutos_inicio"=>'00',"hora_fin" => '11',"minutos_fin"=>'00',"dias"=>array('L','Ma','Mi','J','V','S','D')),
        array("hora_inicio" => '11', "minutos_inicio"=>'00',"hora_fin" => '12',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '12', "minutos_inicio"=>'00',"hora_fin" => '13',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '13', "minutos_inicio"=>'00',"hora_fin" => '14',"minutos_fin"=>'00',"dias"=>array('Todos')),
		array("hora_inicio" => '14', "minutos_inicio"=>'00',"hora_fin" => '15',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '15', "minutos_inicio"=>'00',"hora_fin" => '16',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '16', "minutos_inicio"=>'00',"hora_fin" => '17',"minutos_fin"=>'30',"dias"=>array('Todos')),
        array("hora_inicio" => '19', "minutos_inicio"=>'00',"hora_fin" => '20',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '20', "minutos_inicio"=>'00',"hora_fin" => '21',"minutos_fin"=>'00',"dias"=>array('Todos')),
        array("hora_inicio" => '21', "minutos_inicio"=>'00',"hora_fin" => '22',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '22', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'00',"dias"=>array('V','S','D')),
        array("hora_inicio" => '23', "minutos_inicio"=>'00',"hora_fin" => '23',"minutos_fin"=>'30',"dias"=>array('V','S','D'))
    )
)));

