<?php
require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');
require_once(__DIR__ . '/efficiency.php');
require_once(__DIR__ . '/bitrix_request.php');


file_put_contents(__DIR__ . "/POST.txt", print_r($_POST, true));
$listsElementGet = CRest::call('lists.element.get',[
    'IBLOCK_TYPE_ID' => 'lists',
    'IBLOCK_ID' => 17,
    'FILTER' => [
        '!PROPERTY_64' => 60,
        '>PROPERTY_85' => '2019-10-01T00:00:00',
        '<PROPERTY_85' => '2019-10-31T23:59:59'
    ],
    'ELEMENT_ORDER' => [
        'PROPERTY_124' => 'DESC',
        'PROPERTY_85' => 'asc'
    ]
]);

$listsElement = $listsElementGet['result'];
$length_limit = 15000000; //лимит на длину изделив в неделю
$length_order = 0;
$all_order = []; // все заказы с нужными полями

//формируем массив со всеми зказами
foreach ($listsElement as $value){
    $length_order +=  $value['PROPERTY_97'][key($value['PROPERTY_97'])] * $value['PROPERTY_91'][key($value['PROPERTY_91'])];

     if($length_order >= $length_limit ){
        break;
    }
    $ListsElement =[
        'id' => $value['PROPERTY_63'][key($value['PROPERTY_63'])],
        'shipping_date' => $value['PROPERTY_85'][key($value['PROPERTY_85'])],
        'material' => $value['PROPERTY_89'][key($value['PROPERTY_89'])],
        'width' => $value['PROPERTY_90'][key($value['PROPERTY_90'])],
        'customer' =>$value['PROPERTY_65'][key($value['PROPERTY_65'])], // заказчик
        'quantity' =>$value['PROPERTY_97'][key($value['PROPERTY_97'])], // колличество план. штук
        'length' => $value['PROPERTY_97'][key($value['PROPERTY_97'])] * $value['PROPERTY_91'][key($value['PROPERTY_91'])], // count_order * 'length',  колличество штук заказа * длина заказа
        'remaining_quantity' => 0,
        'efficiency' => [
            'efficiency1' => ['efficiency'=>0, 'width_material'=>0],
            'efficiency2' => ['efficiency'=>0, 'width_material'=>0, 'id_order'=>0],
            'efficiency3' => ['efficiency'=>0, 'width_material'=>0, 'id_order'=>0]
        ]

    ];
    array_push($all_order,$ListsElement);
}

//Высчитываем ээффективность в процентах 3 способами
/*
    1 Нужное изделие и только оно на ширину материала
    2 Нужное изделие + перебор по другим заказам на ширину материала
    3 Нужное изделие + нужное изделие + перебор по другим заказам на ширину материала

    ширина материала: 
        1400
        1260
        1050
        840
 */
$widthMaterial = [840,1050,1260,1400];



$all_order = efficiency1($all_order,$widthMaterial); //Нужное изделие на ширину материала
$all_order = efficiency($all_order,$widthMaterial); 

echo (json_encode($all_order));

put_combinaded_passport($all_order);//

/* echo '<pre>';
print_r();
echo '</pre>'; */