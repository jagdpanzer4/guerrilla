<?php

namespace Concrete\Package\Guerrilla\Theme\Guerrilla;

use Concrete\Core\Page\Theme\Theme;

defined('C5_EXECUTE') or die('Access Denied.');

class PageTheme extends Theme
{
    protected $pThemeName = 'Guerrilla';
    protected $pThemeDescription = 'Guerrilla – nowoczesny motyw dla Concrete CMS 9.5 oparty na Bootstrap 5.';
    protected $pThemeVersion = '1.0.0';

    // Allowed areas and their max number of blocks
    protected $pThemeGridFramework = 'bootstrap5';

    public function registerAssets(): void
    {
        // Bootstrap 5 (grid, utilities)
        $this->requireAsset('javascript', 'bootstrap');
        $this->requireAsset('css', 'bootstrap');

        // Font Awesome 5 (icons for feature, content blocks)
        $this->requireAsset('css', 'font-awesome');

        // Material Web Components (MD3) bundle
        $this->requireAsset('javascript', 'guerrilla/material-web');
    }

    /**
     * Grid container classes.
     */
    public function getThemeDefaultBlockTemplates(): array
    {
        return [];
    }

    /**
     * Supported page types.
     */
    public function getThemePageTypes(): array
    {
        return ['full', 'full_width', 'left_sidebar'];
    }
}
