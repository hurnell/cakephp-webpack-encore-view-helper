<?php

namespace Hurnell\WebpackEncoreViewHelper\View\Helper;

use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\View\Helper;
use JsonPath\InvalidJsonException;
use JsonPath\JsonObject;
use Hurnell\WebpackEncoreViewHelper\View\Helper\Exception\EncoreHelperException;

/**
 * Webpack Encore helper
 */
class EncoreHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'manifest' => WWW_ROOT . 'build' . DS . 'manifest.json',
        'entrypoints' => WWW_ROOT . 'build' . DS . 'entrypoints.json',
        'defaultOptions' => [
            'js' => [
                'block' => 'script'
            ],
            'css' => [
                'block' => 'css'
            ],
        ],
        'silent' => true,
        'configurationKey' => 'EncoreHelper.entries'
    ];
    /**
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * @var array
     */
    private $manifest = [];


    /**
     * @param array $config
     * @throws \Exception
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        if (!Configure::read($this->getConfig('configurationKey'))) {
            Configure::write($this->getConfig('configurationKey'));
        }

        if (false === ($entryPoints = @file_get_contents($this->getConfig('entrypoints')))) {
            $this->handleException('Missing entrypoints file');
        }
        if (false === ($manifest = @file_get_contents($this->getConfig('manifest')))) {
            $this->handleException('Missing manifest file');
        }

        $manifestArray = @array_flip(json_decode($manifest, true));

        if (is_array($manifestArray)) {
            $this->assignManifest($entryPoints, $manifestArray);
        } else {
            $this->handleException('manifest file could not be converted to array');
        }
    }

    /**
     * @param string $name
     * @return string
     * @throws \Exception
     */
    public function load(string $name): string
    {
        if (!isset($this->manifest[$name])) {
            return $this->handleException('Unable to load "' . $name . '"');
        }
        $asset = $this->manifest[$name];
        return $this->writeEntry($asset);
    }

    /**
     * @param array $asset
     * @return string
     * @throws \Exception
     */
    private function writeEntry(?array $asset): string
    {
        $defaultOptions = $this->getConfig('defaultOptions');
        $type = $asset['type'];
        if (!array_key_exists($type, $defaultOptions)) {
            return $this->handleException("Unknown asset type '$type'.");
        }

        $func = reset($defaultOptions[$type]);

        return $this->Html->$func($asset['target']);
    }

    /**
     * @param string $entryPoints
     * @param array $manifestArray
     * @throws \Exception
     */
    private function assignManifest(string $entryPoints, array $manifestArray): void
    {
        $types = array_keys($this->getConfig('defaultOptions'));
        try {
            $jsonObject = new JsonObject($entryPoints);
            foreach ($types as $type) {
                $xpath = sprintf('$..%s.*', $type);
                $targets = $jsonObject->get($xpath);
                if (is_array($targets)) {
                    foreach ($targets as $target) {
                        if (array_key_exists($target, $manifestArray)) {
                            $this->manifest[$manifestArray[$target]] = [
                                'type' => $type,
                                'target' => $target
                            ];
                        }
                    }
                }
            }
        } catch (InvalidJsonException $exception) {
            $this->handleException('Entrypoints contains invalid JSON');
        }
    }

    /**
     * @param string $message
     * @return null|string
     * @throws EncoreHelperException
     */
    private function handleException(string $message): ?string
    {
        if (!$this->getConfig('silent')) {
            throw new EncoreHelperException($message);
        }

        $error = sprintf('"%s" returned the following error: %s', self::class, $message);
        Log::write('error', $error);
        return '';
    }
}
