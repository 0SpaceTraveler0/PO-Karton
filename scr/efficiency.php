<?php
require_once(__DIR__ . '/bitrix_request.php');
/*
echo "<pre>";
print_r($order['id'].' - '.$copy_order['id'].' '.$i);
print_r("   ".$width_mat);
echo "</pre>";
ntcn 
*/

function efficiency(&$all_order, $widthMaterial)
{
    $all_name_material = material_get();
    $result_array= []; // массив для результата подбора
    $copy_all_order = $all_order; // копия массива всех заказов
    foreach ($all_order as &$order){ //перебор всех заказов
        $x = 0;
        $remaining_length = 0;
        $remaining_quantity = $order['quantity'];
        while($remaining_quantity > 0 and $remaining_quantity != 0 and $x != 5){ // подбираем заказы, пока количество "основного" $order заказа не кончиться
            $x++;
            $length_copy_order = 0;
            foreach ($widthMaterial as $width_mat){ //перебираем все возможные ширины рулоном
                if($order['width'] <= $width_mat){ //если меньше ширины рулона или равен ширине рулона
                    foreach ($copy_all_order as $copy_order){ // перебираем все заказы для поиска совместимости, для лучшей эффективности/ меньше отрезков
                        $remaining_quantity_copy_order = $copy_order['quantity'];
                        if($order['material'] == $copy_order['material']){
                            for ($i = 1; $i <= 3; $i++) { // основного заказа на рулоне может быть максимум 3
                                $with_order_i = $order['width']*$i; // ширина основного заказа * на возможное его количество на рулоне
                                if($with_order_i < $width_mat){
                                    for ($y = 1; $y <= 5; $y++){
                                        if( ($with_order_i + $copy_order['width'] * $y) <=  $width_mat){
                                            /* ширина основного заказа * на возможное его количество на рулоне +
                                                ширина совмещенного заказа * на возможное его количество на рулоне
                                            */
                                            $remainder = ($with_order_i + $copy_order['width'] * $y);
                                            $efficiency = (($remainder / $width_mat) * 100); // высчитываем процент заказов на рулоне, чем больше процент, тем лучше

                                            /*if($order['width_material'] == 840 and $order['percent'] > $efficiency+8){}
                                            if($order['width_material'] == 1050 and $order['percent'] > $efficiency+5){}*/
                                            if($order['percent'] < $efficiency){
                                                $order['percent'] = $efficiency;
                                                $order['width_material'] = $width_mat;
                                                $order['id_order'] = $copy_order['id'];
                                                $order['main_order_quantity_widtht'] = $i;
                                                $order['combined_order_quantity_widtht'] = $y;
                                                $remaining_length = ($remaining_quantity*$order['length'])/$i - ($remaining_quantity_copy_order*$copy_order['length'])/$y;
                                                $length_copy_order = $copy_order['length'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if($remaining_length > 0){
                $remaining_quantity = ceil($remaining_length/$order['length']*$order['main_order_quantity_widtht']);
                $remaining_quantity_copy_order = 0;
            }
            if($remaining_length < 0){
                $remaining_quantity_copy_order = ceil(($remaining_length*-1)/$length_copy_order*$order['combined_order_quantity_widtht']);
                $remaining_quantity = 0;
            }
            if($order['percent'] != 0){
                array_push($result_array, [
                    "id" => $order['id'],
                    "name" => $order['name'],
                    "scan_length" => $order['length'],
                    "scan_width" => $order['width'],
                    "urgent" => $order['urgent'],
                    "shipping_date" => $order['shipping_date'],
                    "width" => $order['width_material'],
                    "quantity" => $order['quantity'],
                    "remaining_quantity" => $remaining_quantity,
                    "remaining_quantity_copy_order" => $remaining_quantity_copy_order,
                    "efficiency" =>  $order['percent'],
                    "id_order" =>  $order['id_order'],
                    "customer" => company_get($order['customer']),
                    "customer_id" => $order['customer'],
                    "material" => $all_name_material[$order['material']],
                    "main_order_quantity_widtht" => $order['main_order_quantity_widtht'],
                    "combined_order_quantity_widtht" => $order['combined_order_quantity_widtht'],
                ]);
            }
            if($remaining_length > 0){
                //$order['quantity'] = $remaining_quantity;
                $key_copy_order_delete_all_order = array_search($order['id_order'], array_map(function($v){return $v['id'];},$all_order));
                $key_copy_order_delete_copy_all_order = array_search($order['id_order'], array_map(function($v){return $v['id'];},$copy_all_order));
                unset($all_order[$key_copy_order_delete_all_order]);
                unset($copy_all_order[$key_copy_order_delete_copy_all_order]);
            }
            if($remaining_length < 0){
                $key_copy_order = array_search($order['id_order'], array_map(function($v){return $v['id'];},$copy_all_order));
                $copy_all_order[$key_copy_order]['quantity'] = $remaining_quantity_copy_order;
                $key_copy_order_delete_all_order = array_search($order['id'], array_map(function($v){return $v['id'];},$all_order));
                $key_copy_order_delete_copy_all_order = array_search($order['id'], array_map(function($v){return $v['id'];},$copy_all_order));
                unset($all_order[$key_copy_order_delete_all_order]);
                unset($copy_all_order[$key_copy_order_delete_copy_all_order]);
            }

            $order['percent'] = 0;
        }
        /*$key_copy_order_for_deal = array_search($order['id_order'], array_map(function($v){return $v['id'];},$all_order));
        unset($all_order[$key_copy_order_for_deal]);*/
    }

    return $result_array;
}

