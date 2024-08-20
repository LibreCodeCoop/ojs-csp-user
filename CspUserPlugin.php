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
use PKP\user\form\IdentityForm;
use PKP\services\PKPSchemaService;
use APP\core\Services;
use PKP\core\Core;
use PKP\security\Validation;
use PKP\session\SessionManager;
use Illuminate\Support\Facades\DB;
use PKP\facades\Locale;

class CspUserPlugin extends GenericPlugin {


    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled()) {

            Hook::add('Schema::get::user', [$this, 'addToUserSchema']);
            Services::get('schema')->get(PKPSchemaService::SCHEMA_USER, true);

            Hook::add('registrationform::readuservars', [$this, 'registrationFormReadUserVars']);
            Hook::add('registrationform::Constructor', [$this, 'registrationFormConstructor']);
            Hook::add('registrationform::execute', [$this, 'registrationFormExecute']);
            Hook::add('registrationform::display', [$this, 'registrationFormDisplay']);

            Hook::add('contactform::display', [$this, 'contactFormDisplay']);
            Hook::add('contactform::readuservars', [$this, 'contactFormReaduservars']);
            Hook::add('contactform::execute', [$this, 'contactFormExecute']);

            Hook::add('identityform::display', array($this, 'identityFormDisplay'));
            Hook::add('identityform::readuservars', array($this, 'identityFormReaduservars'));
            Hook::add('identityform::execute', array($this, 'identityFormExecute'));

            Hook::add('TemplateResource::getFilename', [$this, '_overridePluginTemplates']);
            Hook::add('LoadHandler', [$this, 'loadHandler']);
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
            "apiSummary" => true,
            'validation' => ['nullable']
        ];

        $schema->properties->breed = (object) [
            'type' => 'string',
            'multilingual' => false,
            "apiSummary" => true,
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
            "apiSummary" => true,
            'validation' => ['nullable']
        ];

        $schema->properties->region = (object) [
            'type' => 'string',
            'multilingual' => false,
            "apiSummary" => true,
            'validation' => ['nullable']
        ];

        $schema->properties->zipCode = (object) [
            'type' => 'string',
            'multilingual' => false,
            "apiSummary" => true,
            'validation' => ['nullable']
        ];

        return false;
    }


	public function registrationFormConstructor(string $hookName, array $args)
	{
		$form =& $args[0];
        $form->addCheck(new \PKP\form\validation\FormValidatorORCID($form, 'orcid', 'required', 'user.orcid.orcidInvalid'));
	}

    public function registrationFormDisplay(string $hookName, array $args){
        $form = &$args[0];
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);
        $genders['M'] = __('plugins.themes.csp.user.gender.male');
        $genders['F'] = __('plugins.themes.csp.user.gender.female');
        $genders['NB'] = __('plugins.themes.csp.user.gender.non-binary');
        $genders['NI'] = __('plugins.themes.csp.user.gender.not.inform');

        $breeds['amarela'] = __('plugins.themes.csp.user.breed.amarela');
        $breeds['branca'] = __('plugins.themes.csp.user.breed.branca');
        $breeds['indigena'] = __('plugins.themes.csp.user.breed.indigena');
        $breeds['parda'] = __('plugins.themes.csp.user.breed.parda');
        $breeds['preta'] = __('plugins.themes.csp.user.breed.preta');

		$templateMgr->assign('genders', $genders);
        $templateMgr->assign('breeds', $breeds);
    }

	public function registrationformReadUserVars(string $hookName, array $args)
	{
		$args[1][] = 'url';
		$args[1][] = 'gender';
        $args[1][] = 'breed';
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
        $newUser->setData('breed', $form->getData('breed'));
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


	public function contactFormDisplay(string $hookName, array $args){
        $user = Repo::user()->get($args[0]->_user->getData('id'), true);

		$args[0]->_data["affiliation2"] = $user->getData('affiliation2');
		$args[0]->_data["city"] = $user->getData('city');
		$args[0]->_data["region"] = $user->getData('region');
		$args[0]->_data["zipCode"] = $user->getData('zipCode');
	}

	public function contactFormReaduservars(string $hookName, array $args){
		$args[1][] = 'affiliation2';
		$args[1][] = 'city';
		$args[1][] = 'region';
		$args[1][] = 'zipCode';
	}

	public function contactFormExecute(string $hookName, array $args){
		$form = &$args[0];

		$editUser = $form->_user;
		$editUser->setData('affiliation2', $form->getData('affiliation2'));
		$editUser->setData('city', $form->getData('city'));
		$editUser->setData('region', $form->getData('region'));
		$editUser->setData('zipCode', $form->getData('zipCode'));
        $user = Repo::user()->get($args[0]->_user->getData('id'), true);
        $editUser->setData('gender', $user->getData('gender'));
        $editUser->setData('breed', $user->getData('breed'));
	}

	public function identityFormDisplay(string $hookName, array $args){
        $form = &$args[0];
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);

        $genders['M'] = __('plugins.themes.csp.user.gender.male');
        $genders['F'] = __('plugins.themes.csp.user.gender.female');
        $genders['NB'] = __('plugins.themes.csp.user.gender.non-binary');
        $genders['NI'] = __('plugins.themes.csp.user.gender.not.inform');

        $breeds['amarela'] = __('plugins.themes.csp.user.breed.amarela');
        $breeds['branca'] = __('plugins.themes.csp.user.breed.branca');
        $breeds['indigena'] = __('plugins.themes.csp.user.breed.indigena');
        $breeds['parda'] = __('plugins.themes.csp.user.breed.parda');
        $breeds['preta'] = __('plugins.themes.csp.user.breed.preta');

        $user = Repo::user()->get($args[0]->_user->getData('id'), true);
        $templateMgr->assign([
            'genders' => $genders,
            'gender' => $user->getData('gender'),
            'breeds' => $breeds,
            'breed' => $user->getData('breed'),
        ]);
	}

	public function identityFormReaduservars(string $hookName, array $args){
		$args[1][] = 'gender';
        $args[1][] = 'breed';
	}

	public function identityFormExecute(string $hookName, array $args)
	{
		$form = &$args[0];

		$editUser = $form->_user;
		$editUser->setData('gender', $form->getData('gender'));
        $editUser->setData('breed', $form->getData('breed'));
        $user = Repo::user()->get($args[0]->_user->getData('id'), true);
		$editUser->setData('affiliation2', $user->getData('affiliation2'));
		$editUser->setData('city', $user->getData('city'));
		$editUser->setData('region', $user->getData('region'));
		$editUser->setData('zipCode', $user->getData('zipCode'));
	}

    // Integração de login Sagas com OJS
    public function loadHandler($hookName, $args){
        if( $args[0] == "login" && $args[1] == "signIn"){
            $request = Application::get()->getRequest();
            $row = DB::table('csp.Login as l')
            ->leftJoin('ojs.users as ou', 'ou.username', '=', 'l.login')
            ->join('csp.Pessoa as p', 'l.idPessoaFK', '=', 'p.idPessoa')
            ->where('login','=', $request->getUserVar('username'))
            ->where('l.senha', '=', sha1($request->getUserVar('password')))
            ->whereNull('ou.user_id')
            ->get([
                'login AS username',
                'p.email',
                'p.telefone AS phone',
                'p.pais AS country',
                'p.orcid AS orcid',
                'p.nome AS givenName',
                'p.idioma',
                'p.lattes',
                'p.sexo',
                'p.observacao',
                'p.instituicao1',
                'p.instituicao2',
                'p.endereco',
                'p.cidade',
                'p.estado',
                'p.cep'
            ])
            ->first();

            if($row){
                $user = Repo::user()->newDataObject();
                $currentLocale = Locale::getLocale();

                $user->setUsername($row->username);

                $user->setGivenName($row->givenName, $currentLocale);
                $user->setEmail($row->email);
                $user->setCountry($row->country);
                $user->setAffiliation($row->instituicao1, $currentLocale);

                $site = $request->getSite();
                $sitePrimaryLocale = $site->getPrimaryLocale();

                if ($sitePrimaryLocale != $currentLocale) {
                    $user->setGivenName($row->givenName, $sitePrimaryLocale);
                    $user->setAffiliation($row->instituicao1, $sitePrimaryLocale);
                }
                $user->setDateRegistered(Core::getCurrentDate());
                $user->setInlineHelp(1); // default new users to having inline help visible.
                $user->setPassword(Validation::encryptCredentials($row->username, $request->_requestVars["password"]));

                Repo::user()->add($user);
                $userId = $user->getId();
                if (!$userId) {
                    return false;
                }
                // Associate the new user with the existing session
                $sessionManager = SessionManager::getManager();
                $session = $sessionManager->getUserSession();
                $session->setSessionVar('username', $user->getUsername());

                $defaultReaderGroup = Repo::userGroup()->getByRoleIds([Role::ROLE_ID_READER], $request->getContext()->getId(), true)->first();
                $reviewerGroup = Repo::userGroup()->getByRoleIds([Role::ROLE_ID_REVIEWER], $request->getContext()->getId(), true)->first();
                Repo::userGroup()->assignUserToGroup($user->getId(), $defaultReaderGroup->getId(), $request->getContext()->getId());
                Repo::userGroup()->assignUserToGroup($user->getId(), $reviewerGroup->getId(), $request->getContext()->getId());

                $basePath = $request->getBasePath();
                $contextPath = $request->getContext()->getPath();
                $basePath = $request->getBasePath();
                $request->_requestVars["source"] = $basePath.'/index.php/'.$contextPath."/user/profile";
            }
        }
    }

}
