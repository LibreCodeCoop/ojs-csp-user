{**
 * templates/frontend/components/registrationForm.tpl
 *
 * Copyright (c) 2014-2023 Simon Fraser University
 * Copyright (c) 2003-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Display the basic registration form fields
 *
 * @uses $locale string Locale key to use in the affiliate field
 * @uses $firstName string First name input entry if available
 * @uses $middleName string Middle name input entry if available
 * @uses $lastName string Last name input entry if available
 * @uses $countries array List of country options
 * @uses $country string The selected country if available
 * @uses $email string Email input entry if available
 * @uses $username string Username input entry if available
 *}
 
<fieldset class="identity">
	<div class="fields">
		<div class="form-group given_name">
			<label>
				{translate key="user.givenName"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="text" name="givenName" id="givenName" value="{$givenName|escape}" maxlength="255" required>
			</label>
		</div>
		<div class="form-group family_name">
			<label>
				{translate key="user.familyName"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="text" name="familyName" id="familyName" value="{$familyName|escape}" maxlength="255" required>
			</label>
		</div>
		<div class="form-group gender">
			<label>
				<div id="fr">
					{translate key="plugins.themes.csp.user.gender"}
					<span class="form-control-required">*</span>
					<span class="sr-only">{translate key="common.required"}</span>
				</div>
				<select name="gender" id="gender" class="form-control" required>
					<option value="">{translate key="common.chooseOne"}</option>
					{html_options options=$genders selected=$gender}
				</select>
			</label>
		</div>
		<div class="form-group">
			<label>
				{translate key="user.orcid"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="text" name="orcid" id="orcid" value="{$orcid|escape}" maxlength="255" required>
			</label>
		</div>
		<div class="form-group url">
			<label>
				{translate key="user.url"}
				<input class="form-control" type="text" name="url" id="url" value="{$url|escape}" maxlength="255">
			</label>
		</div>
		<div class="form-group affiliation">
			<label>
				{translate key="user.affiliation"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="text" name="affiliation" autocomplete="organization" id="affiliation" value="{$affiliation|default:""|escape}" required aria-required="true">
			</label>
		</div>
		<div class="form-group affiliation2">
			<label>
				{translate key="user.affiliation"}2
				<input class="form-control" type="text" name="affiliation2" id="affiliation2" value="{$affiliation2|escape}">
			</label>
		</div>
		<div class="form-group country">
			<label>
			<div id="fr">
				{translate key="common.country"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
			</div>
				<select class="form-control" name="country" id="country" required>
					<option value="">{translate key="common.chooseOne"}</option>
					{html_options options=$countries selected=$country}
				</select>
			</label>
		</div>
		<div class="form-group region">
			<label>
				{translate key="plugins.themes.csp.user.region"}
				<input class="form-control" type="text" name="region" id="region" value="{$region|escape}" maxlength="255">
			</label>
		</div>
		<div class="form-group city">
			<label>
				{translate key="stats.city"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="text" name="city" id="city" value="{$city|escape}" maxlength="255" required>
			</label>
		</div>
		<div class="form-group mailingAddress">
			<label>
				{translate key="user.mailingAddress"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="text" name="mailingAddress" id="mailingAddress" value="{$mailingAddress|escape}" maxlength="255" required>
			</label>
		</div>
		<div class="form-group zipCode">
			<label>
				{translate key="plugins.themes.csp.user.zip.code"}
				<input class="form-control" type="text" name="zipCode" id="zipCode" value="{$zipCode|escape}" maxlength="255">
			</label>
		</div>
	</div>
</fieldset>

<fieldset class="login">
	<div class="fields">
		<div class="form-group email">
			<label>
				{translate key="user.email"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="email" name="email" id="email" value="{$email|escape}" maxlength="90" required>
			</label>
		</div>
		<div class="form-group username">
			<label>
				{translate key="user.username"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="text" name="username" id="username" value="{$username|escape}" maxlength="32" required>
			</label>
		</div>
		<div class="form-group password">
			<label>
				{translate key="user.password"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="password" name="password" id="password" password="true" maxlength="32" required>
			</label>
		</div>
		<div class="form-group password">
			<label>
				{translate key="user.repeatPassword"}
				<span class="form-control-required">*</span>
				<span class="sr-only">{translate key="common.required"}</span>
				<input class="form-control" type="password" name="password2" id="password2" password="true" maxlength="32" required>
			</label>
		</div>
	</div>
</fieldset>
