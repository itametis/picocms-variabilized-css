<?php
/* This file is part of VARIABILIZED CSS LOADER.
 *
 * VARIABILIZED CSS LOADER is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * VARIABILIZED CSS LOADER is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with VARIABILIZED CSS LOADER.  If not, see <http://www.gnu.org/licenses/gpl.txt/>.
 *
 * If you need to develop a closed-source software, please contact us
 * at 'social@itametis.com' to get a commercial version of VARIABILIZED CSS LOADER,
 * with a proprietary license instead.
 */
final class VariabilizedCssLoader extends AbstractPicoPlugin {
    /**
     * This plugin is enabled by default?
     *
     * @see AbstractPicoPlugin::$enabled
     * @var boolean
     */
    protected $enabled = false;

    /**
     * The key that enable the cache of this plugin inside the 'config.php' file of PICO.
     */
    private static $CACHE_CONFIG_KEY = 'VariabilizedCssLoader.activeCssCache';

    /**
     * true the CSS cache is enabled, false it is not.
     */
    protected $activeCssCache = false;

    /**
     * The path to the cache directory.
     */
    private $cssCacheDirectory = '';
    
    /**
     * The default path to the file containing CSS variables.
     */
    private $cssVariablesFilePath = '';
    
    /**
     * The path to the current theme directory.
     */
    private $themeDir = '';

    /**
     * Retrieve the configuration of this plugin from PICO.
     *
     * @see inherited documentation.
     */
    public function onConfigLoaded(array &$config) {
        if (isset($config[VariabilizedCssLoader::$CACHE_CONFIG_KEY])) {
            $this->activeCssCache = $config[VariabilizedCssLoader::$CACHE_CONFIG_KEY];
        }

        $this->themeDir = 'themes' . DIRECTORY_SEPARATOR . $config['theme'] . DIRECTORY_SEPARATOR;
        $this->cssVariablesFilePath = $this->themeDir . 'css-variables.ini';
        $this->cssCacheDirectory = $this->themeDir . 'cache' . DIRECTORY_SEPARATOR . 'css';
    }

    /**
     * Makes this plugin available inside Twig template by using : {{ variabilizedCssLoader.method() }}
     */
    public function onPageRendering(Twig_Environment &$twig, array &$twigVariables, &$templateName) {
        $twigVariables['variabilizedCssLoader'] = $this;
    }

    /**
     * Generates a CSS file with all variables substituted and echoes the path to this file.
     */
    public function loadCss($filePath) {
        $substitutedCssFilePath = $this->buildCssFilePath($filePath);

        // 1 - Do we need to regenerated the cache
        if (!$this->doesSubstitutedCssFileExists($substitutedCssFilePath) || $this->hasToForceCssSubstitution()) {
            // 2 - Create cache directory
            $this->initCacheDirectory();
            // 3 - Build CSS file name to generate"
            // 4 - Read & Substitute CSS content"
            $cssVariables = $this->readCssVariables();
            $content = $this->processSubstitution($this->themeDir . $filePath, $cssVariables);
            // 6 - Write file"
            $cssFile = $this->writeCssFile($substitutedCssFilePath, $content);
        }
        
        echo '/' . $substitutedCssFilePath;
    }


    private function buildCssFilePath($filePath) {
        return $this->cssCacheDirectory . DIRECTORY_SEPARATOR . $this->extractFileName($filePath);
    }

    private function doesSubstitutedCssFileExists($substitutedCssFilePath) {
        return is_file($substitutedCssFilePath);
    }

    private function extractFileName($filePath) {
        return substr(strrchr($filePath, '/'), 1) ;
    }


    private function hasToForceCssSubstitution() {
        return !$this->activeCssCache;
    }

    private function initCacheDirectory() {
        if (!is_dir($this->cssCacheDirectory)) {
            if (!mkdir($this->cssCacheDirectory, 0700, true)) {
                die('Impossible to create the CSS cache temp directory for VariabilizedCssLoader PICO plugin');
            }
        }
    }

    private function processSubstitution($filePath, $cssVariables) {
        $cssFile = file_get_contents($filePath);

        foreach ($cssVariables as $key => $value) {
            $cssFile = str_replace('$('.$key.')', $value, $cssFile);
        }

        return $cssFile;
    }

    private function readCssVariables() {
        return parse_ini_file($this->cssVariablesFilePath);
    }

    private function writeCssFile($filePath, $fileContent) {
        file_put_contents($filePath, $fileContent);
    }
}
