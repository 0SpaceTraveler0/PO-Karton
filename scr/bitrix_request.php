<?php
require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

function  put_combinaded_passport($all_order){
    foreach ($all_order as $item){
        $listsElementGet = CRest::call('lists.element.get',[
            'IBLOCK_TYPE_ID' => 'lists',
            'IBLOCK_ID' => 17,
            'FILTER' => [
                '=PROPERTY_63' => $item['id']
            ]]);
        $listless = $listsElementGet['result'][0];

        CRest::call('lists.element.update',[
             'IBLOCK_TYPE_ID' => 'lists',
             'IBLOCK_ID' => '17',
             'ELEMENT_ID' => $listless['ID'],
             'FIELDS' => [
                 'NAME' => $listless['NAME'],
                 'DATE_CREATE' => $listless['DATE_CREATE'],
                 'PROPERTY_63' => $item['id'],
                 'PROPERTY_64' => @$listless['PROPERTY_64'][key($listless['PROPERTY_64'])],
                 'PROPERTY_65' => @$listless['PROPERTY_65'][key($listless['PROPERTY_65'])],
                 'PROPERTY_66' => @$listless['PROPERTY_66'][key($listless['PROPERTY_66'])],
                 'PROPERTY_67' => @$listless['PROPERTY_67'][key($listless['PROPERTY_67'])],
                 'PROPERTY_81' => @$listless['PROPERTY_81'][key($listless['PROPERTY_81'])],
                 'PROPERTY_82' => @$listless['PROPERTY_82'][key($listless['PROPERTY_82'])],
                 'PROPERTY_84' => @$listless['PROPERTY_84'][key($listless['PROPERTY_84'])],
                 'PROPERTY_85' => @$listless['PROPERTY_85'][key($listless['PROPERTY_85'])],
                 'PROPERTY_86' => @$listless['PROPERTY_86'][key($listless['PROPERTY_86'])],
                 'PROPERTY_87' => @$listless['PROPERTY_87'][key($listless['PROPERTY_87'])],
                 'PROPERTY_88' => @$listless['PROPERTY_88'][key($listless['PROPERTY_88'])],
                 'PROPERTY_89' => @$listless['PROPERTY_89'][key($listless['PROPERTY_89'])],
                 'PROPERTY_90' => @$listless['PROPERTY_90'][key($listless['PROPERTY_90'])],
                 'PROPERTY_91' => @$listless['PROPERTY_91'][key($listless['PROPERTY_91'])],
                 'PROPERTY_92' => @$listless['PROPERTY_92'][key($listless['PROPERTY_92'])],
                 'PROPERTY_93' => @$listless['PROPERTY_93'][key($listless['PROPERTY_93'])],
                 'PROPERTY_94' => @$listless['PROPERTY_94'][key($listless['PROPERTY_94'])],
                 'PROPERTY_95' => @$listless['PROPERTY_95'][key($listless['PROPERTY_95'])],
                 'PROPERTY_96' => @$listless['PROPERTY_96'][key($listless['PROPERTY_96'])],
                 'PROPERTY_97' => @$listless['PROPERTY_97'][key($listless['PROPERTY_97'])],
                 'PROPERTY_100' => @$listless['PROPERTY_100'][key($listless['PROPERTY_100'])],
                 'PROPERTY_101' => @$listless['PROPERTY_101'][key($listless['PROPERTY_101'])],
                 'PROPERTY_102' => @$listless['PROPERTY_102'][key($listless['PROPERTY_102'])],
                 'PROPERTY_103' => @$listless['PROPERTY_103'][key($listless['PROPERTY_103'])],
                 'PROPERTY_104' => @$listless['PROPERTY_104'][key($listless['PROPERTY_104'])],
                 'PROPERTY_105' => @$listless['PROPERTY_105'][key($listless['PROPERTY_105'])],
                 'PROPERTY_106' => @$listless['PROPERTY_106'][key($listless['PROPERTY_106'])],
                 'PROPERTY_107' => @$listless['PROPERTY_107'][key($listless['PROPERTY_107'])],
                 'PROPERTY_108' => @$listless['PROPERTY_108'][key($listless['PROPERTY_108'])],
                 'PROPERTY_109' => @$listless['PROPERTY_109'][key($listless['PROPERTY_109'])],
                 'PROPERTY_110' => @$listless['PROPERTY_110'][key($listless['PROPERTY_110'])],
                 'PROPERTY_111' => @$listless['PROPERTY_111'][key($listless['PROPERTY_111'])],
                 'PROPERTY_112' => @$item['id_other_order'],
                 'PROPERTY_113' => @$listless['PROPERTY_113'][key($listless['PROPERTY_113'])],
                 'PROPERTY_116' => @$listless['PROPERTY_116'][key($listless['PROPERTY_116'])],
                 'PROPERTY_124' => @$listless['PROPERTY_124'][key($listless['PROPERTY_124'])],
             ]
         ]);
    }
}

function company_get($id){
    $rest = CRest::call('crm.company.get',[
        'ID' => $id]);
    return $rest['result']['TITLE'];
}

function material_get(){
    $material_get = CRest::call('lists.field.get',[
        'IBLOCK_TYPE_ID' => 'lists',
        'IBLOCK_ID' => 17,
        'FIELD_ID' => 'PROPERTY_89'
    ]);
    $all_material = $material_get['result']['L']['DISPLAY_VALUES_FORM'];
    return $all_material;
}

function upload_deal($all_order){
    foreach ($all_order as $order){
        $res =CRest::call('crm.deal.add', [
            'fields' => [
                'CATEGORY_ID' => 9,
                'TITLE' => $order['id'].'test',
                'UF_CRM_1676004652' => $order['name'],
                'STAGE_ID' => 'C9:NEW',
                'CURRENCY_ID' => 'RUB',
                'UF_CRM_1671185143' => $order['id'], // паспорт
                'UF_CRM_1674156116' => $order['id_other_order'], // паспорт
                'UF_CRM_1680086866794' => $order['urgent'], // паспорт
                'UF_CRM_1674181372' => $order['material'], //материал
                'UF_CRM_1673504567' => $order['customer_id'], //заказщик
                'COMPANY_ID' => $order['customer_id'], //заказщик
                'UF_CRM_1674155969' => $order['quantity'], // колличество
                'UF_CRM_1680087517854' => $order['main_order_quantity_widtht'], // Количество основного заказа в ширину
                'UF_CRM_1680088113635' => $order['combined_order_quantity_widtht'], // Количество совмещенного заказа в ширину
                'UF_CRM_1680089010545' => $order['width'], // ширина рулона
            ],
            'params' => ["REGISTER_SONET_EVENT" => "N"]

        ]);
        
    }
}

function get_all_deal(){
    $rest = CRest::call('crm.deal.list',[
        'filter' => [
            "CATEGORY_ID" => 9,
            "STAGE_ID" => 'C9:NEW'
        ],'select' => ['id'],
        'start' => -1
    ]);
    return $rest['result'];
}

function delete_deal(){
    $all_deal = get_all_deal();
    foreach($all_deal as $deal){
        $rest = CRest::call('crm.deal.delete',[
            'ID' => $deal['ID']
        ]);
    }

}

function put_status_in_work($id,$flag){

    if($flag){$work = 59;}
    else{ $work = 58;}
    $listsElementGet = CRest::call('lists.element.get',[
        'IBLOCK_TYPE_ID' => 'lists',
        'IBLOCK_ID' => 17,
        'FILTER' => [
            '=PROPERTY_63' => $id
        ]]);

    $listless = $listsElementGet['result'][0];
    file_put_contents(__DIR__ . "/listless.txt", print_r($listless, true));
    $rest = CRest::call('lists.element.update',[
        'IBLOCK_TYPE_ID' => 'lists',
        'IBLOCK_ID' => '17',
        'ELEMENT_ID' => $listless['ID'],
        'FIELDS' => [
            'NAME' => $listless['NAME'],
            'DATE_CREATE' => $listless['DATE_CREATE'],
            'PROPERTY_63' => $id,
            'PROPERTY_64' => $work,
            'PROPERTY_65' => $listless['PROPERTY_65'][key($listless['PROPERTY_65'])],
            'PROPERTY_66' => $listless['PROPERTY_66'][key($listless['PROPERTY_66'])],
            'PROPERTY_67' => $listless['PROPERTY_67'][key($listless['PROPERTY_67'])],
            'PROPERTY_81' => $listless['PROPERTY_81'][key($listless['PROPERTY_81'])],
            'PROPERTY_82' => $listless['PROPERTY_82'][key($listless['PROPERTY_82'])],
            'PROPERTY_84' => $listless['PROPERTY_84'][key($listless['PROPERTY_84'])],
            'PROPERTY_85' => $listless['PROPERTY_85'][key($listless['PROPERTY_85'])],
            'PROPERTY_86' => $listless['PROPERTY_86'][key($listless['PROPERTY_86'])],
            'PROPERTY_87' => $listless['PROPERTY_87'][key($listless['PROPERTY_87'])],
            'PROPERTY_88' => $listless['PROPERTY_88'][key($listless['PROPERTY_88'])],
            'PROPERTY_89' => $listless['PROPERTY_89'][key($listless['PROPERTY_89'])],
            'PROPERTY_90' => $listless['PROPERTY_90'][key($listless['PROPERTY_90'])],
            'PROPERTY_91' => $listless['PROPERTY_91'][key($listless['PROPERTY_91'])],
            'PROPERTY_92' => $listless['PROPERTY_92'][key($listless['PROPERTY_92'])],
            'PROPERTY_93' => $listless['PROPERTY_93'][key($listless['PROPERTY_93'])],
            'PROPERTY_94' => $listless['PROPERTY_94'][key($listless['PROPERTY_94'])],
            'PROPERTY_95' => $listless['PROPERTY_95'][key($listless['PROPERTY_95'])],
            'PROPERTY_96' => $listless['PROPERTY_96'][key($listless['PROPERTY_96'])],
            'PROPERTY_97' => $listless['PROPERTY_97'][key($listless['PROPERTY_97'])],
            'PROPERTY_100' => intval($listless['PROPERTY_100'][key($listless['PROPERTY_100'])]),
            'PROPERTY_101' => intval($listless['PROPERTY_101'][key($listless['PROPERTY_101'])]),
            'PROPERTY_102' => $listless['PROPERTY_102'][key($listless['PROPERTY_102'])],
            'PROPERTY_103' => $listless['PROPERTY_103'][key($listless['PROPERTY_103'])],
            'PROPERTY_104' => $listless['PROPERTY_104'][key($listless['PROPERTY_104'])],
            'PROPERTY_105' => $listless['PROPERTY_105'][key($listless['PROPERTY_105'])],
            'PROPERTY_106' => $listless['PROPERTY_106'][key($listless['PROPERTY_106'])],
            'PROPERTY_107' => $listless['PROPERTY_107'][key($listless['PROPERTY_107'])],
            'PROPERTY_108' => $listless['PROPERTY_108'][key($listless['PROPERTY_108'])],
            'PROPERTY_109' => $listless['PROPERTY_109'][key($listless['PROPERTY_109'])],
            'PROPERTY_110' => $listless['PROPERTY_110'][key($listless['PROPERTY_110'])],
            'PROPERTY_111' => $listless['PROPERTY_111'][key($listless['PROPERTY_111'])],
            'PROPERTY_112' => $listless['PROPERTY_111'][key($listless['PROPERTY_111'])],
        ]

    ]);

}

