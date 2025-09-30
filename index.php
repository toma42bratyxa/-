<?php

// Пример массива
$example_persons_array = [
    ['fullname' => 'Иванов Иван Иванович', 'job' => 'backend developer'],
    ['fullname' => 'Смирнова Мария Андреевна', 'job' => 'project manager'],
    ['fullname' => 'Кузнецов Николай Николаевич', 'job' => 'frontend developer'],
    ['fullname' => 'Попова Светлана Петровна', 'job' => 'designer'],
    ['fullname' => 'Васильев Сергей Павлович', 'job' => 'QA engineer'],
];

// 1. Функция склеивания ФИО
function getFullnameFromParts($surname, $name, $patronomyc) {
    return "{$surname} {$name} {$patronomyc}";
}

// 2. Функция разбиения ФИО
function getPartsFromFullname($fullname) {
    $parts = explode(" ", $fullname);
    return [
        'surname' => $parts[0],
        'name' => $parts[1],
        'patronomyc' => $parts[2]
    ];
}

// 3. Функция сокращения ФИО
function getShortName($fullname) {
    $parts = getPartsFromFullname($fullname);
    return "{$parts['name']} " . mb_substr($parts['surname'], 0, 1) . ".";
}

// 4. Функция определения пола по ФИО
function getGenderFromName($fullname) {
    $parts = getPartsFromFullname($fullname);
    $genderScore = 0;

    // Признаки мужского пола
    if (mb_substr($parts['patronomyc'], -2) === 'ич') $genderScore++;
    if (mb_substr($parts['name'], -1) === 'й' || mb_substr($parts['name'], -1) === 'н') $genderScore++;
    if (mb_substr($parts['surname'], -1) === 'в') $genderScore++;

    // Признаки женского пола
    if (mb_substr($parts['patronomyc'], -3) === 'вна') $genderScore--;
    if (mb_substr($parts['name'], -1) === 'а') $genderScore--;
    if (mb_substr($parts['surname'], -2) === 'ва') $genderScore--;

    if ($genderScore > 0) return 1;
    if ($genderScore < 0) return -1;
    return 0;
}

// 5. Функция описания гендерного состава
function getGenderDescription($array) {
    $total = count($array);
    $male = count(array_filter($array, function($person) {
        return getGenderFromName($person['fullname']) === 1;
    }));
    $female = count(array_filter($array, function($person) {
        return getGenderFromName($person['fullname']) === -1;
    }));
    $undefined = $total - $male - $female;

    $malePercent = round($male / $total * 100, 1);
    $femalePercent = round($female / $total * 100, 1);
    $undefinedPercent = round($undefined / $total * 100, 1);

    echo "Гендерный состав аудитории:\n";
    echo "---------------------------\n";
    echo "Мужчины - {$malePercent}%\n";
    echo "Женщины - {$femalePercent}%\n";
    echo "Не удалось определить - {$undefinedPercent}%\n";
}

// 6. Функция подбора идеального партнера
function getPerfectPartner($surname, $name, $patronomyc, $array) {
    // Приводим к правильному регистру
    $surname = mb_convert_case($surname, MB_CASE_TITLE, "UTF-8");
    $name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
    $patronomyc = mb_convert_case($patronomyc, MB_CASE_TITLE, "UTF-8");

    $personFullname = getFullnameFromParts($surname, $name, $patronomyc);
    $personGender = getGenderFromName($personFullname);

    if ($personGender === 0) {
        echo "Невозможно определить пол для: $personFullname\n";
        return;
    }

    // Фильтруем массив по противоположному полу
    $oppositeGenderPeople = array_filter($array, function($person) use ($personGender) {
        return getGenderFromName($person['fullname']) === -$personGender;
    });

    // Если не найдены противоположного пола
    if (empty($oppositeGenderPeople)) {
        echo "Нет подходящих партнеров противоположного пола.\n";
        return;
    }

    // Выбираем случайного партнера
    $partner = $oppositeGenderPeople[array_rand($oppositeGenderPeople)];

    // Генерируем процент совместимости
    $compatibility = round(mt_rand(5000, 10000) / 100, 2);

    // Выводим результат
    $personShort = getShortName($personFullname);
    $partnerShort = getShortName($partner['fullname']);

    echo "{$personShort} + {$partnerShort} = \n";
    echo "♡ Идеально на {$compatibility}% ♡\n";
}

// ------------- Вызовы функций для тестирования -------------

echo "--- Проверка getPartsFromFullname ---\n";
print_r(getPartsFromFullname("Иванов Иван Иванович"));

echo "--- Проверка getFullnameFromParts ---\n";
echo getFullnameFromParts("Иванов", "Иван", "Иванович") . "\n";

echo "--- Проверка getShortName ---\n";
echo getShortName("Иванов Иван Иванович") . "\n";

echo "--- Проверка getGenderFromName ---\n";
echo getGenderFromName("Иванов Иван Иванович") . "\n"; // Ожидается 1
echo getGenderFromName("Смирнова Мария Андреевна") . "\n"; // Ожидается -1

echo "--- Проверка getGenderDescription ---\n";
getGenderDescription($example_persons_array);

echo "--- Проверка getPerfectPartner ---\n";
getPerfectPartner("иванова", "Наталья", "Андреевна", $example_persons_array);

?>