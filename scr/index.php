<?php
require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');
require_once(__DIR__ . '/efficiency.php');
require_once(__DIR__ . '/bitrix_request.php');
$start = microtime(true);
$post = file_get_contents('php://input');
$post = (array) json_decode($post);
$listsElementGet = 0;

if(isset($post['dateFrom']) and trim($post['dateFrom'])){
    $filter = [
        '!PROPERTY_64' => 60,
        '>PROPERTY_85' => $post['dateFrom'],
        '<PROPERTY_85' => $post['dateTo']
    ];
}else{
    $filter = [
        '!PROPERTY_64' => 60,
    ];
}

$listsElementGet = CRest::call('lists.element.get',[
    'IBLOCK_TYPE_ID' => 'lists',
    'IBLOCK_ID' => 17,
    'FILTER' => $filter,
    'ELEMENT_ORDER' => [
        'PROPERTY_124' => 'DESC',
        'PROPERTY_85' => 'asc'
    ]
]);

$listsElement = $listsElementGet['result'];

$length_limit = 15000000; //лимит на длину изделив в неделю
$length_order = 0;
$orders = []; // все заказы с нужными полями

//формируем массив со всеми зказами
foreach ($listsElement as $value){
    $length_order +=  $value['PROPERTY_97'][key($value['PROPERTY_97'])] * $value['PROPERTY_91'][key($value['PROPERTY_91'])];


/*     if($length_order >= $length_limit ){
        break;
    }*/
    $urgent = isset($value['PROPERTY_124']) ? $value['PROPERTY_124'][key($value['PROPERTY_124'])] : 0;
    $ListsElement =[
        'id' => $value['PROPERTY_63'][key($value['PROPERTY_63'])], //id паспорта
        'urgent' => $urgent, // срочный bool 0/1
        'shipping_date' => $value['PROPERTY_85'][key($value['PROPERTY_85'])], // дата отгрузки
        'material' => $value['PROPERTY_89'][key($value['PROPERTY_89'])], // id материала
        'width' => $value['PROPERTY_90'][key($value['PROPERTY_90'])],  //ширина развертки
        'customer' =>$value['PROPERTY_65'][key($value['PROPERTY_65'])], // заказчик
        'quantity' =>$value['PROPERTY_97'][key($value['PROPERTY_97'])], // колличество план. штук
        'items_per_plan' =>$value['PROPERTY_97'][key($value['PROPERTY_97'])], // колличество план. штук
        'remaining_quantity' => 0,
        'length' => $value['PROPERTY_91'][key($value['PROPERTY_91'])],  //длина развертки
        'all_length' => $value['PROPERTY_97'][key($value['PROPERTY_97'])] * $value['PROPERTY_91'][key($value['PROPERTY_91'])], // count_order * 'length',  колличество штук заказа * длина заказа        
        'percent'=>0, //коэффициент эффективности
        'width_material'=>0, //ширина материала используеммая для расчёта коэффициента эффективности
        'combined_id_passport'=>0, //совмещенный паспорт
        'main_order_quantity_widtht'=>0, //количество изделий оставшееся послерасчёта к коэффициенту эффективности
        'combined_order_quantity_widtht'=>0 //количество изделий_совмещенного оставшееся послерасчёта к коэффициенту эффективности
            

    ];
    array_push($orders,$ListsElement);
}
/* ширина материала: 1400мм, 1260, 1050, 840 */
$widthMaterial = [840, 1050, 1260, 1400];
//file_put_contents(__DIR__ . "/orders.txt", print_r($orders, true));
$orders = calculateEfficiency($orders,$widthMaterial);
usort($orders, fn($a, $b) =>

    [$a['width']]

    <

    [$b['width']]

);
echo (json_encode($orders));

/* delete_deal();
upload_deal(array_reverse($orders)); */
//put_combinaded_passport($orders);
