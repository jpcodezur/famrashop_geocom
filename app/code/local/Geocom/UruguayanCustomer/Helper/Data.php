<?php
class Geocom_UruguayanCustomer_Helper_Data extends Cualit_UruguayanCustomer_Helper_Data{

    function getDeliveryScheduleByLocal($idLocal,$dayNumber,$hour){
        $deliverySchedule = unserialize (HORARIOS_DISPONIBLES_LOCALES);
        $deliveryScheduleData=array();
        if($deliverySchedule && $deliverySchedule[(int)$idLocal]){
                //$hour=($hour==23)?0:$hour;//fix
                $dslocal = $deliverySchedule[(int)$idLocal];
                $todayConverted = $this->convertDayNumberToString($dayNumber);
                foreach ($dslocal as $dsl) {
                    if ($dsl["hora_inicio"] > $hour + HORA_INCREMENTO_DELIVERY && (in_array($todayConverted, $dsl['dias']) || in_array('Todos', $dsl['dias']))) {
                        //format display string
                        $formattedString = $dsl["hora_inicio"] . ':'.$dsl["minutos_inicio"].' a ' . $dsl["hora_fin"] . ':'.$dsl["minutos_inicio"].' Hs.';
                        //insert in array
                        $deliveryScheduleData[] = $formattedString;
                    }
                }
            }
        return $deliveryScheduleData;
    }
}
	 