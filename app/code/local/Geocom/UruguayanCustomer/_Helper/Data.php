<?php
class Geocom_UruguayanCustomer_Helper_Data extends Mage_Core_Helper_Abstract
{

    function c_encrypt($text,$salt)
    {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    function c_decrypt($text,$salt)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

    function getDeliveryScheduleByLocal($idLocal,$dayNumber,$hour){
        $deliverySchedule = unserialize (HORARIOS_DISPONIBLES_LOCALES);
        $deliveryScheduleData=array();
        if($deliverySchedule && $deliverySchedule[(int)$idLocal]){
                //$hour=($hour==23)?0:$hour;//fix
                $dslocal = $deliverySchedule[(int)$idLocal];
                $todayConverted = $this->convertDayNumberToString($dayNumber);
                foreach ($dslocal as $dsl) {
                    if ($dsl["hora_inicio"] > $hour + 1 && (in_array($todayConverted, $dsl['dias']) || in_array('Todos', $dsl['dias']))) {
                        //format display string
                        $formattedString = $dsl["hora_inicio"] . ':'.$dsl["minutos_inicio"].' a ' . $dsl["hora_fin"] . ':'.$dsl["minutos_inicio"].' Hs.';
                        //insert in array
                        $deliveryScheduleData[] = $formattedString;
                    }
                }
            }
        return $deliveryScheduleData;
    }


    /*
     * converts a day number ie: 0 to "D" for Domingo, 1 to "L" for Lunes.
     * returns string.
     */
    function convertDayNumberToString($dayNumber){
        if($dayNumber==0){
            return 'D';
        }else if($dayNumber==1){
            return 'L';
        }elseif($dayNumber==2){
            return 'Ma';
        }elseif($dayNumber==3){
            return 'Mi';
        }elseif($dayNumber==4){
            return 'J';
        }elseif($dayNumber==5){
            return 'V';
        }else{
            return 'S';
        }
    }

}
	 