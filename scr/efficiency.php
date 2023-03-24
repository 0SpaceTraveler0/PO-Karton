<?php
require_once(__DIR__ . '/bitrix_request.php');
//Нужное изделие и только оно на ширину материала
//первое значение остатака от первого заказа в массиве $all_order
function efficiency($all_order, $widthMaterial){
    $use_order = [];
    $result_array = [];
    $id_other_order = 0;
    $order_selection = $all_order; 
    $all_name_material = material_get();
    $res = 0;
    foreach ($all_order as $key2 => &$order) {
        foreach ($widthMaterial as $width) {
            foreach ($order_selection as $key => $value_select) {                
                if ($order['material'] == $value_select['material'] && ($order['width'] + $value_select['width']) <= $width && $order['efficiency']['efficiency2']['efficiency'] == 0) {

                    $remainder = ($order['width'] + $value_select['width']);
                    $efficiency = ceil((($remainder / $width) * 100));

                    $order['efficiency']['efficiency2']['efficiency'] = $efficiency;
                    $order['efficiency']['efficiency2']['width_material'] = $width;
                    $order['efficiency']['efficiency2']['id_order'] = $value_select['id'];
                    $order['efficiency']['efficiency2']['remaining_quantity'] = $order['quantity'] - $value_select['quantity'];                        
                    continue;
                }
                if ($order['material'] == $value_select['material'] && ($order['width'] + $value_select['width']) <= $width) {
                    $remainder = ($order['width'] + $value_select['width']);
                    $efficiency = ceil((($remainder / $width) * 100));
                    if ($order['efficiency']['efficiency2']['efficiency'] != 0 && $order['efficiency']['efficiency2']['efficiency'] < $efficiency) {
                        $order['efficiency']['efficiency2']['efficiency'] = $efficiency;
                        $order['efficiency']['efficiency2']['width_material'] = $width;
                        $order['efficiency']['efficiency2']['id_order'] = $value_select['id'];
                        $order['efficiency']['efficiency2']['remaining_quantity'] = $order['quantity'] - $value_select['quantity']; 
                        continue;
                    }
                }
                if ($order['material'] == $value_select['material'] && (($order['width'] * 2) + $value_select['width']) <= $width && $order['efficiency']['efficiency3']['efficiency'] == 0 && !in_array($order['id'], $use_order)) {

                    $remainder = (($order['width'] * 2) + $value_select['width']);
                    $efficiency = ceil((($remainder / $width) * 100));

                    $order['efficiency']['efficiency3']['efficiency'] = $efficiency;
                    $order['efficiency']['efficiency3']['width_material'] = $width;
                    $order['efficiency']['efficiency3']['id_order'] = $value_select['id'];
                    $order['efficiency']['efficiency3']['remaining_quantity'] = $order['quantity'] - $value_select['quantity']; 
                    continue;
                }
                if ($order['material'] == $value_select['material'] && (($order['width'] * 2) + $value_select['width']) <= $width && !in_array($order['id'], $use_order)) {
                    $remainder = (($order['width'] * 2) + $value_select['width']);
                    $efficiency = ceil((($remainder / $width) * 100));
                    if ($order['efficiency']['efficiency3']['efficiency'] != 0 && $order['efficiency']['efficiency3']['efficiency'] < $efficiency) {
                        $order['efficiency']['efficiency3']['efficiency'] = $efficiency;
                        $order['efficiency']['efficiency3']['width_material'] = $width;
                        $order['efficiency']['efficiency3']['id_order'] = $value_select['id'];
                        $order['efficiency']['efficiency3']['remaining_quantity'] = $order['quantity'] - $value_select['quantity'];
                        array_push($use_order, $order['id']);
                        continue;
                    }
                }
            }
        }
        
        $eff = $order['efficiency']['efficiency1']['efficiency'];
        foreach ($order['efficiency'] as $item){
            if($eff < $item['efficiency']){
                $eff = $item['efficiency'];
                $width = $item['width_material'];
                $remaining_quantity = $item['remaining_quantity'];
                $id_other_order = $item['id_order'];
            }
        }

        array_push($result_array, [
            "id" => $order['id'],
            "shipping_date" => $order['shipping_date'],
            "width" => $width,
            "quantity" => $order['quantity'],
            "remaining_quantity" => $remaining_quantity,
            "efficiency" =>  $eff,
            "id_other_order" =>  $id_other_order,
            "customer" => company_get($order['customer']),
            "material" => $all_name_material[$order['material']],

        ]);

        $res = array_search($id_other_order, array_map(function($v){return $v['id'];},$all_order));
        $res2 = array_search($order['id'], array_map(function($v){return $v['id'];},$result_array));

        unset($all_order[$res]);
        unset($order_selection[$res]);
    }
    return $result_array;
}

function efficiency1($all_order, $widthMaterial)
{
    foreach ($all_order as &$order) {
        foreach ($widthMaterial as $width) {
            if ($order['width']<= $width) {
                //считаем остаток от длины матреиала
                $remainder = $width - ($order['width']);
                //высчитываем проценты
                $efficiency = ($remainder / $width) * 100;
                $order['efficiency']['efficiency1']['efficiency'] = $efficiency;
                $order['efficiency']['efficiency1']['width_material'] = $width;
                break;
            }
        }
    }
    return $all_order;
}