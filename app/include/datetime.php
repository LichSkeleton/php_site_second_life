<?php

function appTimeZone(): DateTimeZone
{
    return new DateTimeZone('Europe/Kyiv');
}

function appDate($value): ?DateTimeImmutable
{
    if ($value === null || $value === '') {
        return null;
    }
    try {
        return new DateTimeImmutable((string) $value, appTimeZone());
    } catch (Exception $e) {
        return null;
    }
}

function formatAppDate($value): string
{
    $date = appDate($value);
    return $date ? $date->format('d.m.Y H:i') : '';
}

function formatAppDateIso($value): string
{
    $date = appDate($value);
    return $date ? $date->format('c') : '';
}
