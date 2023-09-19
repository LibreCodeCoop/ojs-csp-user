<?php

/**
 * @file plugins /generic/cspUser/CspUserPlugin.inc.php
 *
 * Copyright (c) 2020-2023 Lívia Gouvêa
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CspUserPlugin
 * @brief Customizes User profile fields
 */

 namespace APP\plugins\generic\cspUser;

use PKP\plugins\GenericPlugin;
use APP\core\Application;
use PKP\plugins\Hook;
use APP\template\TemplateManager;
use APP\facades\Repo;
use PKP\security\Role;
use PKP\user\form\RegistrationForm;

class CspUserPlugin extends GenericPlugin {


    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path);
        if ($success && $this->getEnabled()) {
            Hook::add('Schema::get::user', [$this, 'addToUserSchema']);
            Hook::add('registrationform::readuservars', [$this, 'registrationFormReadUserVars']);
            Hook::add('registrationform::Constructor', [$this, 'registrationFormConstructor']);
            Hook::add('registrationform::execute', [$this, 'registrationFormExecute']);
            Hook::add('registrationform::display', [$this, 'registrationFormDisplay']);
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



    public function addToUserSchema(string $hookName, array $args)
    {
      $schema = $args[0]; /** @var stdClass */
      $schema->properties->gender = (object) [
          'type' => 'string',
          'multilingual' => false,
          'validation' => ['nullable']
      ];

      $schema->properties->affiliation2 = (object) [
        "type" => "string",
        "multilingual" => false,
        "apiSummary" => true,
        "validation" => ["nullable"]
    ];
    
    $schema->properties->city = (object) [
        'type' => 'string',
        'multilingual' => false,
        'validation' => ['nullable']
    ];

    $schema->properties->region = (object) [
        'type' => 'string',
        'multilingual' => false,
        'validation' => ['nullable']
    ];

    $schema->properties->zipCode = (object) [
        'type' => 'string',
        'multilingual' => false,
        'validation' => ['nullable']
    ];

      return false;
    }


	public function registrationFormConstructor(string $hookName, array $args)
	{
		$form =& $args[0];
        $form->addCheck(new \PKP\form\validation\FormValidatorORCID($form, 'orcid', 'requires', 'user.orcid.orcidInvalid'));
	}

    public function registrationFormDisplay(string $hookName, array $args){
        $form = &$args[0];
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);
        $genders['M'] = __('plugins.themes.csp.user.gender.male');
        $genders['F'] = __('plugins.themes.csp.user.gender.female');
        $genders['NB'] = __('plugins.themes.csp.user.gender.non-binary');
        $genders['NI'] = __('plugins.themes.csp.user.gender.not.inform');

		$templateMgr->assign('genders', $genders);
    }

	public function registrationformReadUserVars(string $hookName, array $args)
	{
		$args[1][] = 'url';
		$args[1][] = 'gender';
		$args[1][] = 'affiliation2';
		$args[1][] = 'mailingAddress';
		$args[1][] = 'city';
		$args[1][] = 'region';
		$args[1][] = 'zipCode';
		$args[1][] = 'orcid';
	}


	public function registrationFormExecute(string $hookName, array $args)
	{
		$form = &$args[0];

		$newUser = $form->user;
		$newUser->setData('url', $form->getData('url'));
		$newUser->setData('gender', $form->getData('gender'));
		$newUser->setData('affiliation2', $form->getData('affiliation2'));
		$newUser->setData('mailingAddress', $form->getData('mailingAddress'));
		$newUser->setData('city', $form->getData('city'));
		$newUser->setData('region', $form->getData('region'));
		$newUser->setData('zipCode', $form->getData('zipCode'));
		$newUser->setData('orcid', $form->getData('orcid'));

        $request = Application::get()->getRequest();
        $reviewerGroup = Repo::userGroup()->getByRoleIds([Role::ROLE_ID_REVIEWER], $request->getContext()->getId(), true)->first()->getId();
        $readerGroup = Repo::userGroup()->getByRoleIds([Role::ROLE_ID_READER], $request->getContext()->getId(), true)->first()->getId();
        $form->setData('reviewerGroup', array($reviewerGroup => $reviewerGroup));
        $form->setData('readerGroup', array($readerGroup => $readerGroup));
	}
}
