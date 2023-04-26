<?php
require_once(__DIR__ . '/bitrix_request.php');

function calculateEfficiency(&$all_order, $widthMaterial)
{
    $all_name_material = getMaterials();
    $result_array= [];
    $copy_key_cush = 0;
    foreach ($all_order as $key => &$order){
        $x = 0;
        $remaining_length = 0;
        while($order['quantity'] > 0 and $order['quantity'] != 0 and $x != 5){
            $x++;
            foreach ($all_order as $copy_key => $copy_order){
                if($copy_order['quantity'] == 0){
                    continue;
                }
                if($order['material'] != $copy_order['material']){continue;}
                foreach ($widthMaterial as $width_mat){
                    if($order['width'] > $width_mat){continue;}
                    for ($i = 1, $width_order_i = $order['width']*$i; $i <= 3; $i++){
                        if ($width_order_i > $widthMaterial){continue;}
                        for ($y = 1; $y <= 5; $y++){
                            if(($width_order_i + $copy_order['width'] * $y) <=  $width_mat){
                                $remainder = ($width_order_i + $copy_order['width'] * $y);
                                $efficiency = (($remainder / $width_mat) * 100);

                                if($width_mat == 1050 and $order['percent'] >= $efficiency-5){
                                    continue;
                                }
                                if($order['width_material'] == 1050 and $order['percent']-5 <= $efficiency){
                                    $order['percent'] = $order['percent']-5;
                                }
                                if($width_mat == 840 and $order['percent'] >= $efficiency-8){
                                    continue;
                                }
                                if($order['width_material'] == 840 and $order['percent']-8 <= $efficiency){
                                    $order['percent'] = $order['percent']-8;
                                }
                                

                                if($order['percent'] < $efficiency){
                                    //$order['percent'] = round($efficiency,2);
                                    $order['percent'] = $efficiency;
                                    $order['width_material'] = $width_mat;
                                    $order['combined_id_passport'] = $copy_order['id'];
                                    $order['main_order_quantity_widtht'] = $i;
                                    $order['combined_order_quantity_widtht'] = $y;
                                    $remaining_length = ($order['quantity']*$order['length'])/$i - ($copy_order['quantity']*$copy_order['length'])/$y;
                                    $length_copy_order = $copy_order['length'];
                                    $copy_key_cush = $copy_key;
                                }
                            }
                        }
                    }
                }
            }
            if($remaining_length == 0){
                $order['quantity'] = 0;$all_order[$copy_key_cush]['quantity'] = 0;
            }
            if($remaining_length > 0){
                $order['quantity'] = ceil($remaining_length/$order['length']*$order['main_order_quantity_widtht']);$all_order[$copy_key_cush]['quantity'] = 0;
            }
            if($remaining_length < 0){
                $all_order[$copy_key_cush]['quantity'] = ceil(($remaining_length*-1)/$length_copy_order*$order['combined_order_quantity_widtht']);$order['quantity'] = 0;}
            if($order['percent'] != 0){array_push($result_array, [
                "id" => $order['id'],
                "combined_id_passport" =>  $order['combined_id_passport'],
                "customer" => getCompany($order['customer']),
                "customer_id" => $order['customer'],
                "shipping_date" => $order['shipping_date'],
                "efficiency" =>  $order['percent'],
                "width" => $order['width_material'],
                "material" => $all_name_material[$order['material']],
                "items_per_plan" => $order['items_per_plan'],
                "remaining_quantity" => $order['quantity'],
                "remaining_quantity_copy_order" => $all_order[$copy_key_cush]['quantity'],
                "main_order_quantity_widtht" => $order['main_order_quantity_widtht'],
                "combined_order_quantity_widtht" => $order['combined_order_quantity_widtht'],
                "urgent" => $order['urgent'],
                "flag" => 0,
            ])
            ;}
            /* if($remaining_length == 0){
                unset ($all_order[$key]);
                unset($all_order[$copy_key_cush]);
            }
            if($remaining_length > 0){
                unset($all_order[$copy_key_cush]);
            }
            if($remaining_length < 0){
                unset ($all_order[$key]);
            } */
            $order['percent'] = 0;}
    }
    return $result_array;
}
