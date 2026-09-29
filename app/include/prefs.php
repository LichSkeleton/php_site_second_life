<?php

function siteTheme(): string
{
    return (($_COOKIE['site_theme'] ?? '') === 'dark') ? 'dark' : 'light';
}
