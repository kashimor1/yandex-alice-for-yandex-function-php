<?php
function handler($event, $context) {
    //return $event;
    // ключ Laitis
    $key = 'PUpR7ydUO0';

    $response = [
        'response' => [
            'end_session' => false,
        ],
        'version' => $event['version']
    ];

    switch ($event['request']['command']) {
        case '':
        case 'Запусти навык моя Мирана':
            $response['response']['text'] = 'Навык запущен';
            $response['response']['tts'] = 'Навык запущен';
    }
        
    if (!empty($event['request']['command'])) {
        
        $command_text = $event['request']['command'];
        $command_text = str_replace('алиса', '', $command_text);
        $command_text =  trim($command_text);

        $command_get = str_replace([' ', '  ', '   '], '%20', trim(replaceResponse($command_text, true)));
        $laitis_request = file_get_contents("https://laitis.ru/send?key=" . $key . "&phrase=".$command_get);
        
        $laitis_request_bool = false;
        if ($laitis_request == 'OK' || $laitis_request == 'Phrase is empty') {
            $laitis_request_bool = true;
        } else {
            $response['response']['text'] = 'Что-то не так, Laitis вернул: ' . $laitis_request;
            $response['response']['tts'] = 'Что-то не так, Laitis вернул: ' . $laitis_request;
        }
        
        if ($laitis_request_bool) {
            
            $textResponse = replaceResponse($command_text);
            $response['response']['text'] = $textResponse;
            $response['response']['tts'] = $textResponse;
            
        }
    }
    return $response;
}

function replaceResponse($command, $pharse = false) {
    
    $arrayCommandsReplace = [
        0 => [
            'regWord' => '[Вв]ключ',
            'returnWord' => 'Включила',
            'statusReplaceCommand' => false
        ],
        1 => [
            'regWord' => '[Оо]тключ',
            'returnWord' => 'Отключила',
            'statusReplaceCommand' => true
        ],
        2 => [
            'regWord' => '[Оо]ткр',
            'returnWord' => 'Открыла',
            'statusReplaceCommand' => true
        ],
        7 => [
            'regWord' => '[Зз]акр',
            'returnWord' => 'Закрываю',
            'statusReplaceCommand' => true
        ],
        3 => [
            'regWord' => '[Нн]ажми',
            'returnWord' => 'Нажала',
            'statusReplaceCommand' => true
        ],
        4 => [
            'regWord' => '[Вв]ыключ',
            'returnWord' => 'Выключила',
            'statusReplaceCommand' => false
        ],
        5 => [
            'regWord' => '[Пп]окаж',
            'returnWord' => 'Показываю',
            'statusReplaceCommand' => false
        ],
        6 => [
            'regWord' => '[Пп]оказ',
            'returnWord' => 'Показываю',
            'statusReplaceCommand' => false
        ],
        8 => [
            'regWord' => '[Уу]стан',
            'returnWord' => 'Установила',
            'statusReplaceCommand' => true
        ],
        9 => [
            'regWord' => '[Ии]змени',
            'returnWord' => 'Изменила',
            'statusReplaceCommand' => true
        ],
        10 => [
            'regWord' => '[Пп]остав',
            'returnWord' => 'Поставила',
            'statusReplaceCommand' => true
        ],
        11 => [
            'regWord' => '[Уу]дали',
            'returnWord' => 'Удалила',
            'statusReplaceCommand' => false
        ],
        12 => [
            'regWord' => '[Пп]оменя',
            'returnWord' => 'Поменяла',
            'statusReplaceCommand' => true
        ],
        13 => [
            'regWord' => '[Сс]дела',
            'returnWord' => 'Сделала',
            'statusReplaceCommand' => true
        ],
        14 => [
            'regWord' => '[Вв]ыдел',
            'returnWord' => 'Выделила',
            'statusReplaceCommand' => false
        ],
        15 => [
            'regWord' => '[Пп]ереключ',
            'returnWord' => 'Переключила',
            'statusReplaceCommand' => true
        ],
        16 => [
            'regWord' => '[Пп]еремес',
            'returnWord' => 'Переместила',
            'statusReplaceCommand' => true
        ],
        17 => [
            'regWord' => '[Пп]еренес',
            'returnWord' => 'Перенесла',
            'statusReplaceCommand' => true
        ],
        18 => [
            'regWord' => '[Нн]айди',
            'returnWord' => 'Ищу в поисковике',
            'statusReplaceCommand' => false
        ],
        19 => [
            'regWord' => '[Вв]ыбери',
            'returnWord' => 'Выбрала',
            'statusReplaceCommand' => true
        ],
    ];

    $return = 'Готово';
    if ($pharse) {
        $return = $command;
    }
    foreach ($arrayCommandsReplace as $commandReplace) {
        $commandReg = $command;
        $reg = '/(^|\A|\s|\-)' . $commandReplace['regWord'] . '.*?(\s|$|\Z|\-)/u';
        preg_match_all($reg, $commandReg, $matches, PREG_SET_ORDER, 0);
       
        if (isset($matches[0][0]) && !empty($matches[0][0])) {
            $data = preg_replace($reg, '', $command);
            $return = $commandReplace['returnWord'] . ' ' . $data;
            if ($pharse) {
                $return = $data;
                if (!$commandReplace['statusReplaceCommand']) {
                    $return = $command;
                }
            }
        }
    }
    return $return;
}
