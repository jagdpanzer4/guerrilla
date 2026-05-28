<?php

namespace Concrete\Package\Guerrilla;

use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends Package
{
    protected string $pkgHandle = 'guerrilla';
    protected string $appVersionRequired = '9.5.0';
    protected string $pkgVersion = '1.0.0';
    protected $pkgAutoloaderRegistries = [];

    public function getPackageDescription(): string
    {
        return t('Guerrilla theme package for Concrete CMS 9.5.');
    }

    public function getPackageName(): string
    {
        return t('Guerrilla');
    }

    public function install(): void
    {
        $pkg = parent::install();

        // Install the theme
        $theme = \Concrete\Core\Page\Theme\Theme::add('guerrilla', $pkg);
        $theme->applyToSite();
    }

    public function upgrade(): void
    {
        parent::upgrade();
    }

    public function uninstall(): void
    {
        parent::uninstall();
    }
}
