<?php

use Mongolid\Laravel\AbstractModel;
use Symfony\Component\VarDumper\Caster\Caster;

function objectId($id) {
    return new \MongoDB\BSON\ObjectId($id);
}

function utcDateTime($milliseconds) {
    return new \MongoDB\BSON\UTCDateTime($milliseconds);
}

/**
 * @param AbstractModel $object
 */
function ModelCaster($object): array
{
    $results = [];

    foreach ($object->getDocumentAttributes() as $key => $value) {
        $results[Caster::PREFIX_VIRTUAL.$key] = $value;
    }

    if ($object->errors()->any()) {
        $results[Caster::PREFIX_PROTECTED.'errors'] = $object->errors();
    }

    return $results;
}

/**
 * @param \MongoDB\BSON\UTCDateTime $object
 */
function UTCDateTimeCaster($object): array
{
    return [
        Caster::PREFIX_VIRTUAL.'milliseconds' => (string) $object,
        Caster::PREFIX_PROTECTED.'date' => Mongolid\Util\LocalDateTime::format($object),
    ];
}

/**
 * @param \Illuminate\Contracts\Support\MessageBag $object
 */
function MessageBagCaster($object): array
{
    $results = [];

    foreach ($object->toArray() as $field => $errors) {
        $results[Caster::PREFIX_VIRTUAL.$field] = $errors;
    }

    return $results;
}

if (app()->environment('local')) {
    $config->setHistoryFile(storage_path('logs/tinker_history.log'));
}


return [
    'casters' => [
        AbstractModel::class => 'ModelCaster',
        MongoDB\BSON\UTCDateTime::class => 'UTCDateTimeCaster',
        Illuminate\Contracts\Support\MessageBag::class => 'MessageBagCaster'
    ]
];
