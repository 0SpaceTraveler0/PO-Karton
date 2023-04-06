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
$all_order = []; // все заказы с нужными полями

//формируем массив со всеми зказами
foreach ($listsElement as $value){
    $length_order +=  $value['PROPERTY_97'][key($value['PROPERTY_97'])] * $value['PROPERTY_91'][key($value['PROPERTY_91'])];

/*     if($length_order >= $length_limit ){
        break;
    } */
    $ListsElement =[
        'id' => $value['PROPERTY_63'][key($value['PROPERTY_63'])], //id паспорта
        'name' => $value['NAME'], //название паспорта
        'urgent' => $value['PROPERTY_124'][key($value['PROPERTY_124'])], // срочный bool 0/1
        'shipping_date' => $value['PROPERTY_85'][key($value['PROPERTY_85'])], // дата отгрузки
        'material' => $value['PROPERTY_89'][key($value['PROPERTY_89'])], // id материала
        'width' => $value['PROPERTY_90'][key($value['PROPERTY_90'])],  //ширина развертки
        'customer' =>$value['PROPERTY_65'][key($value['PROPERTY_65'])], // заказчик
        'quantity' =>$value['PROPERTY_97'][key($value['PROPERTY_97'])], // колличество план. штук
        'length' => $value['PROPERTY_91'][key($value['PROPERTY_91'])],  //длина развертки
        'all_length' => $value['PROPERTY_97'][key($value['PROPERTY_97'])] * $value['PROPERTY_91'][key($value['PROPERTY_91'])], // count_order * 'length',  колличество штук заказа * длина заказа        
        'percent'=>0, //коэффициент эффективности
        'width_material'=>0, //ширина материала используеммая для расчёта коэффициента эффективности
        'width_material_840'=>['percent'=>0,'id_order'=>0,'main_order_quantity_widtht'=>0,'combined_order_quantity_widtht'=>0], 
        'width_material_1050'=>['percent'=>0,'id_order'=>0,'main_order_quantity_widtht'=>0,'combined_order_quantity_widtht'=>0], 
        'width_material_1260'=>['percent'=>0,'id_order'=>0,'main_order_quantity_widtht'=>0,'combined_order_quantity_widtht'=>0],
        'width_material_1400'=>['percent'=>0,'id_order'=>0,'main_order_quantity_widtht'=>0,'combined_order_quantity_widtht'=>0], 
        'id_order'=>0, //совмещенный паспорт
        'main_order_quantity_widtht'=>0, //количество изделий оставшееся послерасчёта к коэффициенту эффективности
        'combined_order_quantity_widtht'=>0 //количество изделий_совмещенного оставшееся послерасчёта к коэффициенту эффективности
            
            /*
            percent - коэффициент эффективности
            width_material - ширина материала используеммая для расчёта коэффициента эффективности
            id_order - совмещенный паспорт
            order_quantity - количество изделий оставшееся послерасчёта к коэффициента эффективности
            copy_quantly_order - количество изделий_совмещенного оставшееся послерасчёта к коэффициента эффективности
            */
    ];
    array_push($all_order,$ListsElement);
}
/* ширина материала: 1400мм, 1260, 1050, 840 */
$widthMaterial = [840,1050,1260,1400];
//file_put_contents(__DIR__ . "/all_order.txt", print_r($all_order, true));
$all_order = efficiency($all_order,$widthMaterial);

$all_order = get_top_efficiency($all_order);
/* usort($all_order, function (array $a, array $b) {
    return [$a['urgent'], $a['width']] <=> [$b['urgent'], $b['width']]? -1 : 1;
    }); */
echo (json_encode($all_order));
/* delete_deal();
upload_deal(array_reverse($all_order)); */
//put_combinaded_passport($all_order);

