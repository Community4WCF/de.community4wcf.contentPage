{include file="documentHeader"}
<head>
	<title>{lang}wcf.content.page.page{@$action|ucfirst}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	{capture append='userMessages'}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
	
		{if $preview|isset}
			<div class="border messagePreview">
				<div class="containerHead">
					<h3>{lang}wcf.message.preview{/lang}</h3>
				</div>
				<div class="message content">
					<div class="messageInner container-1">
						{if $subject}
							<h4>{$subject}</h4>
						{/if}
						<div class="messageBody">
							<div>{@$preview}</div>
						</div>
					</div>
				</div>
			</div>
		{/if}
	{/capture}

	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		{if $pageID}
			<li><a href="index.php?page=ContentPage&amp;pageID={@$pageID}{@SID_ARG_2ND}"><img src="{icon}{@$page->icon}S.png{/icon}" alt="" /> <span>{lang}wcf.content.page.page-{@$pageID}{/lang}</span></a> &raquo;</li>
		{/if}
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}contentPage{@$action|ucfirst}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.content.page.page{@$action|ucfirst}{/lang}</h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	<form enctype="multipart/form-data" method="post" action="{if $action == 'add'}index.php?form=ContentPageAdd{else}index.php?form=ContentPageEdit&amp;pageID={@$pageID}{/if}">

	<div class="border content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.content.page.page{@$action|ucfirst}{/lang}</h3>
				
			<fieldset>
				<legend>{lang}wcf.content.page.pageAdd.general{/lang}</legend>
				
				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wcf.user.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID"  onchange="location.href='index.php?form=ContentPageEdit&amp;pageID={@$pageID}&amp;languageID=' + this.value + '{@SID_ARG_2ND}'">
								{foreach from=$languages key=key item=language}
									<option value="{@$key}"{if $key == $languageID} selected="selected"{/if}>
										{lang}wcf.global.language.{@$language}{/lang}
									</option>
								{/foreach}						
							</select>
						</div>
					</div>
				{/if}
					
				<div class="formElement{if $errorField == 'subject'} formError{/if}">
					<div class="formFieldLabel">
						<label for="subject">{lang}wcf.content.page.pageAdd.subject{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="subject" id="subject" value="{$subject}" tabindex="{counter name='tabindex'}" />
						{if $errorField == 'subject'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
				
				<div class="formElement{if $errorField == 'icon'} formError{/if}">
					<div class="formFieldLabel">
						<label for="icon">{lang}wcf.content.page.pageAdd.icon{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="icon" id="icon" value="{$icon}" tabindex="{counter name='tabindex'}" />
						{if $errorField == 'icon'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc">
						{lang}wcf.content.page.pageAdd.icon.description{/lang}
					</div>
				</div>
				
				<div class="formElement{if $errorField == 'shortDescription'} formError{/if}">
					<div class="formFieldLabel">
						<label for="shortDescription">{lang}wcf.content.page.pageAdd.shortDescription{/lang}</label>
					</div>			
					<div class="formField">
						<textarea id="shortDescription" name="shortDescription" rows="5" cols="40" tabindex="{counter name='tabindex'}">{$shortDescription}</textarea>
						{if $errorField == 'shortDescription'}
							<p class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.error.empty{/lang}
								{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc">
						{lang}wcf.content.page.pageAdd.shortDescription.description{/lang}
					</div>
				</div>
				
				<div class="formGroup{if $errorField == 'canSeeGroupIDs'} formError{/if}">
						<div class="formFieldLabel">
							<label for="canSeeGroupIDs">{lang}wcf.content.page.pageAdd.canSeeGroupIDs{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<div class="formField">
									{htmlCheckboxes options=$availableGroups name=canSeeGroupIDs selected=$canSeeGroupIDs}
									{if $errorField == 'canSeeGroupIDs'}
										<p class="innerError">
											{if $errorType == 'empty'}{lang}wcf.content.page.pageAdd.canSeeGroupIDs.empty{/lang}{/if}
										</p>
									{/if}
								</div>
								<div class="formFieldDesc">
									<p>{lang}wcf.content.page.pageAdd.canSeeGroupIDs{/lang}</p>
								</div>
							</fieldset>
						</div>
					</div>
			</fieldset>
				
			<fieldset>
				<legend>{lang}wcf.content.page.pageAdd.message{/lang}</legend>
				
				<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
					<div class="formFieldLabel">
						<label for="text">{lang}wcf.content.page.pageAdd.message{/lang}</label>
					</div>
						
					<div class="formField">				
						<textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
						{if $errorField == 'text'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType == 'tooLong'}{lang}wcf.message.error.tooLong{/lang}{/if}
								{if $errorType == 'censoredWordsFound'}{lang}wcf.message.error.censoredWordsFound{/lang}{/if}
							</p>
						{/if}
					</div>

				</div>
					
				{include file='messageFormTabs'}
			</fieldset>
			
			{if $additionalFields|isset}{@$additionalFields}{/if}
		
			<div class="formSubmit">
				<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
				<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
				<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
				{@SID_INPUT_TAG}
				<input type="hidden" name="idHash" value="{$idHash}" />
			</div>
		</div>
	</div>
	</form>
</div>

{include file='footer' sandbox=false}
</body>
</html>