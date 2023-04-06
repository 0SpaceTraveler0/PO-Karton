<?php
require_once(__DIR__ . '/bitrix_request.php');
/*
echo "<pre>";
print_r($order['id'].' - '.$copy_order['id'].' '.$i);
print_r("   ".$width_mat);
echo "</pre>";
ntcn 
*/

function efficiency(&$all_order, $widthMaterial): array
{

    $copy_all_order = $all_order;
    //file_put_contents(__DIR__ . "/copy_all_order.txt", print_r($copy_all_order, true));
    foreach ($all_order as &$order){
        foreach ($widthMaterial as $width_mat){
            if($order['width'] <= $width_mat){ //если меньше ширины рулона или равен
                foreach ($copy_all_order as $copy_order){
                    if($order['material'] == $copy_order['material']){
                        for ($i = 1; $i <= 3; $i++) {
                            $with_order_i = $order['width']*$i;
                            if($with_order_i < $width_mat){
                                for ($y = 1; $y <= 3; $y++){
                                    if( ($with_order_i + $copy_order['width'] * $y) <=  $width_mat){

                                        $remainder = ($with_order_i + $copy_order['width'] * $y);
                                        $efficiency = (($remainder / $width_mat) * 100);
                                        
                                        if($order['width_material'] == 840 and $order['percent']+8 > $efficiency){
                                            continue;
                                        }
                                        if($order['width_material'] == 1050 and $order['percent']+5 > $efficiency){
                                            continue;
                                        }

                                        if($order['percent'] < $efficiency){

                                            $order['percent'] = $efficiency;
                                            $order['width_material'] = $width_mat;
                                            $order['id_order'] = $copy_order['id'];
                                            //$order['remaining_quantity'] = $order['quantity'] - $copy_order['quantity'];
                                            $order['main_order_quantity_widtht'] = $i;
                                            $order['combined_order_quantity_widtht'] = $y;
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
            $order['width_material_'.$width_mat]['percent'] = $order['percent']; 
            $order['width_material_'.$width_mat]['id_order'] = $order['id_order']; 
            $order['width_material_'.$width_mat]['main_order_quantity_widtht'] = $order['main_order_quantity_widtht']; 
            $order['width_material_'.$width_mat]['combined_order_quantity_widtht'] = $order['combined_order_quantity_widtht']; 

        }
      
        $res = array_search($order['id_order'], array_map(function($v){return $v['id'];},$all_order));
        $res2 = array_search($order['id'], array_map(function($v){return $v['id'];},$copy_all_order));
        unset($all_order[$res]);
            unset($copy_all_order[$res2]);
       
        /* $res = array_search($id_other_order, array_map(function($v){return $v['id'];},$all_order));
        unset($all_order[$res]); */
    }
    
    return $all_order;
}
function get_top_efficiency($all_order){

    $all_name_material = material_get();
    $result_array= [];
    foreach ($all_order as $order){

        array_push($result_array, [
            "id" => $order['id'],
            "name" => $order['name'],
            "scan_length" => $order['length'],
            "scan_width" => $order['width'],
            "urgent" => $order['urgent'],
            "shipping_date" => $order['shipping_date'],
            "width" => $order['width_material'],
            "quantity" => $order['quantity'],
            //"remaining_quantity" => $remaining_quantity,
            "efficiency" =>  $order['percent'],
            "id_other_order" =>  $order['id_order'],
            "customer" => company_get($order['customer']),
            "customer_id" => $order['customer'],
            "material" => $all_name_material[$order['material']],
            "main_order_quantity_widtht" => $order['main_order_quantity_widtht'],
            "combined_order_quantity_widtht" => $order['combined_order_quantity_widtht'],
            'width_material_840'=>$order['width_material_840'], 
            'width_material_1050'=>$order['width_material_1050'], 
            'width_material_1260'=>$order['width_material_1260'],
            'width_material_1400'=>$order['width_material_1400'], 

        ]);
    }
    return $result_array;
}
