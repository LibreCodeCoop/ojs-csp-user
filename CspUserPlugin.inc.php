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
        return __('plugins.generic.cspUser.displayName');
    }

    /**
     * Provide a description for this plugin
     *
     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription()
    {
        return __('plugins.generic.cspUser.description');
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
