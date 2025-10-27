<?php

// Новый массив с расширенным списком персон
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester'
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer'
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst'
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer'
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst'
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer'
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager'
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager'
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst'
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer'
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter'
    ]
];

// 1. Функция склеивания ФИО
function getFullnameFromParts($surname, $name, $patronymic) {
    return "{$surname} {$name} {$patronymic}";
}

// 2. Функция разбиения ФИО
function getPartsFromFullname($fullname) {
    $parts = explode(" ", $fullname);
    return [
        'surname' => $parts[0],
        'name' => $parts[1],
        'patronymic' => $parts[2]
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
    if (mb_substr($parts['patronymic'], -2) === 'ич') $genderScore++;
    if (mb_substr($parts['name'], -1) === 'й' || mb_substr($parts['name'], -1) === 'н') $genderScore++;
    if (mb_substr($parts['surname'], -1) === 'в') $genderScore++;

    // Признаки женского пола
    if (mb_substr($parts['patronymic'], -3) === 'вна') $genderScore--;
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

    return 
        'total' => $total,
        'male' => $malePercent,
        'female' => $femalePercent,
        'undefined' => $undefinedPercent