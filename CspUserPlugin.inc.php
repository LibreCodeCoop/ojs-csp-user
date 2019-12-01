<?php
import('lib.pkp.classes.plugins.GenericPlugin');

class CspUserPlugin extends GenericPlugin
{

    public function register($category, $path, $mainContextId = NULL)
    {

        // Register the plugin even when it is not enabled
        $success = parent::register($category, $path);
        if (! Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE'))
            return true;
        if ($success && $this->getEnabled($mainContextId)) {
            HookRegistry::register('userdao::getAdditionalFieldNames', array($this, 'handleAdditionalFieldNames'));
            HookRegistry::register('User::getProperties::summaryProperties', array(
                $this,
                'getSummaryProperties'
            ));
            // Do something when the plugin is enabled
        }

        return $success;
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDisplayName()
    {
        return 'LyseonTech Users';
    }

    /**
     * Provide a description for this plugin
     *
     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription()
    {
        return 'Return extended user settings when get user properties.';
    }

    function getSummaryProperties($hookName, $params)
    {
        return false;
    }

    public function handleAdditionalFieldNames($hookName, $params) {
        $fields =& $params[1];
        $fields[] = 'phone';
        $fields[] = 'lattes';
        $fields[] = 'sexo';
        $fields[] = 'observacao';
        $fields[] = 'instituicao1';
        $fields[] = 'instituicao2';
        $fields[] = 'endereco';
        $fields[] = 'cidade';
        $fields[] = 'estado';
        $fields[] = 'cep';
        $fields[] = 'pais';
        $fields[] = 'palavraChave';

        return false;
    }
}
