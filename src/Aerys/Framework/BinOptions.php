<?php

namespace Aerys\Framework;

class BinOptions {

    private $help;
    private $config;
    private $workers;
    private $port;
    private $ip;
    private $name;
    private $root;
    private $shortOpts = 'hc:w:p:i:n:r:';
    private $longOpts = [
        'help',
        'config:',
        'workers:',
        'port:',
        'ip:',
        'name:',
        'root:'
    ];
    private $shortOptNameMap = [
        'h' => 'help',
        'c' => 'config',
        'w' => 'workers',
        'p' => 'port',
        'i' => 'ip',
        'n' => 'name',
        'r' => 'root'
    ];

    /**
     * Load command line options that may be used to bootstrap a server
     * 
     * @param array $options Used if defined, loaded from the CLI otherwise
     * @throws \Aerys\Framework\ConfigException
     * @return \Aerys\Framework\BinOptions Returns the current object instance
     */
    function loadOptions(array $options = NULL) {
        $rawOptions = isset($options) ? $options : $this->getCommandLineOptions();

        $normalizedOptions = [
            'help' => NULL,
            'config' => NULL,
            'workers' => NULL,
            'port' => NULL,
            'ip' => NULL,
            'name' => NULL,
            'root' => NULL
        ];

        foreach ($rawOptions as $key => $value) {
            if (isset($this->shortOptNameMap[$key])) {
                $normalizedOptions[$this->shortOptNameMap[$key]] = $value;
            } else {
                $normalizedOptions[$key] = $value;
            }
        }

        $this->setOptionValues($normalizedOptions);
        
        if (!($this->help || $this->config || $this->root)) {
            throw new ConfigException(
                'App config file (-c, --config) or document root directory (-r, --root) required'
            );
        }
        
        return $this;
    }
    
    private function getCommandLineOptions() {
        return getopt($this->shortOpts, $this->longOpts);
    }

    private function setOptionValues(array $options) {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'help':
                    $this->help = isset($value) ? TRUE : NULL;
                    break;
                case 'config':
                    $this->config = $value;
                    break;
                case 'workers':
                    $this->setWorkers($value);
                    break;
                case 'port':
                    $this->port = $value;
                    break;
                case 'ip':
                    $this->ip = $value;
                    break;
                case 'name':
                    $this->name = $value;
                    break;
                case 'root':
                    $this->root = $value;
                    break;
            }
        }
    }

    private function setWorkers($count) {
        $this->workers = filter_var($count, FILTER_VALIDATE_INT, ['options' => [
            'default' => 0,
            'min_range' => 1
        ]]);
    }

    function getHelp() {
        return $this->help;
    }

    function getConfig() {
        return $this->config;
    }

    function getWorkers() {
        return $this->workers;
    }

    function getPort() {
        return $this->port;
    }

    function getIp() {
        return $this->ip;
    }

    function getName() {
        return $this->name;
    }

    function getRoot() {
        return $this->root;
    }

}